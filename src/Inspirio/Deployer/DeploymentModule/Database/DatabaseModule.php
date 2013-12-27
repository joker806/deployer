<?php
namespace Inspirio\Deployer\DeploymentModule\Database;

use Inspirio\Deployer\Module\Deployment\AbstractDeploymentModule;
use Symfony\Component\Yaml\Yaml;

class DatabaseModule extends AbstractDeploymentModule
{
	/**
	 * {@inheritdoc}
	 */
	public function getTitle()
	{
		return '<i class="icon-hdd"></i> Database';
	}

    /**
     * List of module sections.
     *
     * @return array
     */
    protected function getSections()
    {
        return array(
	        'console' => 'Console'
        );
    }

	/**
	 * Returns 'console' template data.
	 *
	 * @return bool|array
	 */
	protected function renderConsole()
	{
		return array(
			'repoUrl'   => 'https://svn.inspirio.cz/inspirio/projects/',
			'env'       => 'prod',
			'revision'  => 'HEAD',
		);
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
			return $params['parameters'];

		} else {
			return array();
		}
	}

	/**
	 * Writes the parameters file.
	 *
	 * @param array  $parameters
	 * @param string $env
	 */
	private function writeParameters(array $parameters, $env = null)
	{
		file_put_contents($this->getParametersFile(), Yaml::dump(array('parameters' => $parameters)));
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
