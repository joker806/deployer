<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Config\ConfigAware;
use Inspirio\Deployer\View\ViewAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractModule implements ModuleInterface, ConfigAware, ViewAware
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $title;

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
     * {@inheritdoc}
     */
    public function getName()
    {
        if (!$this->name) {
            $name = get_class($this);

            if (($slashPos = strrpos($name, '/')) !== false) {
                $name = substr($name, $slashPos + 1);
            }

            $name = preg_replace('/[A-Z]/', '_$0', $name);
            $name = strtolower($name);

            $this->name = $name;
        }

        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        if (!$this->title) {
            $title = $this->getName();
            $title = preg_replace('/_([a-z])/', ' $1', $title);
            $title = ucfirst($title);

            $this->title = $title;
        }

        return $this->title;
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
