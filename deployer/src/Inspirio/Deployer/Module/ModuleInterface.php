<?php
namespace Inspirio\Deployer\Module;
use Symfony\Component\HttpFoundation\Request;

/**
 * Action interface.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
interface ModuleInterface
{
	/**
	 * Sets the application root dir.
	 *
	 * This path can be used to access the target application filesystem.
	 *
	 * @param string $projectDir
	 */
	public function setProjectDir($projectDir);

	/**
	 * Returns action name. Must be plain string without spaces.
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Returns action title. May contain HTML formatting.
	 *
	 * @return string
	 */
	public function getTitle();

	/**
	 * Checks if the action is enabled.
	 *
	 * @return bool
	 */
	public function isEnabled();

    /**
     * Renders the action user interface.
     *
     * @param Request $request
     * @return string
     */
	public function render(Request $request);
}
