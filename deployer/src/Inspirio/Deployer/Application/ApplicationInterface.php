<?php
namespace Inspirio\Deployer\Application;

use Inspirio\Deployer\Module\ModuleInterface;
use Inspirio\Deployer\Security\SecurityInterface;
use Inspirio\Deployer\Starter\StarterInterface;

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
     * Returns application started module.
     *
     * @return StarterInterface[]
     */
    public function getStarted();

    /**
     * Returns application-registered modules.
     *
     * @return ModuleInterface[]
     */
    public function getModules();

    /**
     * Returns project name and version,
     *
     * @return array
     */
    public function getProjectInfo();

	/**
	 * Returns database connection.
	 *
	 * @return \PDO
	 */
	public function getDatabaseConnection();
}