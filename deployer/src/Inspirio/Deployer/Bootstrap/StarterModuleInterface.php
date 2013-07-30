<?php
namespace Inspirio\Deployer\Bootstrap;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\ModuleInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Project starter interface.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
interface StarterModuleInterface extends ModuleInterface
{
    /**
     * Sets application to startup.
     *
     * @param ApplicationInterface $app
     */
    public function setApp(ApplicationInterface $app);
}
