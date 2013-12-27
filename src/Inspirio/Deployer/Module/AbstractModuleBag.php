<?php
namespace Inspirio\Deployer\Module;

use Inspirio\Deployer\Config;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * {@inheritdoc}
     */
    public function pickModule(Config $config, Request $request, $moduleName)
    {
        foreach ($this->modules as $module) {
            $module->setConfig($config);

            if ($this->checkModule($request, $module, $moduleName)) {
                return $module;
            }
        }

        return null;
    }

    /**
     * @param Request         $request
     * @param ModuleInterface $module
     * @param string|null     $moduleName
     *
     * @return bool
     */
    abstract protected function checkModule(Request $request, ModuleInterface $module, $moduleName);
}
