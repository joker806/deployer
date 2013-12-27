<?php
namespace Inspirio\Deployer\Module\Security;

use Inspirio\Deployer\Module\ModuleInterface;
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
