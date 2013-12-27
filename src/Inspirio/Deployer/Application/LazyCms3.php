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
            ->addStarterModule(
                StarterModule\ChoiceStarter::create('')
                    ->addChild(new StarterModule\SubversionCheckout())
                    ->addChild(new StarterModule\Dummy())
            )

            ->addDeploymentModule(new DeploymentModule\Info\InfoModule())
            ->addDeploymentModule(new DeploymentModule\Deployment\DeploymentModule())
            ->addDeploymentModule(new DeploymentModule\Configuration\ConfigurationModule())
            ->addDeploymentModule(new DeploymentModule\Maintenance\MaintenanceModule())
            ->addDeploymentModule(new DeploymentModule\Database\DatabaseModule())
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
