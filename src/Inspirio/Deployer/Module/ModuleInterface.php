<?php
namespace Inspirio\Deployer\Module;

use Inspirio\Deployer\Config;
use Symfony\Component\HttpFoundation\Request;

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
     * Sets the module config.
     *
     * @param Config $config
     */
    public function setConfig(Config $config);

    /**
     * Renders user interface of the module.
     *
     * @param \Twig_Environment $twig
     * @param Request           $request
     *
     * @return string rendered web page
     * @return string data for the same-named template
     */
    public function render(\Twig_Environment $twig, Request $request);
}