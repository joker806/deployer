<?php
namespace Inspirio\Deployer\Module\Starter;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Module\ModuleInterface;

/**
 * Project starter module interface.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
interface StarterModuleInterface extends ModuleInterface
{
    /**
     * Checks if the application is started.
     *
     * @param ApplicationInterface $app
     * @return bool
     */
    public function isStarted(ApplicationInterface $app);
}