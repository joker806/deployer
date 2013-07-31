<?php
namespace Inspirio\Deployer\Command;

use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * Symfony command.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
class SymfonyCommand extends PhpCommand
{
	/**
	 * @var string
	 */
	protected $env = 'prod';

	/**
	 * @var bool
	 */
	protected $isDebug = false;

	/**
	 * Sets the target environment.
	 *
	 * @param string $env
	 */
	public function setEnv($env)
	{
		$this->env = $env;
	}

	/**
	 * Sets the target debug mode.
	 *
	 * @param bool $isDebug
	 */
	public function setDebug($isDebug)
	{
		$this->isDebug = $isDebug;
	}

	/**
	 * Switches the used parameters file.
	 *
	 * @param string $env
	 * @return bool
	 */
	public function switchParameters($env)
	{
		$paramsPath = $this->workingDir .'/app/config';
		$sourceFile = "{$paramsPath}/parameters_{$env}.yml";
		$targetFile = "{$paramsPath}/parameters.yml";

		if (!file_exists($sourceFile)) {
			return false;
		}

		if (file_exists($targetFile)) {
			unlink($targetFile);
		}

		copy($sourceFile, $targetFile);
		chmod($targetFile, 0660);

		return true;
	}

	/**
	 * Updates the application parameters.
	 *
	 * @param array $update
	 * @return bool
	 */
	public function updateParameters(array $update)
	{
		$file = $this->getParametersFile();

		$current = Yaml::parse($file);
		$current['parameters'] = $update + $current['parameters'];

		file_put_contents($file, Yaml::dump($current));

		return true;
	}

	/**
	 * Rebuilds application.
	 *
	 * This is a bulk command containing cache cleanup, assets dumping and other steps
	 * necessary after application installation or update.
	 *
	 * @return bool
	 */
	public function rebuildApplication()
	{
		$steps = array('buildBootstrap', 'cacheClear', 'assetsInstall', 'asseticDump', 'generateSitesMap');

		foreach ($steps as $step) {
			if (!call_user_func(array($this, $step))) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Builds a bootstrap cache file.
	 *
	 * @return bool
	 */
	public function buildBootstrap()
	{
		$builder = $this->createPhpProcessBuilder();
		$builder->add('vendor/sensio/distribution-bundle/Sensio/Bundle/DistributionBundle/Resources/bin/build_bootstrap.php');

		return $this->run($builder->getProcess());
	}

	/**
	 * Clears application cache.
	 *
	 * @return bool
	 */
	public function cacheClear()
	{
		$builder = $this->createSymfonyProcessBuilder();
		$builder->add('cache:clear');

		return $this->run($builder->getProcess());
	}

	/**
	 * Installs bundle assets.
	 *
	 * @return bool
	 */
	public function assetsInstall()
	{
		$builder = $this->createSymfonyProcessBuilder();
		$builder->add('assets:install');

		return $this->run($builder->getProcess());
	}

	/**
	 * Dumps Assetic assets.
	 *
	 * @return bool
	 */
	public function asseticDump()
	{
		$builder = $this->createSymfonyProcessBuilder();
		$builder->add('assetic:dump');

		return $this->run($builder->getProcess());
	}

	/**
	 * Generates sites map.
	 *
	 * @return bool
	 */
	public function generateSitesMap()
	{
		$builder = $this->createSymfonyProcessBuilder();
		$builder->add('generate:sites-map');

		return $this->run($builder->getProcess());
	}

	/**
	 * Creates process-builder for a PHP command.
	 *
	 * @return ProcessBuilder
	 */
	protected function createSymfonyProcessBuilder()
	{
		$builder = $this->createPhpProcessBuilder()
			->add('app/console')
			->add('--env='. $this->env)
		;

		if (!$this->isDebug) {
			$builder->add('--no-debug');
		}

		return $builder;
	}

	/**
	 * Returns parameters file path.
	 *
	 * @param string|null $env
	 * @return string
	 */
	private function getParametersFile($env = null)
	{
		$path = $this->workingDir .'/app/config/parameters';
		return $env ? "{$path}_{$env}.yml" : "{$path}.yml";
	}
}
