<?php
namespace Inspirio\Deployer\View;

class View
{

    /**
     * @var string[]
     */
    private $templateDir;

    /**
     * @var array
     */
    private $defaultData;

    /**
     * @var string
     */
    private $templateFile;

    /**
     * @var string[]
     */
    private $decorators;

    /**
     * Constructor.
     *
     */
    public function __construct($templateDir)
    {
        $this->templateDir = $templateDir;
        $this->defaultData = array();
        $this->decorators  = array();
    }

    /**
     * Sets view default data.
     *
     * @param array $data
     */
    public function setDefaultData(array $data)
    {
        $this->defaultData = $data;
    }

    /**
     * Adds default data item.
     *
     * @param string $key
     * @param mixed $value
     */
    public function addDefaultData($key, $value)
    {
        $this->defaultData[$key] = $value;
    }

    /**
     * Adds template decorator.
     *
     * @param string $decorator
     * @param array $data
     */
    public function pushDecorator($decorator, array $data = array())
    {
        $this->decorators[] = array($decorator, $data);
    }

    /**
     * Renders the template.
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    public function render($template, array $data = array())
    {
        $content = $this->doRender($template, $data + $this->defaultData);

        foreach (array_reverse($this->decorators) as $decorator) {
            list($template, $data) = $decorator;

            $data += $this->defaultData;
            $data['subContent'] = $content;

            $content = $this->doRender($template, $data);
        }

        return $content;
    }

    /**
     * Renders the template.
     *
     * @param string $templateFile
     * @param array $context
     * @return string
     */
    private function doRender($templateFile, array $context)
    {
        extract($context);

        ob_start();
        include $this->templateDir . '/' . $templateFile;

        return ob_get_clean();
    }
}
