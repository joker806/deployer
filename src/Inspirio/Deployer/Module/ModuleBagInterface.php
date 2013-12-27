<?php
namespace Inspirio\Deployer\Module;

use Inspirio\Deployer\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ModuleBagInterface
{
    /**
     * Returns template category name.
     *
     * @return string
     */
    public function getTemplateCategory();

    /**
     * Picks a module, that should handle the request.
     *
     * @param Config      $config
     * @param Request     $request
     * @param string|null $moduleName
     *
     * @return ModuleInterface when appropriate module is found
     * @return Response        when some specific action is need to be done
     * @return null            otherwise
     */
    public function pickModule(Config $config, Request $request, $moduleName);
}
