<?php
namespace Inspirio\Deployer\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface SecurityModuleInterface
{
    /**
     * Authorizes request.
     *
     * @param Request $request
     * @return bool|Response
     */
    public function authorize(Request $request);
}
