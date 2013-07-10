<?php
namespace Inspirio\Deployer\Module;

use Inspirio\Deployer\CommandConfigurator;
use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Config\ConfigAware;
use Inspirio\Deployer\FeatureDetector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

/**
 * Base action implementation.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
abstract class Module implements ModuleInterface, ConfigAware
{
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
	 * @var \ReflectionObject
	 */
	private $reflection = null;

	/**
	 * @var string
	 */
	private $name = null;

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
	public function getName()
	{
		if (!$this->name) {
			$name = $this->getReflection()->getShortName();
			$name = substr($name, 0, -6); // strip the Action suffix
			$name = lcfirst($name);

			$this->name = $name;
		}

		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTitle()
	{
		$title = $this->getName();
		$title = preg_replace('/[A-Z]/', ' $0', $title);
		$title = ucfirst($title);

		return $title;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isEnabled()
	{
		return true;
	}

    /**
     * {@inheritdoc}
     */
    public function render(Request $request)
    {
        $sections = $this->getSections();
        $sectionContent = array();

        foreach ($sections as $name => $title) {
            $sectionRenderer = 'render'. ucfirst($name);

            if (!method_exists($this, $sectionRenderer)) {
                $sectionContent[$name] = $this->renderTemplate($name, array());
                continue;
            }

            $sectionData = call_user_func(array($this, $sectionRenderer), $request);

            // returned 0/null/false - section disabled
            if (!$sectionData && !is_array($sectionData)) {
                continue;
            }

            // returned string - custom rendered section
            if (is_string($sectionData)) {
                $sectionContent[$name] = $sectionData;
                continue;
            }

            if ($sectionData === true) {
                $sectionData = array();
            }

            $sectionContent[$name] = $this->renderTemplate($name, $sectionData);
        }

        return $this->doRenderTemplate(__DIR__ .'/baseTemplate.html.php', array(
            'sections'       => $sections,
            'sectionContent' => $sectionContent,
        ));
    }

    /**
     * List of module sections.
     *
     * @return array
     */
    abstract protected function getSections();

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
		$dirName      = dirname($this->getReflection()->getFileName());
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

	/**
	 * Returns object reflection.
	 *
	 * @return \ReflectionObject
	 */
	private function getReflection()
	{
		if (!$this->reflection) {
			$this->reflection = new \ReflectionObject($this);
		}

		return $this->reflection;
	}
}
