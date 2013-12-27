<?php
namespace Inspirio\Deployer\Starter;

use Inspirio\Deployer\AbstractModule;

abstract class AbstractStarterModule extends AbstractModule implements StarterModuleInterface
{
    public function startupAction(array $data)
    {
        return $this->startup($data);
    }
}
