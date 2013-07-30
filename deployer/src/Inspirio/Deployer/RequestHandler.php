<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Config\ConfigAware;
use Inspirio\Deployer\Module\ActionModuleInterface;
use Inspirio\Deployer\Module\Info\InfoModule;
use Inspirio\Deployer\Security\SecurityInterface;
use Inspirio\Deployer\View\View;
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
     * @var SecurityInterface[]
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
     * @param SecurityInterface $security
     * @return $this
     */
    public function addSecurity(SecurityInterface $security)
    {
        $this->security[] = $security;
        return $this;
    }


    /**
     * Runs the action.
     *
     * @param ActionModuleInterface $module
     * @param string          $action
     * @param array           $args
     * @return Response
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function runAction(ActionModuleInterface $module, $action, array $args)
    {
        $actionRefl = null;

        try {
            $actionRefl = new \ReflectionMethod(get_class($module), $action .'Action');
        } catch (\ReflectionException $e) {}

        if (!$actionRefl || !$actionRefl->isPublic()) {
            throw new \LogicException("Module '{$module->getName()}' has no action '{$action}'");
        }

        $realArgs = array();

        foreach ($actionRefl->getParameters() as $i => $param) {
            $name = $param->getName();

            if (array_key_exists($name, $args)) {
                $realArgs[$i] = $args[$name];

            } elseif ($param->isOptional()) {
                $realArgs[$i] = $param->getDefaultValue();

            } else {
                throw new \InvalidArgumentException("Action '{$module->getName()}' is missing  '{$name}' parameter value");
            }
        }

        $response = new StreamedResponse(function() use ($actionRefl, $module, $realArgs) {
            $actionRefl->invokeArgs($module, $realArgs);
        });

        return $response;
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
        if ($response = $this->checkSecurity($request)) {
            return $response;
        }

        if ($response = $this->startupApp($request)) {
            return $response;
        }

        $isPost = $request->isMethod('post');
        $isAjax = $request->isXmlHttpRequest();

        $moduleName    = $request->query->get('module', $this->app->getHomeModuleName());
        $requestModule = null;

        foreach($this->app->getModules() as $module) {
            if ($module instanceof ConfigAware) {
                $module->setConfig($this->config);
            }

            if (!$module->isEnabled()) {
                continue;
            }

            if ($module->getName() == $moduleName) {
                $request->attributes->set('module', $module);
            }
        }

        $module = $request->attributes->get('module');

        if (!$module) {
            return new Response('404 Not Found', 404);
        }

        if ($isPost) {
            if ($isAjax) {
                $action = $request->request->get('run', null);

                if ($action === null) {
                    throw new \LogicException("No action specified (missing 'run' argument)");
                }

                $args = $request->request->all();
                unset($args['run']);

                return $this->runAction($module, $action, $args);
            }

            throw new \Exception('Handling of non-ajax POST requests is not implemented yet');

//            $params = $module->handlePost($_POST);
//
//            if (is_array($params)) {
//                if (!array_key_exists('action', $params)) {
//                    $params['action'] = $moduleName;
//                }
//
//                header('HTTP/1.1 303 See Other');
//                header('Location: ?'. http_build_query($params));
//            }
        }

        $this->view['app']    = $this->app;
        $this->view['module'] = $module;

        $content = $module->render($request, $this->view);

        return new Response($content);
    }

    /**
     * Checks security rules.
     *
     * @param Request $request
     * @return null|Response
     */
    private function checkSecurity(Request $request)
    {
        foreach ($this->security as $security) {
            if ($security instanceof ConfigAware) {
                $security->setConfig($this->config);
            }

            $response = $security->authorize($request);

            if ($response instanceof Response) {
                return $response;
            }

            if (!$response) {
                return new Response('403 Forbidden', 403);
            }
        }

        return null;
    }

    /**
     * Checks if application is started-up and shows start-up screen if not.
     *
     * @param Request $request
     * @return null|Response
     */
    private function startupApp(Request $request)
    {
        foreach ($this->app->getStarters() as $starter) {
            if ($starter instanceof ConfigAware) {
                $starter->setConfig($this->config);
            }

            $response = $starter->startupApp($request);

            if ($response instanceof Response) {
                return $response;
            }
        }

        return null;
    }
}
