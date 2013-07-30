<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\View\View;
use Symfony\Component\HttpFoundation\Request;

abstract class ModuleBase implements ModuleInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ApplicationInterface
     */
    protected $app;

    /**
     * @var CommandConfigurator
     */
    private $commandConfigurator = null;

    /**
     * @var FeatureDetector
     */
    private $featureDetector = null;

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
    public function setApp(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * Returns command-configurator instance.
     *
     * @return CommandConfigurator
     */
    protected function getCommandConfigurator()
    {
        if (!$this->commandConfigurator) {
            $this->commandConfigurator = new CommandConfigurator($this->app->getRootPath(), $this->config);
        }

        return $this->commandConfigurator;
    }

    /**
     * Returns project feature detector.
     *
     * @return FeatureDetector
     */
    protected function getFeatureDetector()
    {
        if (!$this->featureDetector) {
            $this->featureDetector = new FeatureDetector($this->app->getRootPath());
        }

        return $this->featureDetector;
    }

    /**
     * Runs commands in bulk.
     *
     * @param array $steps
     * @return bool
     */
    protected function runBulkCommand(array $steps)
    {
        foreach ($steps as $step) {
            if (!call_user_func_array(array($step[0], $step[1]), isset($step[2]) ? $step[2] : array())) {
                return false;
            }
        }

        return true;
    }
}
