<?php
namespace Inspirio\Deployer\Application;

use Inspirio\Deployer\DeploymentModule;
use Inspirio\Deployer\Project;
use Inspirio\Deployer\Starter;

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
                Starter\ChoiceStarter::create('')
                    ->addChild(new Starter\SubversionCheckout())
                    ->addChild(new Starter\Dummy())
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

    /**
     * Returns project name and version,
     *
     * @return array
     */
    public function getProjectInfo()
    {
        return new Project('a');
    }
}
