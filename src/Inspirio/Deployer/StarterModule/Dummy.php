<?php
namespace Inspirio\Deployer\StarterModule;

use Inspirio\Deployer\Application\ApplicationInterface;
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
    public function render(Request $request)
    {
        return array();
    }
}