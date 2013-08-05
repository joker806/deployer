<?php
namespace Inspirio\Deployer\Module;

use Inspirio\Deployer\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Action interface.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
interface ActionModuleInterface extends ModuleInterface
{
    /**
     * Checks if the action is enabled.
     *
     * @return bool
     */
    public function isEnabled();
}
