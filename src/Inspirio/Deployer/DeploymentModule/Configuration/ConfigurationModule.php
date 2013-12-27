<?php
namespace Inspirio\Deployer\DeploymentModule\Configuration;

use Inspirio\Deployer\Module\Deployment\AbstractDeploymentModule;
use Inspirio\Deployer\Command\SymfonyCommand;
use Symfony\Component\Yaml\Yaml;

class ConfigurationModule extends AbstractDeploymentModule
{
	/**
	 * {@inheritdoc}
	 */
	public function getTitle()
	{
		return '<i class="icon-cog"></i> Configuration';
	}

	/**
	 * {@inheritdoc}
	 */
	public function isEnabled()
	{
		return (bool)$this->app->findFile('app/config');
	}

    /**
     * {@inheritdoc}
     */
    protected function getSections()
    {
        return array(
            'parameters' => 'Parameters',
        );
    }

    public function setPredefinedParametersAction($env)
    {
        $symfony = new SymfonyCommand($this->projectDir);
        return $symfony->switchParameters($env);
    }

    public function setCustomParametersAction($databaseName, $databaseHost, $databaseUser, $databasePassword)
    {
        $symfony = new SymfonyCommand($this->projectDir);
        return $symfony->updateParameters(array(
            'database_name'     => $databaseName,
            'database_host'     => $databaseHost,
            'database_user'     => $databaseUser,
            'database_password' => $databasePassword,
        ));
    }

	/**
	 * {@inheritdoc}
	 */
	public function renderParameters()
	{
		$params = $this->readParameters();
		$params += array(
			'databaseName'     => '',
			'databaseHost'     => '',
			'databaseUser'     => '',
			'databasePassword' => '',
		);

		return $params;
	}

	/**
	 * Reads the parameters file.
	 *
	 * @param string $env
	 * @return array
	 */
	private function readParameters($env = null)
	{
		$file = $this->getParametersFile($env);

		if (file_exists($file)) {
			$params = Yaml::parse(file_get_contents($file));
			$params = $params['parameters'];

            return array(
                'databaseName'     => isset($params['database_name'])     ? $params['database_name']     : '',
                'databaseHost'     => isset($params['database_host'])     ? $params['database_host']     : '',
                'databaseUser'     => isset($params['database_user'])     ? $params['database_user']     : '',
                'databasePassword' => isset($params['database_password']) ? $params['database_password'] : '',
            );

		} else {
			return array();
		}
	}

	/**
	 * Returns parameters file path.
	 *
	 * @param string|null $env
	 * @return string
	 */
	private function getParametersFile($env = null)
	{
		$path = $this->projectDir .'/app/config/parameters';
		return $env ? "{$path}_{$env}.yml" : "{$path}.yml";
	}
}
