<?php
namespace Inspirio\Deployer\Command;


class BulkCommand extends Command
{
	/**
	 * @var array
	 */
	protected $steps;

	/**
	 * Constructor
	 *
	 * @param array $steps
	 */
	public function __construct(array $steps)
	{
		$this->steps = $steps;
	}

	/**
	 * Runs commands.
	 *
	 * @return bool
	 */
	public function run()
	{
		foreach ($this->steps as $step) {
			if (!call_user_func_array(array($step[0], $step[1]), isset($step[2]) ? $step[2] : array())) {
				return false;
			}

			$this->output("\n\n");
		}

		return true;
	}
}
