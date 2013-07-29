<?php
namespace Inspirio\Deployer\Module\Deployment;

use Inspirio\Deployer\Module\ActionModuleBase;
use Inspirio\Deployer\Command\ComposerCommand;
use Inspirio\Deployer\Command\SubversionCommand;
use Inspirio\Deployer\Command\SymfonyCommand;

class DeploymentModule extends ActionModuleBase
{
    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return '<i class="icon-cloud-download"></i> Deployment';
    }

    /**
     * {@inheritdoc}
     */
    protected function getSections()
    {
        return array(
            'checkout' => 'Checkout',
            'update'   => 'Update',
        );
    }

    /**
     * Returns 'checkout' template data.
     *
     * @return bool|array
     */
    protected function renderCheckout()
    {
        if ($this->findAppFile('.svn')) {
            return false;
        }

        return array(
            'repoUrl'   => 'https://svn.inspirio.cz/inspirio/projects/',
            'env'       => 'prod',
            'revision'  => 'HEAD',
        );
    }

    /**
     * Returns 'update' template data.
     *
     * @return bool|array
     */
    protected function renderUpdate()
    {
        if (!$this->findAppFile('.svn')) {
            return false;
        }

        return array(
            'revison' => 'HEAD',
        );
    }

    /**
     * Checkout action.
     *
     * @param string $repoUrl
     * @param string $revision
     * @param string $env
     * @return bool
     */
    public function checkoutAction($repoUrl, $revision, $env)
    {
        // CHECKOUT
        $subversion = new SubversionCommand($this->projectDir);

        $configurator = $this->getCommandConfigurator();
        $configurator->configureSubversion($subversion, 'herkules');

        $result = $subversion->checkout($repoUrl, $revision);

        if (!$result) {
            return false;
        }

        $steps   = array();
        $feature = $this->getFeatureDetector();

        // SETUP PARAMETERS
        if ($feature->isSymfony()) {
            $symfony = new SymfonyCommand($this->projectDir);

            $steps[] = array($symfony, 'switchParameters', array($env));

        } elseif ($feature->hasEnvConfigs()) {

            // TODO
        }

        // COMPOSER UPDATE
        if ($feature->hasComposer()) {
            $composer = new ComposerCommand($this->projectDir);

            $steps[]  = array($composer, 'downloadComposer');
            $steps[]  = array($composer, 'install');
        }

        if (isset($symfony)) {
            $steps[] = array($symfony, 'rebuildApplication');
        }

        return $this->runBulkCommand($steps);
    }

    /**
     * Update action.
     *
     * @param string $revision
     * @return bool
     */
    public function updateAction($revision)
    {
        $subversion = new SubversionCommand($this->projectDir);

        $configurator = $this->getCommandConfigurator();
        $configurator->configureSubversion($subversion);

        $steps   = array(
            array($subversion, 'update', array($revision))
        );

        $feature = $this->getFeatureDetector();

        if ($feature->hasComposer()) {
            $composer = new ComposerCommand($this->projectDir);
            $steps[]  = array($composer, 'install');
        }

        if ($feature->isSymfony()) {
            $symfony = new SymfonyCommand($this->projectDir);
            $steps[] = array($symfony, 'rebuildApplication');
        }

        return $this->runBulkCommand($steps);
    }
}
