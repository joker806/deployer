<?php
namespace Inspirio\Deployer\Module\Security;

use Inspirio\Deployer\Module\AbstractModule;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractSecurityModule extends AbstractModule implements SecurityModuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTemplatePath()
    {
        return 'security';
    }

    /**
     * {@inheritdoc}
     */
    public function render(\Twig_Environment $twig, Request $request)
    {
        return $twig->render('security/notAuthorized.twig');
    }
}
