<?php
namespace Inspirio\Deployer\Application;

use Inspirio\Deployer\Module\Deployment\DeploymentModuleBag;
use Inspirio\Deployer\Module\Starter\StarterModuleBag;
use Inspirio\Deployer\Module\Starter\StarterModuleInterface;
use Inspirio\Deployer\Module\Deployment\DeploymentModuleInterface;

abstract class AbstractApplication implements ApplicationInterface
{
    /**
     * @var string
     */
    private $rootPath;

    /**
     * @var StarterModuleBag
     */
    private $starterModules;

    /**
     * @var DeploymentModuleBag
     */
    private $deploymentModules;

    /**
     * {@inheritdoc}
     */
    public function __construct($rootPath)
    {
        $this->rootPath          = $rootPath;
        $this->starterModules    = new StarterModuleBag($this);
        $this->deploymentModules = new DeploymentModuleBag($this);
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
    public function addStarterModule(StarterModuleInterface $starter)
    {
        $this->starterModules->addModule($starter);
        return $this;
    }

    /**
     * Returns registered starter modules.
     *
     * @return StarterModuleBag
     */
    public function getStarterModules()
    {
        return $this->starterModules;
    }

    /**
     * Registers action module.
     *
     * @param DeploymentModuleInterface $module
     *
     * @return $this
     */
    public function addDeploymentModule(DeploymentModuleInterface $module)
    {
        $this->deploymentModules->addModule($module);
        return $this;
    }

    /**
     * Returns registered action modules.
     *
     * @return DeploymentModuleBag
     */
    public function getDeploymentModules()
    {
        return $this->deploymentModules;
    }
}
