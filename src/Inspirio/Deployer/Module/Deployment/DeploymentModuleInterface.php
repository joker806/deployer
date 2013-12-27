<?php
namespace Inspirio\Deployer\Module\Deployment;

use Inspirio\Deployer\Module\ModuleInterface;

/**
 * Action interface.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
interface DeploymentModuleInterface extends ModuleInterface
{
    /**
     * Checks if the action is enabled.
     *
     * @return bool
     */
    public function isEnabled();
}
