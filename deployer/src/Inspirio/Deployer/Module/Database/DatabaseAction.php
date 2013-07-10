<?php
namespace Inspirio\Deployer\Module\Database;

use Inspirio\Deployer\Module\Module;
use Inspirio\Deployer\Command\ComposerCommand;
use Inspirio\Deployer\Command\ProcessCommand;
use Inspirio\Deployer\Command\SubversionCommand;
use Inspirio\Deployer\Command\SymfonyCommand;
use Inspirio\Deployer\Deployer;
use Symfony\Component\Yaml\Yaml;

class DatabaseAction extends Module
{
	/**
	 * {@inheritdoc}
	 */
	public function getTitle()
	{
		return '<i class="icon-hdd"></i> Database';
	}

	/**
	 * {@inheritdoc}
	 */
	public function isEnabled()
	{
		return file_exists($this->projectDir .'/app/config/parameters.yml');
	}

	/**
	 * {@inheritdoc}
	 */
	public function handlePost(array $data)
	{


		return array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(array $query)
	{
		return $this->renderTemplate('base', array(

		));
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
