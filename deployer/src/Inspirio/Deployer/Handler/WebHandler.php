<?php
namespace Inspirio\Deployer\Handler;

use Inspirio\Deployer\Config\ConfigAware;
use Inspirio\Deployer\Module\ModuleInterface;
use Inspirio\Deployer\Module\Info\InfoModule;
use Inspirio\Deployer\Security\SecurityInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Web request handler.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
class WebHandler extends Handler
{
    /**
     * @var SecurityInterface[]
     */
    private $security;

	/**
	 * {@inheritdoc}
	 */
	public function __construct($projectDir, array $modules, array $security = array())
	{
		parent::__construct($projectDir, $modules);
        $this->security = $security;
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
     * Renders the action page.
     *
     * @param ModuleInterface $activeModule
     * @param Request         $request
     * @return string
     */
	private function renderModule(ModuleInterface $activeModule, Request $request)
	{
        $modules = $this->modules;

		if ($composerJson = $this->findFile('composer.json')) {
			$project = json_decode(file_get_contents($composerJson), true);
		} else {
			$project = array(
				'name'    => 'unknown',
				'version' => '0.0',
			);
		}

        $project['rootDir'] = realpath($this->projectDir);

		ob_start();
		require __DIR__ . '/view/index.html.php';
		return ob_get_clean();
	}
}
