<?php
namespace Inspirio\Deployer\Application;

use Inspirio\Deployer\Project;

class LazyCms3 extends SymfonyApp
{
    /**
     * {@inheritdoc}
     */
    public function __construct($rootPath)
    {
        parent::__construct($rootPath);

        $this
            ->addStarter(new \Inspirio\Deployer\Starter\SubversionCheckout())

            ->addModule(new \Inspirio\Deployer\Module\Info\InfoModule())
            ->addModule(new \Inspirio\Deployer\Module\Deployment\DeploymentModule())
            ->addModule(new \Inspirio\Deployer\Module\Configuration\ConfigurationModule())
            ->addModule(new \Inspirio\Deployer\Module\Maintenance\MaintenanceModule())
            ->addModule(new \Inspirio\Deployer\Module\Database\DatabaseModule())
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
