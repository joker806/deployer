<?php
namespace Inspirio\Deployer\Application;

use Inspirio\Deployer\StarterModule\StarterModuleInterface;
use Inspirio\Deployer\DeploymentModule\DeploymentModuleInterface;

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
     * @var DeploymentModuleInterface[]
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
     * @param DeploymentModuleInterface $module
     *
     * @return $this
     */
    public function addModule(DeploymentModuleInterface $module)
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * Returns registered action modules.
     *
     * @return DeploymentModuleInterface
     */
    public function getModules()
    {
        return $this->modules;
    }
}
