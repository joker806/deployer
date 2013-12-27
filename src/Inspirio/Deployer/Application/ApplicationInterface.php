<?php
namespace Inspirio\Deployer\Application;

use Inspirio\Deployer\Starter\StarterModuleInterface;
use Inspirio\Deployer\DeploymentModule\DeploymentModuleInterface;
use Inspirio\Deployer\ProjectInfoInterface;

interface ApplicationInterface {

    /**
     * Constructor
     *
     * @param $rootPath
     *
     * @throws \RuntimeException
     */
    public function __construct($rootPath);

    /**
     * Returns path to application root dir.
     *
     * @return string
     */
    public function getRootPath();

    /**
     * Finds path to application file (if file exists).
     *
     * @param string $file
     * @return string|null
     */
    public function findFile($file);

    /**
     * Returns application starter modules.
     *
     * @return StarterModuleInterface[]
     */
    public function getStarters();

    /**
     * Returns application-registered modules.
     *
     * @return DeploymentModuleInterface[]
     */
    public function getModules();

    /**
     * Returns name of home module.
     *
     * @return string
     */
    public function getHomeModuleName();

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
