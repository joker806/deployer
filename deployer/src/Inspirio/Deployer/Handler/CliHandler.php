<?php
namespace Inspirio\Deployer\Handler;

use Inspirio\Deployer\Module\Info\InfoModule;
use Symfony\Component\Yaml\Yaml;

/**
 * Cli request handler.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
class CliHandler extends Handler
{
	/**
	 * Handles the HTTP request.
	 *
	 * @return string
	 */
	public function dispatch()
	{
		$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
		$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
		          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

		$composerJson = $this->projectDir .'/composer.json';
		if (file_exists($composerJson)) {
			$project = json_decode(file_get_contents($composerJson), true);
		} else {
			$project = array();
		}

		$actionName = isset($_GET['action']) ? $_GET['action'] : null;
		$actions    = $this->getModules();
		$action     = $this->findModule($actionName);

		if (!$action) {
			$action = new InfoModule();
			$action->setProjectDir($this->projectDir);
		}

		if (!$action->isEnabled()) {
			header('HTTP/1.1 307 Temporary Redirect');
			header('Location: ?');
			exit;
		}

		if ($isPost) {
			if ($isAjax) {
				$action->handlePost($_POST);
				exit;

			} else {
				$params = $action->handlePost($_POST);

				if (is_array($params)) {
					if (!array_key_exists('action', $params)) {
						$params['action'] = $actionName;
					}

					header('HTTP/1.1 303 See Other');
					header('Location: ?'. http_build_query($params));
					exit;
				}
			}
		}

		$parameters = $_GET;
		unset($parameters['action']);

		ob_start();
		require __DIR__ . '/view/index.html.php';
		return ob_get_clean();
	}


}
