<?php
namespace Inspirio\Deployer\Bootstrap;

use Inspirio\Deployer\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubversionCheckout extends AbstractStarter
{
    /**
     * {@inheritdoc}
     */
    public function isReady()
    {
        return file_exists($this->app->getRootPath() .'/.svn');
    }

    /**
     * {@inheritdoc}
     */
    public function render(Request $request, View $view)
    {
        if ($this->app->findFile('.svn')) {
            return null;
        }

        return new Response('aa');
    }
}
