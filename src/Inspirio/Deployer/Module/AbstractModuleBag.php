<?php
namespace Inspirio\Deployer\Module;

use Inspirio\Deployer\Module\ModuleInterface;

abstract class AbstractModuleBag implements ModuleBagInterface, \IteratorAggregate {

    /**
     * @var ModuleInterface[]
     */
    protected $modules = array();

    /**
     * Adds the module into the bag.
     *
     * @param ModuleInterface $module
     *
     * @return $this
     */
    public function addModule(ModuleInterface $module)
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * Creates modules iterator.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->modules);
    }
}
