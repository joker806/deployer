<?php
namespace Inspirio\Deployer\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware implements MiddlewareInterface
{
    /**
     * @var SecurityModuleInterface[]
     */
    private $modules;

    /**
     * Constructor.
     *
     * @param SecurityModuleInterface[] $modules
     */
    function __construct(array $modules)
    {
        $this->modules = $modules;
    }

    /**
     * {@inheritdoc}
     */
    public function interceptRequest(Request $request, $moduleName = null)
    {
        foreach ($this->modules as $module) {
            if ($module->isAuthorized($request)) {
                continue;
            }

            if ($moduleName === null) {
                return new ModuleRedirectResponse($module->getName());
            }

            if ($module->getName() === $moduleName) {
                return $module;
            }

            return new Response('401 Unauthorized', 401);
        }

        return null;
    }
}
