<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Config\ConfigAware;
use Inspirio\Deployer\Exception\Request\NotAuthenticatedException;
use Inspirio\Deployer\Exception\Request\NotStartedException;
use Inspirio\Deployer\Module\ActionModuleInterface;
use Inspirio\Deployer\Module\Info\InfoModule;
use Inspirio\Deployer\Security\SecurityModuleInterface;
use Inspirio\Deployer\Starter\StarterModuleInterface;
use Inspirio\Deployer\View\View;
use Inspirio\Deployer\View\ViewAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Web request handler.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
class RequestHandler
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var View
     */
    private $view;

    /**
     * @var ApplicationInterface
     */
    private $app;

    /**
     * @var SecurityModuleInterface[]
     */
    private $security;

    /**
     * Constructor.
     *
     * @param Config               $config
     * @param View                 $view
     * @param ApplicationInterface $app
     */
    public function __construct(Config $config, View $view, ApplicationInterface $app)
    {
        $this->config   = $config;
        $this->view     = $view;
        $this->app      = $app;
        $this->security = array();
    }

    /**
     * Registers security module.
     *
     * @param SecurityModuleInterface $security
     * @return $this
     */
    public function addSecurity(SecurityModuleInterface $security)
    {
        $this->security[] = $security;
        return $this;
    }

	/**
	 * Handles the HTTP request.
	 *
	 * @return string
	 */
	public function dispatch()
	{
        $request = Request::createFromGlobals();

        $session = new Session();
        $request->setSession($session);

        $response = $this->handleRequest($request);
        $response->send();
	}

    /**
     * Handles request.
     *
     * @param Request $request
     * @return Response
     *
     * @throws \LogicException
     * @throws \Exception
     */
    private function handleRequest(Request $request)
    {
        if (
            !$request->isMethod('get') &&
            !($request->isMethod('post') && $request->query->has('module'))
        ) {
            return new Response('405 Method Not Allowed', 405);
        }

        if ($request->isMethod('post') && !$request->isXmlHttpRequest()) {
            throw new \Exception('Handling of non-ajax POST requests is not implemented yet');
        }

        $module = $this->pickModule($request);

        if (!$module) {
            return new Response('404 Not Found', 404);
        }

        $request->attributes->set('module', $module);

        if ($request->isMethod('post')) {
            return $this->runModuleAction($module, $request);

        } else {
            return $this->renderModule($module, $request);
        }
    }

    /**
     * Finds module to launch.
     *
     * @param Request $request
     * @return ModuleInterface|null
     *
     * @throws Exception\Request\NotAuthenticatedException
     * @throws Exception\Request\NotStartedException
     */
    private function pickModule(Request $request)
    {
        $moduleName = $request->query->get('module');

        $module = $this->checkSecurity($request);

        if ($module) {
            if ($moduleName !== null && $module->getName() !== $moduleName) {
                throw new NotAuthenticatedException();
            }

            return $module;
        }

        $module = $this->checkStarters();

        if ($module) {
            if ($moduleName !== null && $module->getName() !== $moduleName) {
                throw new NotStartedException();
            }

            return $module;
        }

        return $this->pickActionModule($moduleName);
    }

    /**
     * Picks a security module that is not authorized yet.
     *
     * @param Request $request
     * @return SecurityModuleInterface|null
     */
    private function checkSecurity(Request $request)
    {
        foreach ($this->security as $module) {
            $this->initModule($module);

            if (!$module->isAuthorized($request)) {
                return $module;
            }
        }

        return null;
    }

    /**
     * Picks a starter module that is not started yet.
     *
     * @return StarterModuleInterface|null
     */
    private function checkStarters()
    {
        foreach ($this->app->getStarters() as $module) {
            $this->initModule($module);

            if (!$module->isStarted()) {
                return $module;
            }
        }

        return null;
    }

    /**
     * Picks an default action module.
     *
     * @param string|null $moduleName
     * @return ActionModuleInterface|null
     */
    private function pickActionModule($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->app->getHomeModuleName();
        }

        foreach ($this->app->getModules() as $module) {
            $this->initModule($module);

            if (!$module->isEnabled()) {
                continue;
            }

            if ($module->getName() == $moduleName) {
                return $module;
            }
        }

        return null;
    }

    /**
     * Initializes module.
     *
     * @param ModuleInterface $module
     */
    private function initModule(ModuleInterface $module)
    {
        if ($module instanceof ConfigAware) {
            $module->setConfig($this->config);
        }

        if ($module instanceof ViewAware) {
            $module->setView($this->view);
        }
    }

    /**
     * Renders module.
     *
     * @param ModuleInterface $module
     * @param Request $request
     * @return Response
     */
    private function renderModule(ModuleInterface $module, Request $request)
    {
        $response = $module->render($request);

        // complete response returned
        if ($response instanceof Response) {
            return $response;
        }

        // rendered content returned
        if (is_scalar($response)) {
            return new Response($response);
        }

        // view data returned
        $view = $this->view;

        $view->setDefaultData(
            array(
                'app'     => $this->app,
                'module'  => $this,
                'request' => $request,
            )
        );

        $view->pushDecorator('page.html.php');
        $view->pushDecorator('starter/decorator.html.php');

        $className = get_class($module);
        $className = substr($className, strrpos($className, '\\') + 1);
        $template  = 'starter/' . lcfirst($className) . '.html.php';

        $response = $this->view->render($template, $response);
        return new Response($response);
    }

    /**
     * Runs module action.
     *
     * @param ModuleInterface $module
     * @param Request $request
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @return StreamedResponse
     */
    private function runModuleAction(ModuleInterface $module, Request $request)
    {
        if (!$request->request->has('run')) {
            throw new \InvalidArgumentException("Missing 'run' parameter data");
        }

        $action = $request->request->get('run');
        $request->request->remove('run');

        $actionMethod = null;

        try {
            $actionMethod = new \ReflectionMethod($module, $action . 'Action');
        } catch (\ReflectionException $e) {
        }

        if (!$actionMethod || !$actionMethod->isPublic()) {
            $moduleName = get_class($module);
            throw new \LogicException("Starter module '{$moduleName}' is missing 'startup' method");
        }

        $args     = $request->request->all();
        $realArgs = array();

        foreach ($actionMethod->getParameters() as $i => $param) {
            $name = $param->getName();

            if (array_key_exists($name, $args)) {
                $realArgs[$i] = $args[$name];

            } elseif ($param->isOptional()) {
                $realArgs[$i] = $param->getDefaultValue();

            } else {
                $moduleName = get_class($this);
                throw new \InvalidArgumentException("Missing '{$moduleName}' starter module 'startup' method '{$name}' parameter value");
            }
        }

        return new StreamedResponse(function () use ($actionMethod, $module, $realArgs) {
            $actionMethod->invokeArgs($module, $realArgs);
        });
    }
}
