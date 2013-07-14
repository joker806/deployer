<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Application\ApplicationInterface;
use Symfony\Component\HttpFoundation\Request;

interface ModuleInterface {

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
    public function render(Request $request);
}
