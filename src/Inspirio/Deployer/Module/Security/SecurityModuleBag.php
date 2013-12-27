<?php
namespace Inspirio\Deployer\Module\Security;

use Inspirio\Deployer\Module\AbstractModuleBag;
use Inspirio\Deployer\Module\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityModuleBag extends AbstractModuleBag
{
    /**
     * @param SecurityModuleInterface $module
     */
    protected function checkModule(Request $request, ModuleInterface $module, $moduleName)
    {
        if ($module->isAuthorized($request)) {
            return null;
        }

        if ($moduleName === null || $module->getName() === $moduleName) {
            return $module;
        }

        return new Response('401 Unauthorized', 401);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateLayout()
    {
        return 'securityLayout.twig';
    }
}
