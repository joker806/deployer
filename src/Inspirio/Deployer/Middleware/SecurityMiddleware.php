<?php
namespace Inspirio\Deployer\Middleware;

use Inspirio\Deployer\SecurityModule\SecurityModuleInterface;
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
     * Registers the security module.
     *
     * @param SecurityModuleInterface $module
     *
     * @return $this
     */
    public function registerModule(SecurityModuleInterface $module)
    {
        $this->modules[] = $module;
        return $this;
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

    /**
     * {@inheritdoc}
     */
    public function getTemplateLayout()
    {
        return 'securityLayout.twig';
    }
}
