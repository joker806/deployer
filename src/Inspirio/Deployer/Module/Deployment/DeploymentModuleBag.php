<?php
namespace Inspirio\Deployer\Module\Deployment;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Module\AbstractModuleBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property DeploymentModuleInterface[] $modules
 */
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
    public function pickModule(Request $request, $moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->app->getHomeModuleName();
        }

        foreach ($this->app->getDeploymentModules() as $module) {
            if ($module->getName() !== $moduleName) {
                continue;
            }

            if (!$module->isEnabled()) {
                return null;
            }

            return $module;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateLayout()
    {
        return 'deploymentLayout.twig';
    }
}
