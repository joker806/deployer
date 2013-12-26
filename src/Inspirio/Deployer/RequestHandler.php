<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Middleware\MiddlewareInterface;
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
     * @var MiddlewareInterface[]
     */
    private $middlewares = array();

    /**
     * Registers security module.
     *
     * @param MiddlewareInterface $middleware
     * @return $this
     */
    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->middlewares[] = $middleware;
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

        $moduleName = $request->query->get('module');

        foreach ($this->middlewares as $middleware) {
            $result = $middleware->interceptRequest($request, $moduleName);

            if ($result === null) {
                continue;
            }

            if ($result instanceof Response) {
                return $result;
            }

            if ($result instanceof ModuleInterface) {
                $request->attributes->set('module', $result);

                if ($request->isMethod('post')) {
                    return $this->runModuleAction($result, $request);

                } else {
                    return $this->renderModule($result, $request);
                }
            }

            $hint = is_object($result) ? get_class($result) : gettype($result);
            throw new \LogicException(
                "Invalid middleware result type. " .
                "Expected instance of \\Symfony\\Component\\HttpFoundation\\Response, " .
                "\\Inspirio\\Deployer\\Middleware\\ModuleInterface or NULL, got {$hint}"
            );
        }

        return new Response('404 Not Found', 404);
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
        $content = $module->render($request);

        // rendered content returned
        if (is_scalar($content)) {
            return new Response($content);
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

        $content = $this->view->render($template, $content);
        return new Response($content);
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

        $actionMethodName = $action . 'Action';
        $actionMethod     = null;

        try {
            $actionMethod = new \ReflectionMethod($module, $actionMethodName);
        } catch (\ReflectionException $e) {
        }

        if (!$actionMethod || !$actionMethod->isPublic()) {
            $moduleName = get_class($module);
            throw new \LogicException("Starter module '{$moduleName}' is missing '{$actionMethodName}' method");
        }

        $requestArgs = $request->request->all();
        $methodArgs  = array();

        foreach ($actionMethod->getParameters() as $i => $param) {
            $name = $param->getName();

            if (array_key_exists($name, $requestArgs)) {
                $methodArgs[$i] = $requestArgs[$name];

            } elseif ($param->isOptional()) {
                $methodArgs[$i] = $param->getDefaultValue();

            } else {
                $moduleName = get_class($this);
                throw new \InvalidArgumentException("Missing '{$moduleName}' starter module 'startup' method '{$name}' parameter value");
            }
        }

        return new StreamedResponse(function () use ($actionMethod, $module, $methodArgs) {
            $actionMethod->invokeArgs($module, $methodArgs);
        });
    }
}
