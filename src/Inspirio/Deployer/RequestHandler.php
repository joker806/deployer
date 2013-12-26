<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Middleware\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Web request handler.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
class RequestHandler
{

    /**
     * @var ModuleRenderer
     */
    private $moduleRenderer;

    /**
     * @var ActionRunner
     */
    private $actionRunner;

    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares = array();

    /**
     * Constructor.
     *
     * @param ModuleRenderer $renderer
     * @param ActionRunner   $runner
     */
    public function __construct(ModuleRenderer $renderer, ActionRunner $runner)
    {
        $this->moduleRenderer = $renderer;
        $this->actionRunner   = $runner;
    }

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

            if (!$result instanceof ModuleInterface) {
                $hint = is_object($result) ? get_class($result) : gettype($result);
                throw new \LogicException(
                    "Invalid middleware result type. " .
                    "Expected instance of \\Symfony\\Component\\HttpFoundation\\Response, " .
                    "\\Inspirio\\Deployer\\Middleware\\ModuleInterface or NULL, got {$hint}"
                );
            }

            $request->attributes->set('module', $result);

            if ($request->isMethod('get')) {
                if (!$result instanceof RenderableModuleInterface) {
                    return new Response("GET method no allowed for module '{$moduleName}'.", 405);
                }

                return $this->moduleRenderer->renderModule($result, $request);

            } else {
                if (!$result instanceof RunnableModuleInterface) {
                    return new Response("POST method no allowed for module '{$moduleName}'.", 405);
                }

                return $this->actionRunner->runAction($result, $request);
            }
        }

        return new Response('404 Not Found', 404);
    }
}
