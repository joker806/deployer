<?php
namespace Inspirio\Deployer\StarterModule;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Module\Starter\AbstractStarterModule;
use Symfony\Component\HttpFoundation\Request;

class Dummy extends AbstractStarterModule
{
    /**
     * {@inheritdoc}
     */
    public function isStarted(ApplicationInterface $app)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function render(\Twig_Environment $twig, Request $request)
    {
        return array();
    }
}
