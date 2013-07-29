<?php
namespace Inspirio\Deployer\Application;


use Inspirio\Deployer\Bootstrap\StarterModuleInterface;
use Inspirio\Deployer\Module\ActionModuleInterface;
use Inspirio\Deployer\Security\SecurityInterface;

abstract class AbstractApplication implements ApplicationInterface
{
    /**
     * @var SecurityInterface[]
     */
    private $security;

    /**
     * @var StarterModuleInterface[]
     */
    private $starters;

    /**
     * @var ActionModuleInterface[]
     */
    private $modules;

    /**
     * Registers security module.
     *
     * @param SecurityInterface $security
     * @return $this
     */
    public function addSecurity(SecurityInterface $security)
    {
        $this->security[] = $security;
        return $this;
    }

    /**
     * Returns registered security modules.
     *
     * @return SecurityInterface
     */
    public function getSecurity()
    {
        return $this->security;
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
     * @param ActionModuleInterface $module
     * @return $this
     */
    public function addModule(ActionModuleInterface $module)
    {
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
}
