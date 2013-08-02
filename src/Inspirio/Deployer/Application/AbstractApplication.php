<?php
namespace Inspirio\Deployer\Application;

use Inspirio\Deployer\Starter\StarterModuleInterface;
use Inspirio\Deployer\Module\ActionModuleInterface;

abstract class AbstractApplication implements ApplicationInterface
{
    /**
     * @var string
     */
    private $rootPath;

    /**
     * @var StarterModuleInterface[]
     */
    private $starters;

    /**
     * @var ActionModuleInterface[]
     */
    private $modules;

    /**
     * {@inheritdoc}
     */
    public function __construct($rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootPath() {
        return $this->rootPath;
    }

    /**
     * {@inheritdoc}
     */
    public function findFile($file)
    {
        return realpath($this->getRootPath() . '/' . $file) ? : null;
    }

    /**
     * Registers starter module.
     *
     * @param StarterModuleInterface $starter
     * @return $this
     */
    public function addStarter(StarterModuleInterface $starter)
    {
        $starter->setApp($this);

        $this->starters[] = $starter;
        return $this;
    }

    /**
     * Returns registered starter modules.
     *
     * @return StarterModuleInterface
     */
    public function getStarters()
    {
        return $this->starters;
    }

    /**
     * Registers action module.
     *
     * @param ActionModuleInterface $module
     * @return $this
     */
    public function addModule(ActionModuleInterface $module)
    {
        $module->setApp($this);

        $this->modules[] = $module;
        return $this;
    }

    /**
     * Returns registered action modules.
     *
     * @return ActionModuleInterface
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Finds module instance by its name.
     *
     * @param string $moduleName
     * @return ActionModuleInterface|null
     */
    private function findModule($moduleName)
    {
        foreach ($this->modules as $module) {
            if ($module->getName() != $moduleName) {
                continue;
            }

            if (!$module->isEnabled()) {
                continue;
            }

            return $module;
        }

        return null;
    }
}
