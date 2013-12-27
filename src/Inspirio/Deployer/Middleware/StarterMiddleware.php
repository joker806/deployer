<?php
namespace Inspirio\Deployer\Middleware;

use Inspirio\Deployer\Application\ApplicationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StarterMiddleware implements MiddlewareInterface
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
        foreach ($this->app->getStarters() as $module) {
            if ($module->isStarted($this->app)) {
                continue;
            }

            if ($moduleName === null) {
                return new ModuleRedirectResponse($module->getName());
            }

            if ($module->getName() === $moduleName) {
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
