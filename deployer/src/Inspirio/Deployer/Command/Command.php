<?php
namespace Inspirio\Deployer\Command;

use Symfony\Component\Process\Process;

class Command
{
	/**
	 * @var string
	 */
	protected $workingDir;

	/**
	 * @var bool
	 */
	protected $autoFlush = true;

	/**
	 * @var bool
	 */
	protected $showCmd = true;

	/**
	 * Constructor.
	 *
	 * @param string $workingDir
	 */
	public function __construct($workingDir)
	{
        $this->workingDir = $workingDir;
	}

	/**
	 * Applies the command config.
	 *
	 * @param array $config
	 */
	public function applyConfig(array $config)
	{
		if (isset($config['autoFlush'])) {
			$this->autoFlush = (bool)$config['autoFlush'];
		}

		if (isset($config['showCmd'])) {
			$this->showCmd = (bool)$config['showCmd'];
		}
	}

	/**
	 * Sets auto-flush mode.
	 *
	 * @param boolean $autoFlush
	 */
	public function setAutoFlush($autoFlush)
	{
		$this->autoFlush = $autoFlush;
	}

	/**
	 * Sets show-cmd mode.
	 *
	 * @param boolean $showCmd
	 */
	public function setShowCmd($showCmd)
	{
		$this->showCmd = $showCmd;
	}

	/**
	 * Renders text to the output.
	 *
	 * NOTE: keep this method publuc for PHP <5.4
	 *
	 * @param $text
	 */
	public function output($text)
	{
		echo $text;

		if ($this->autoFlush) {
			flush();
			ob_flush();
		}
	}

	/**
	 * Renders the executed command.
	 *
	 * @param string $cmd
	 * @return string
	 */
	protected function outputCmd($cmd)
	{
		if (!$this->showCmd) {
			return;
		}

		$this->output('<span class="command" style="color:blue;">'.$cmd."</span>\n");
	}
}
