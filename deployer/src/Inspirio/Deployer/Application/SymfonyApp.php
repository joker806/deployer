<?php
namespace Inspirio\Deployer\Application;

use Inspirio\Deployer\Bootstrap\StarterModuleInterface;
use Inspirio\Deployer\Module\ActionModuleInterface;
use Inspirio\Deployer\Security\SecurityInterface;
use Symfony\Component\Yaml\Yaml;

abstract class SymfonyApp extends AbstractApplication
{
	const PARAMS_FILE = 'app/config/parameters.yml';

    /**
     * Returns project name and version,
     *
     * @return array
     */
    public function getProjectInfo()
    {
        // TODO: Implement getProjectInfo() method.
    }

    /**
	 * {@inheritdoc}
	 */
	public function getDatabaseConnection()
	{
		$defaults = array(
			'database_driver'   => 'pdo_mysql',
			'database_host'     => 'localhost',
			'database_name'     => null,
			'database_user'     => null,
			'database_password' => null,
		);

        $params  = $this->loadParams();
        $params += $defaults;

		if (!preg_match('/^pdo_(\w+)$/', $params['database_driver'], $match)) {
			throw new \RuntimeException("Invalid database driver '{$params['database_driver']}'");
		}

		$driver = $match[1];
        $dsn    = "{$driver}:host={$params['database_host']};dbname={$params['database_name']}";

		return new \PDO($dsn, $params['database_user'], $params['database_password']);
	}

	/**
	 * Loads content of the parameters.yml file.
	 *
	 * @param string $paramsFile
	 * @return array
	 */
	private function loadParams($paramsFile = self::PARAMS_FILE)
	{
		$params = Yaml::parse(file_get_contents($this->getRootPath() .'/'. $paramsFile));
		return $params['parameters'];
	}
}
