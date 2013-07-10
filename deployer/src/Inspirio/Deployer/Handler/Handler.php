<?php
namespace Inspirio\Deployer\Handler;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Config\ConfigAware;
use Inspirio\Deployer\Module\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Yaml\Yaml;

/**
 * Action handler base class.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
abstract class Handler
{
    const CONFIG_FILE = 'deployer.yml';

    /**
     * @var ApplicationInterface
     */
    protected $app;

    /**
     * @var Config
     */
    protected $config;

	/**
	 * @var ModuleInterface[]
	 */
	protected $modules = null;

    /**
     * Constructor.
     *
     * @param ApplicationInterface $app
     * @param ModuleInterface[]    $modules
     */
	public function __construct(ApplicationInterface $app, array $modules)
	{
		$this->app        = $app;
        $this->config     = new Config(self::CONFIG_FILE);
		$this->setupModules($modules);
	}

	/**
	 * Dispatches the handler.
	 */
	public abstract function dispatch();

	/**
	 * Return real file-path (when file exists in project).
	 *
	 * @param string $fileName
	 * @return string|null
	 */
	public function findFile($fileName)
	{
		return realpath($this->app->getRootPath() .'/'. $fileName) ?: null;
	}

    /**
     * Runs the action.
     *
     * @param ModuleInterface $module
     * @param string          $action
     * @param array           $args
     * @return Response
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
	public function runAction(ModuleInterface $module, $action, array $args)
	{
		$actionRefl = null;

		try {
			$actionRefl = new \ReflectionMethod(get_class($module), $action .'Action');
		} catch (\ReflectionException $e) {}

		if (!$actionRefl || !$actionRefl->isPublic()) {
			throw new \LogicException("Module '{$module->getName()}' has no action '{$action}'");
		}

		$realArgs = array();

		foreach ($actionRefl->getParameters() as $i => $param) {
			$name = $param->getName();

			if (array_key_exists($name, $args)) {
				$realArgs[$i] = $args[$name];

			} elseif ($param->isOptional()) {
				$realArgs[$i] = $param->getDefaultValue();

			} else {
				throw new \InvalidArgumentException("Action '{$module->getName()}' is missing  '{$name}' parameter value");
			}
		}

        $response = new StreamedResponse(function() use ($actionRefl, $module, $realArgs) {
            $actionRefl->invokeArgs($module, $realArgs);
        });

		return $response;
	}

    /**
     * Returns module instance.
     *
     * @param string $moduleName
     * @return ModuleInterface|null
     */
    protected function findModule($moduleName)
    {
        if ($moduleName && isset($this->modules[$moduleName])) {
            $module = $this->modules[$moduleName];

        } else {
            $module = reset($this->modules);
        }

        if (!$module->isEnabled()) {
            $module = null;

            foreach ($this->modules as $try) {
                if ($try->isEnabled()) {
                    $module = $try;
                    break;
                }
            }
        }

        return $module;
    }

	/**
	 * Setups action instances list.
	 *
	 * @param ModuleInterface[] $modules
	 */
	private function setupModules(array $modules)
	{
		$this->modules = array();

		foreach ($modules as $module) {
			$this->modules[$module->getName()] = $module;
            $module->setProjectDir($this->app->getRootPath());

            if ($module instanceof ConfigAware) {
                $module->setConfig($this->config);
            }
		}
	}
}
