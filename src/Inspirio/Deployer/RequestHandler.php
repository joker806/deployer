<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Module\ModuleBagInterface;
use Inspirio\Deployer\Module\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @var ModuleRenderer
     */
    private $moduleRenderer;

    /**
     * @var ActionRunner
     */
    private $actionRunner;

    /**
     * @var ModuleBagInterface[]
     */
    private $moduleBags = array();

    /**
     * Constructor.
     *
     * @param Config         $config
     * @param ModuleRenderer $renderer
     * @param ActionRunner   $runner
     */
    public function __construct(
        Config $config,
        ModuleRenderer $renderer,
        ActionRunner $runner
    ) {
        $this->config         = $config;
        $this->moduleRenderer = $renderer;
        $this->actionRunner   = $runner;
    }

    /**
     * Adds the module bag.
     *
     * @param ModuleBagInterface $bag
     *
     * @return $this
     */
    public function addModuleBag(ModuleBagInterface $bag)
    {
        $this->moduleBags[] = $bag;
        return $this;
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
    public function handleRequest(Request $request)
    {
//        if (
//            !$request->isMethod('get') &&
//            !($request->isMethod('post') && $request->query->has('module'))
//        ) {
//            return new Response('405 Method Not Allowed', 405);
//        }
//
//        if ($request->isMethod('post') && !$request->isXmlHttpRequest()) {
//            throw new \Exception('Handling of non-ajax POST requests is not implemented yet');
//        }

        $moduleName = $request->query->get('module');

        foreach ($this->moduleBags as $bag) {
            $module = $bag->pickModule($this->config, $request, $moduleName);

            // no module from the current bag
            // try another bag
            if ($module === null) {
                continue;
            }

            // no module, but specific response
            // return the response
            if ($module instanceof Response) {
                return $module;
            }

            if (!$module instanceof ModuleInterface) {
                $hint = is_object($module) ? get_class($module) : gettype($module);
                throw new \LogicException(
                    "Invalid ModuleBag::pickModule() result type. " .
                    "Expected instance of \\Symfony\\Component\\HttpFoundation\\Response, " .
                    "\\Inspirio\\Deployer\\Module\\ModuleInterface or NULL, got {$hint}"
                );
            }

            $request->attributes->set('module', $module);

            if ($request->isMethod('get')) {
                return $this->moduleRenderer->renderModule($request, $module);

            } else {
                return $this->actionRunner->runAction($module, $request);
            }
        }

        return new Response('404 Not Found', 404);
    }
}
