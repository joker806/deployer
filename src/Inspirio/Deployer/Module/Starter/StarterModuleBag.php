<?php
namespace Inspirio\Deployer\Module\Starter;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Module\AbstractModuleBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property StarterModuleInterface[] $modules
 */
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
     * {@inheritdoc}
     */
    public function pickModule(Request $request, $moduleName = null)
    {
        foreach ($this->modules as $module) {
            if ($module->isStarted($this->app)) {
                continue;
            }

            if ($moduleName === null || $moduleName === $module->getName()) {
                return $module;
            }

            return new Response('412 Precondition Failed', 412);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateLayout()
    {
        return 'starterLayout.twig';
    }
}
