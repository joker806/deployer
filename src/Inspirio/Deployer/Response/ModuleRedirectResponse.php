<?php
namespace Inspirio\Deployer\Response;

use Inspirio\Deployer\Module\ModuleInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ModuleRedirectResponse extends RedirectResponse {

    public function __construct($module, $status = 307, array $headers = array())
    {
        if (is_string($module)) {
            // ok, we have a module name

        } elseif ($module instanceof ModuleInterface) {
            $module = $module->getName();

        } else {
            $hint = is_object($module) ? get_class($module) : gettype($module);
            throw new \InvalidArgumentException(
                "Invalid \$module argument value.".
                "Expected a module name or instance of ModuleInterface, ".
                "got {$hint}"
            );
        }

        parent::__construct('/?module='. $module, $status, $headers);
    }
}
