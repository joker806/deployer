<?php
namespace Inspirio\Deployer\Command;

use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Composer command.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
class ComposerCommand extends PhpCommand
{
	/**
	 * Downloads Composer.
	 *
	 * @return bool
	 */
	public function downloadComposer()
	{
		$proc = new Process('wget http://getcomposer.org/composer.phar');

		return $this->run($proc);
	}

	/**
	 * Runs composer install command.
	 *
	 * @return bool
	 */
	public function install()
	{
		$builder = $this->createComposerProcessBuilder('install');
		$builder->add('--no-dev');
		$builder->add('--prefer-dist');
		$builder->add('--no-scripts');
		$builder->add('--optimize-autoloader');

		$result = $this->run($builder->getProcess());

		return $result;
	}

	/**
	 * Runs composer update command.
	 *
	 * @return bool
	 */
	public function update()
	{
		$builder = $this->createComposerProcessBuilder('update');
		$builder->add('--no-dev');
		$builder->add('--prefer-dist');
		$builder->add('--no-scripts');
		$builder->add('--optimize-autoloader');

		return $this->run($builder->getProcess());
	}

    /**
     * Creates process-builder for a Composer command.
     *
     * @param string $cmd
     * @return ProcessBuilder
     */
	protected function createComposerProcessBuilder($cmd)
	{
		return $this->createPhpProcessBuilder()
			->setEnv('COMPOSER_HOME', '.composer')
			->add('composer.phar')
			->add($cmd)
			->add('--no-interaction')
		;
	}
}
