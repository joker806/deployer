<?php
namespace Inspirio\Deployer\Middleware;

use Inspirio\Deployer\Application\ApplicationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleMiddleware implements MiddlewareInterface
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
    public function interceptRequest(Request $request, $moduleName = null)
    {
        if ($moduleName === null) {
            return new ModuleRedirectResponse($this->app->getHomeModuleName());
        }

        foreach ($this->app->getModules() as $module) {
            if ($module->getName() !== $moduleName) {
                continue;
            }

            if (!$module->isEnabled()) {
                continue;
            }

            return $module;
        }

        return null;
    }
}
