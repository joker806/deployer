<?php
namespace Inspirio\Deployer\Application;

use Inspirio\Deployer\Bootstrap\StarterModuleInterface;
use Inspirio\Deployer\Module\ActionModuleInterface;
use Inspirio\Deployer\ProjectInfoInterface;
use Inspirio\Deployer\Security\SecurityInterface;

interface ApplicationInterface {

    /**
     * Constructor
     *
     * @param $appDir
     *
     * @throws  \RuntimeException
     */
    public function __construct($appDir);

    /**
     * Returns path to application root dir.
     *
     * @return string
     */
    public function getRootPath();

    /**
     * Returns application security modules.
     *
     * @return SecurityInterface[]
     */
    public function getSecurity();

    /**
     * Returns application starter modules.
     *
     * @return StarterModuleInterface[]
     */
    public function getStarters();

    /**
     * Returns application-registered modules.
     *
     * @return ActionModuleInterface[]
     */
    public function getModules();

    /**
     * Returns project name and version,
     *
     * @return ProjectInfoInterface
     */
    public function getProjectInfo();

	/**
	 * Returns database connection.
	 *
	 * @return \PDO
	 */
	public function getDatabaseConnection();
}
