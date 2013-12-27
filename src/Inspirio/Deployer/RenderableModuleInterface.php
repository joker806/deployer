<?php
namespace Inspirio\Deployer;

use Symfony\Component\HttpFoundation\Request;

/**
 * Describes renderable module interface.
 *
 */
interface RenderableModuleInterface extends ModuleInterface
{
    /**
     * Renders user interface of the module.
     *
     * @param Request $request
     *
     * @return string rendered web page
     * @return array  data for the same-named template
     */
    public function render(Request $request);
}
