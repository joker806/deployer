<?php
namespace Inspirio\Deployer\Bootstrap;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubversionCheckout extends AbstractStarterModule
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return null|string|\Symfony\Component\HttpFoundation\Response
     */
    public function handleRequest(Request $request)
    {
        if ($this->app->findFile('.svn')) {
            return null;
        }

        return $this->createTemplateResponse('bootstrap/subversionCheckout.html.php');
    }
}
