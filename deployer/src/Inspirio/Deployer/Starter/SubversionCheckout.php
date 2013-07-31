<?php
namespace Inspirio\Deployer\Starter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubversionCheckout extends AbstractStarterModule
{
    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return (bool)$this->app->findFile('.svn');
    }

    /**
     * {@inheritdoc}
     */
    public function render(Request $request)
    {
        return array(
            'repoUrl'  => '',
            'revision' => 'HEAD',
        );
    }
}
