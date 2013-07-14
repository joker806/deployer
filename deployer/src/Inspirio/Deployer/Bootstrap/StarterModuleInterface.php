<?php
namespace Inspirio\Deployer\Bootstrap;

use Inspirio\Deployer\ModuleInterface;

/**
 * Project starter interface.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
interface StarterModuleInterface extends ModuleInterface
{
    /**
     * Checks if project is bootstrapped.
     *
     * @return bool
     */
    public function isReady();

    /**
     * Bootstraps the application.
     */
    public function bootstrapApp();
}
