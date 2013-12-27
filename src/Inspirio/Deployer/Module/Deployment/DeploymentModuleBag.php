<?php
namespace Inspirio\Deployer\Module\Deployment;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Config;
use Inspirio\Deployer\Module\AbstractModuleBag;
use Inspirio\Deployer\Module\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;

class DeploymentModuleBag extends AbstractModuleBag
{
    /**
     * @var ApplicationInterface
     */
    private $app;

    /**
     * Constructor.
     *
     * @param ApplicationInterface $app
     */
    function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function pickModule(Config $config, Request $request, $moduleName)
    {
        if ($moduleName === null) {
            $moduleName = $this->app->getHomeModuleName();
        }

        return parent::pickModule($config, $request, $moduleName);
    }

    /**
     * @param DeploymentModuleInterface $module
     */
    protected function checkModule(Request $request, ModuleInterface $module, $moduleName)
    {
        if ($module->getName() !== $moduleName) {
            return null;
        }

        if (!$module->isEnabled()) {
            return null;
        }

        return $module;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateCategory()
    {
        return 'deployment';
    }
}
