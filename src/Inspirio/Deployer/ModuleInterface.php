<?php
namespace Inspirio\Deployer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ModuleInterface
{
    /**
     * Returns module name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns module title.
     *
     * @return string
     */
    public function getTitle();

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
