<?php
namespace Inspirio\Deployer\Module\Security;

use Inspirio\Deployer\Module\AbstractModuleBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property SecurityModuleInterface[] $modules
 */
class SecurityModuleBag extends AbstractModuleBag
{
    /**
     * {@inheritdoc}
     */
    public function pickModule(Request $request, $moduleName = null)
    {
        foreach ($this->modules as $module) {
            if ($module->isAuthorized($request)) {
                continue;
            }

            if ($moduleName === null || $module->getName() === $moduleName) {
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
