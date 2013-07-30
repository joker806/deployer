<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\View\View;
use Symfony\Component\HttpFoundation\Request;

interface ModuleInterface
{
    /**
     * Sets the configuration.
     *
     * @param Config $config
     */
    public function setConfig(Config $config);

    /**
     * Sets the application.
     *
     * @param ApplicationInterface $app
     */
    public function setApp(ApplicationInterface $app);

    /**
     * Renders the action user interface.
     *
     * @param Request $request
     * @return string
     */
    public function handleRequest(Request $request);
}
