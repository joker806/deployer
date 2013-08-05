<?php
namespace Inspirio\Deployer\Starter;

use Inspirio\Deployer\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Project starter interface.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
interface StarterModuleInterface extends ModuleInterface
{
    /**
     * Checks if application is started.
     *
     * @return bool
     */
    public function isStarted();

    /**
     * Startup application.
     *
     * @param array $data
     */
    public function startupAction(array $data);
}
