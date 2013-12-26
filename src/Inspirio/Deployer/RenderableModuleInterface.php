<?php
namespace Inspirio\Deployer;

use Symfony\Component\HttpFoundation\Response;

/**
 * Describes renderable module interface.
 *
 */
interface RenderableModuleInterface extends ModuleInterface
{
    /**
     * Renders user interface of the module.
     *
     * @return Response complete response
     * @return string   rendered web page
     * @return array    data for the same-named template
     */
    public function render();
}
