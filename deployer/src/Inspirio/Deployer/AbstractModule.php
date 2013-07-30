<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\View\View;
use Inspirio\Deployer\View\ViewAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractModule implements ModuleInterface, ViewAware
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
     * @var View
     */
    protected $view;

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
    public function setView(View $view)
    {
        $this->view = $view;
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
     * Creates a response containing a rendered template.
     *
     * @param $template
     * @param array $data
     * @return Response
     */
    protected function createTemplateResponse($template, array $data = array())
    {
        $content = $this->view->render($template, $data);

        return new Response($content);
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
