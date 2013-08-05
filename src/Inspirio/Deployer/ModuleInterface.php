<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Application\ApplicationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ModuleInterface
{
    /**
     * Sets the application.
     *
     * @param ApplicationInterface $app
     */
    public function setApp(ApplicationInterface $app);

    /**
     * Returns module title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Returns module name.
     *
     * @return string
     */
    public function getName();

    /**
     * Renders module.
     *
     * @param Request $request
     * @return string
     */
    public function render(Request $request);
}
