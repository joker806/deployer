<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Config\Config;
use Symfony\Component\HttpFoundation\Request;

abstract class ModuleBase {

    /**
     * @var string
     */
    protected $projectDir;

    /**
     * @var Config
     */
    protected $config;

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
    public function setProjectDir($projectDir)
    {
        $this->projectDir = $projectDir;
    }

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
    abstract public function render(Request $request);

    /**
     * Returns command-configurator instance.
     *
     * @return CommandConfigurator
     */
    protected function getCommandConfigurator()
    {
        if (!$this->commandConfigurator) {
            $this->commandConfigurator = new CommandConfigurator($this->projectDir, $this->config);
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
            $this->featureDetector = new FeatureDetector($this->projectDir);
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

    /**
     * Finds path to project file if file exists.
     *
     * @param string $file
     * @return string|null
     */
    protected function findFile($file)
    {
        return realpath($this->projectDir .'/'. $file) ?: null;
    }

    /**
     * Renders the action template.
     *
     * Template should be located in the action view directory.
     *
     * @param string $templateName
     * @param array  $data
     * @return string
     */
    protected function renderTemplate($templateName, array $data = array())
    {
        $templateFile = "{$dirName}/view/{$templateName}.html.php";

        $data['action']     = $this;
        $data['currentUrl'] = '?module='. $this->getName();
        $data['appDir'] = $this->projectDir;

        return $this->doRenderTemplate($templateFile, $data);
    }

    /**
     * Does the real template rendering.
     *
     * @param string $templateFile
     * @param array  $data
     * @return string
     */
    private function doRenderTemplate($templateFile, array $data)
    {
        extract($data);

        ob_start();
        include $templateFile;
        return ob_get_clean();
    }
}
