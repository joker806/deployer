<?php
namespace Inspirio\Deployer\Module;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * ModuleRedirectResponse represents an HTTP response doing a redirect to a module.
 *
 */
class ModuleRedirectResponse extends RedirectResponse
{
    /**
     * Constructor.
     *
     * @param string $module
     * @param int $status
     */
    public function __construct($module, $status = 307)
    {
        parent::__construct("/?module={$module}", $status);
    }

}
