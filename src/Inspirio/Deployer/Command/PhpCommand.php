<?php
namespace Inspirio\Deployer\Command;

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

/**
 * PHP command class.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
class PhpCommand extends ProcessCommand
{
	/**
	 * @var string
	 */
	private $phpExecutable;

	/**
	 * Returns PHP executable.
	 *
	 * @return string
	 */
	private function getPhpExecutable()
	{
		if (!$this->phpExecutable) {
			$finder = new PhpExecutableFinder();
			$this->phpExecutable = $finder->find();
		}

		return $this->phpExecutable;
	}

	/**
	 * Creates process-builder for a PHP command.
	 *
	 * @return ProcessBuilder
	 */
	protected function createPhpProcessBuilder()
	{
		return $this->createProcessBuilder()
			->add('php')
			->add('-d memory_limit=-1')
			->add('-d safe_mode=off')
		;
	}
}
;
