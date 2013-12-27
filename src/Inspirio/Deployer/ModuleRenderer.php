<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Module\ModuleBagInterface;
use Inspirio\Deployer\Module\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleRenderer {

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var Request|null
     */
    private $request = null;

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
     * @param Request         $request
     * @param ModuleInterface $module
     *
     * @throws \RuntimeException
     * @return Response
     */
    public function renderModule(Request $request, ModuleInterface $module)
    {
        $this->request = $request;

        $response = $this->subRenderModule($module);

        if (!$response instanceof Response) {
            $template = $module->getTemplatePath() .'/layout.twig';

            $data['module']        = $module;
            $data['moduleContent'] = $response;
            $data['request']       = $request;

            $response = $this->twig->render($template, $data);
            $response = new Response($response);
        }

        $this->request = null;

        return $response;
    }

    /**
     * Renders the module.
     *
     * @param ModuleInterface $module
     *
     * @throws \RuntimeException
     * @return Response|string
     */
    public function subRenderModule(ModuleInterface $module)
    {
        if ($this->request === null) {
            throw new \RuntimeException("Can't sub-render module {$module}, call the renderModule method first");
        }

        $data = $module->render($this->twig, $this->request);

        // a complete response returned
        if ($data instanceof Response) {
            return $data;
        }

        // rendered content returned
        if (is_scalar($data)) {
            return (string)$data;
        }

        // invalid content
        if (!is_array($data)) {
            $hint = is_object($data) ? get_class($data) : gettype($data);
            throw new \RuntimeException("Module {$module->getName()} render returned {$hint}, string or array expected.");
        }

        // view data returned
        $data['module']  = $module;
        $data['request'] = $this->request;

        $template = lcfirst(implode('', array_map('ucfirst', explode('_', $module->getName()))));
        $template = $module->getTemplatePath() .'/'. $template .'.twig';

        return $this->twig->render($template, $data);
    }
}
