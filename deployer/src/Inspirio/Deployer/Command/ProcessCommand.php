<?php
namespace Inspirio\Deployer\Command;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Basic process command class.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
class ProcessCommand extends Command
{
	/**
	 * Runs the command.
	 *
	 * @param string $cmd
	 * @return bool
	 */
	public function runCmd($cmd)
	{
		return $this->run(new Process($cmd));
	}

	/**
	 * Runs the process.
	 *
	 * @param Process $process
	 * @return bool
	 */
	protected function run(Process $process)
	{
		$process->setWorkingDirectory($this->workingDir);
		$this->outputCmd($process->getCommandLine());

		$me     = $this;
		$output = function ($type, $buffer) use ($me) {
			if ('err' === $type) {
				$lines = explode("\n", $buffer);
				$lines = array_map(function($line) {
					return '<span class="error" style="color:red;">'.$line.'</span>';
				}, $lines);

				$buffer = implode("\n", $lines);
			}

			$me->output($buffer);
		};

		return $process->run($output) === 0;
	}

	/**
	 * Creates process-builder.
	 *
	 * @return ProcessBuilder
	 */
	protected function createProcessBuilder()
	{
		return new ProcessBuilder();
	}
}
