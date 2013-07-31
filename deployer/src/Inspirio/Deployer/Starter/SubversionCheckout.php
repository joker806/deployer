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
        $repos = $this->config->get('subversion');
        $repos = is_array($repos) ? array_keys($repos) : array();

        return array(
            'repos'    => $repos,
            'repoName' => '',
            'repoPath' => '',
            'revision' => 'HEAD',
        );
    }

    public function startup($repoName, $repoPath, $revision)
    {
        var_dump(func_get_args());
    }
}
