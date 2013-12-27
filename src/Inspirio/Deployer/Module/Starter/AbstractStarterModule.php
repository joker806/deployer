<?php
namespace Inspirio\Deployer\Module\Starter;

use Inspirio\Deployer\Module\AbstractModule;

abstract class AbstractStarterModule extends AbstractModule implements StarterModuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTemplatePath()
    {
        return 'starter';
    }
}
