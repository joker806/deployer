<?php
namespace Inspirio\Deployer\Middleware;

use Inspirio\Deployer\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface MiddlewareInterface
{
    /**
     * Intercepts a request and returns name of module, that
     * should be displayed.
     *
     * @param Request $request
     * @param string|null $moduleName
     * @return ModuleInterface|Response|null
     */
    public function interceptRequest(Request $request, $moduleName = null);

    /**
     * Returns middleware template layout filename.
     *
     * @return string
     */
    public function getTemplateLayout();
}
