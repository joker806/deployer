<?php
namespace Inspirio\Deployer\StarterModule;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\RenderableModuleInterface;
use Symfony\Component\HttpFoundation\Request;

class Dummy extends AbstractStarterModule implements RenderableModuleInterface
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
