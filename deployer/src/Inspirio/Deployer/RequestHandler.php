<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Config\ConfigAware;
use Inspirio\Deployer\Module\ActionModuleInterface;
use Inspirio\Deployer\Module\Info\InfoModule;
use Inspirio\Deployer\Security\SecurityInterface;
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
    const CONFIG_FILE = 'deployer.yml';

    /**
     * @var string
     */
    private $deployerDir;

    /**
     * @var ApplicationInterface
     */
    private $app;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SecurityInterface[]
     */
    private $security;

    /**
     * @var ActionModuleInterface[]
     */
    private $modules;

    /**
     * Constructor.
     *
     * @param string               $deployerDir
     * @param ApplicationInterface $app
     * @param ActionModuleInterface[]    $modules
     * @param SecurityInterface[]  $security
     */
    public function __construct($deployerDir, ApplicationInterface $app, array $modules, array $security = array())
    {
        $this->deployerDir = $deployerDir;
        $this->app         = $app;
        $this->config      = new Config(self::CONFIG_FILE);
        $this->security    = $security;
        $this->modules     = array();

        foreach ($modules as $module) {
            $this->modules[$module->getName()] = $module;
            $module->setProjectDir($this->app->getRootPath());

            if ($module instanceof ConfigAware) {
                $module->setConfig($this->config);
            }
        }
    }

    /**
     * Return real file-path (when file exists in project).
     *
     * @param string $fileName
     * @return string|null
     */
    public function findFile($fileName)
    {
        return realpath($this->app->getRootPath() .'/'. $fileName) ?: null;
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

        $isPost = $request->isMethod('post');
        $isAjax = $request->isXmlHttpRequest();

        $moduleName = $request->query->get('module');
        $module     = $this->findModule($moduleName);

        if (!$module) {
            return new Response('404 Not Found', 404, array(
                'Location' => '?'
            ));
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

        $content = $this->renderModule($module, $request);

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
     * Returns module instance.
     *
     * @param string $moduleName
     * @return ActionModuleInterface|null
     */
    private function findModule($moduleName)
    {
        if ($moduleName && isset($this->modules[$moduleName])) {
            $module = $this->modules[$moduleName];

        } else {
            $module = reset($this->modules);
        }

        if (!$module->isEnabled()) {
            $module = null;

            foreach ($this->modules as $try) {
                if ($try->isEnabled()) {
                    $module = $try;
                    break;
                }
            }
        }

        return $module;
    }

    /**
     * Renders the action page.
     *
     * @param ActionModuleInterface $activeModule
     * @param Request         $request
     * @return string
     */
	private function renderModule(ActionModuleInterface $activeModule, Request $request)
	{
        $app     = $this->app;
        $project = $app->getProjectInfo();
        $modules = $this->modules;

		ob_start();
		require $this->deployerDir .'/view/index.html.php';
		return ob_get_clean();
	}
}
