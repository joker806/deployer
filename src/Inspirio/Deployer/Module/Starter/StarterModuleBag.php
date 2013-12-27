<?php
namespace Inspirio\Deployer\Module\Starter;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Module\AbstractModuleBag;
use Inspirio\Deployer\Module\ModuleInterface;
use Inspirio\Deployer\Response\ModuleRedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StarterModuleBag extends AbstractModuleBag
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
     * @param StarterModuleInterface $module
     */
    protected function checkModule(Request $request, ModuleInterface $module, $moduleName)
    {
        if ($module->isStarted($this->app)) {
            return null;
        }

        if ($moduleName === null || $module->getName() === $moduleName) {
            return new ModuleRedirectResponse($module);
        }

        return new Response('412 Precondition Failed', 412);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateCategory()
    {
        return 'starter';
    }
}
