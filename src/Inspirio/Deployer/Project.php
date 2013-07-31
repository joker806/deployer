<?php
namespace Inspirio\Deployer;


class Project implements ProjectInfoInterface
{
	/**
	 * @var array
	 */
	private $description;

	/**
	 * Constructor.
	 *
	 * @param string $descriptorFile
	 */
	public function __construct($descriptorFile)
	{
		$this->description = array();// json_decode(file_get_contents($descriptorFile), true);
	}

	/**
	 * Returns project name.
	 *
	 * @return null|string
	 */
	public function getName()
	{
		return isset($this->description['name']) ? $this->description['name'] : null;
	}

	/**
	 * Returns project version.
	 *
	 * @return null|string
	 */
	public function getVersion()
	{
		return isset($this->description['version']) ? $this->description['version'] : null;
	}
}
