<?php
namespace Inspirio\Deployer\Application;

use Inspirio\Deployer\DeploymentModule;
use Inspirio\Deployer\StarterModule;

class LazyCms3 extends SymfonyApp
{
    /**
     * {@inheritdoc}
     */
    public function __construct($rootPath)
    {
        parent::__construct($rootPath);

        $this
            ->addStarter(
                StarterModule\ChoiceStarter::create('')
                    ->addChild(new StarterModule\SubversionCheckout())
                    ->addChild(new StarterModule\Dummy())
            )

            ->addModule(new DeploymentModule\Info\InfoModule())
            ->addModule(new DeploymentModule\Deployment\DeploymentModule())
            ->addModule(new DeploymentModule\Configuration\ConfigurationModule())
            ->addModule(new DeploymentModule\Maintenance\MaintenanceModule())
            ->addModule(new DeploymentModule\Database\DatabaseModule())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getHomeModuleName()
    {
        return 'info';
    }
}
