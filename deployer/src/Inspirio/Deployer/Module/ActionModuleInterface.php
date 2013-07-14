<?php
namespace Inspirio\Deployer\Module;

use Inspirio\Deployer\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Action interface.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
interface ActionModuleInterface extends ModuleInterface
{
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
}
