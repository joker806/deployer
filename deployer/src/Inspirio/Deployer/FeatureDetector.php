<?php
namespace Inspirio\Deployer;

/**
 * Application feature detector.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
class FeatureDetector
{
	/**
	 * @var string
	 */
	protected $projectDir;

	/**
	 * Constructor.
	 *
	 * @param string $projectDir
	 */
	public function __construct($projectDir)
	{
		$this->projectDir = $projectDir;
	}

	/**
	 * Check Composer support.
	 *
	 * @return bool
	 */
	public function hasComposer()
	{
		return file_exists($this->projectDir .'/composer.json');
	}

	/**
	 * Checks Symfony project.
	 *
	 * @return bool
	 */
	public function isSymfony()
	{
		return file_exists($this->projectDir .'/web/app.php');
	}

	/**
	 * Checks if the project has config for various environments (config-local.dev.php).
	 */
	public function hasEnvConfigs()
	{
		return file_exists($this->projectDir .'/settings/config-local.dev.php');
	}
}
