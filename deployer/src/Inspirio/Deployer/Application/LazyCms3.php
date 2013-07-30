<?php
namespace Inspirio\Deployer\Application;


use Inspirio\Deployer\Bootstrap\StarterModuleInterface;
use Inspirio\Deployer\Module\ActionModuleInterface;
use Inspirio\Deployer\Project;
use Inspirio\Deployer\Security\SecurityInterface;

class LazyCms3 extends SymfonyApp
{
    public function __construct($rootPath)
    {
        parent::__construct($rootPath);

        $this
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
