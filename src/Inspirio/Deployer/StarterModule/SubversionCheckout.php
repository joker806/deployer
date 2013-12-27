<?php
namespace Inspirio\Deployer\StarterModule;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Command\SubversionCommand;
use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\ConfigAwareModuleInterface;
use Inspirio\Deployer\RenderableModuleInterface;
use Symfony\Component\HttpFoundation\Request;

class SubversionCheckout extends AbstractStarterModule implements RenderableModuleInterface, ConfigAwareModuleInterface
{

    /**
     * @var Config
     */
    private $config;

    /**
     * {@inheritdoc}
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted(ApplicationInterface $app)
    {
        return (bool)$app->findFile('.svn');
    }

    /**
     * {@inheritdoc}
     */
    public function render(Request $request)
    {
        $repos = $this->config['subversion'];
        $repos = is_array($repos) ? array_keys($repos) : array();

        return array(
            'repos'    => $repos,
            'repoName' => '',
            'repoPath' => '',
            'revision' => 'HEAD',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function startupAction(array $data)
    {
//        $repoName = $data['']
//
//        $repoName, $repoPath, $revision

        $subversion = new SubversionCommand($this->app->getRootPath());

        $configurator = $this->getCommandConfigurator();
        $configurator->configureSubversion($subversion, $repoName);

        $repoConfig = $this->config->get('subversion', $repoName);

        $subversion->checkout($repoConfig['url'] . '/' . $repoPath, $revision);
    }
}
