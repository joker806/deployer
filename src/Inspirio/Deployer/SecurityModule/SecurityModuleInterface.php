<?php
namespace Inspirio\Deployer\SecurityModule;

use Inspirio\Deployer\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;

interface SecurityModuleInterface extends ModuleInterface
{
    /**
     * Checks if the user is authorized.
     *
     * @param Request $request
     * @return bool
     */
    public function isAuthorized(Request $request);
}
