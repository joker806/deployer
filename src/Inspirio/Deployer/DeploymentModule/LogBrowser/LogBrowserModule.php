<?php
namespace Inspirio\Deployer\DeploymentModule\LogBrowser;

use Inspirio\Deployer\Module\Deployment\AbstractDeploymentModule;

class LogBrowserModule extends AbstractDeploymentModule
{
    const SERVER_LOG_PATH = '../statistics/logs';
    const APPLICATION_LOG_PATH = '';

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return '<i class="icon-list"></i> Log browser';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSections()
    {
        return array(
            'server'      => 'Server',
            'application' => 'Application',
        );
    }

    /**
     * Returns 'server' template data.
     *
     * @return bool|array
     */
    protected function renderServer()
    {
        if (file_exists($this->projectDir .'/.svn')) {
            return false;
        }

        return $this->renderTemplate('log');
    }

    /**
     * Returns 'application' template data.
     *
     * @return bool|array
     */
    protected function renderApplication()
    {
        if (file_exists($this->projectDir .'/.svn')) {
            return false;
        }

        return $this->renderTemplate('log');
    }

    /**
     * Load log action.
     *
     * @param string $file
     * @return bool
     */
    public function loadLogAction($file)
    {
        $file = $this->projectDir .'/'. $file;

        if (!is_file($file)) {

        }

        readfile($file);
    }
}
