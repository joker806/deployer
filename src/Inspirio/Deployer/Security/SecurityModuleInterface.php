<?php
namespace Inspirio\Deployer\Security;

use Inspirio\Deployer\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;

interface SecurityModuleInterface extends ModuleInterface
{
    /**
     * Checks if user request is authorized.
     *
     * @param Request $request
     * @return bool
     */
    public function isAuthorized(Request $request);

    /**
     * Authorizes user request.
     *
     * @param Request $request
     * @return bool
     */
    public function authorizeAction(Request $request);
}
