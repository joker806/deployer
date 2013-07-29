<?php
namespace Inspirio\Deployer\Application;


use Inspirio\Deployer\Bootstrap\StarterModuleInterface;
use Inspirio\Deployer\Module\ActionModuleInterface;
use Inspirio\Deployer\Project;
use Inspirio\Deployer\Security\SecurityInterface;

class LazyCms3 extends SymfonyApp
{
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
