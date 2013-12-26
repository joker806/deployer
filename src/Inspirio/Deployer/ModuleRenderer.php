<?php
namespace Inspirio\Deployer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleRenderer {

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Renders the module.
     *
     * @param RenderableModuleInterface $module
     * @param Request                   $request
     *
     * @return Response
     */
    public function renderModule(RenderableModuleInterface $module, Request $request)
    {
        $content = $module->render($request);

        // complete response
        if ($content instanceof Response) {
            return $content;
        }

        // rendered content returned
        if (is_scalar($content)) {
            return new Response($content);
        }

        // view data returned
        $content['module']  = $module;
        $content['request'] = $request;

        $className = get_class($module);
        $className = substr($className, strrpos($className, '\\') + 1);
        $template  = 'starter/' . lcfirst($className) . '.html.php';

        $content = $this->twig->render($template, $content);
        return new Response($content);
    }
}
