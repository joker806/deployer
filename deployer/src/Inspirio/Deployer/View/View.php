<?php
namespace Inspirio\Deployer\View;

use string;

class View implements \ArrayAccess {

    /**
     * @var string[]
     */
    private $templateDir;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $templateFile;

    /**
     * @var null|string
     */
    private $decorator;

    /**
     * Constructor.
     *
     */
    public function __construct($templateDir)
    {
        $this->templateDir = $templateDir;
        $this->data        = array();
        $this->decorator   = null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \DomainException("Invalid data item '{$offset}' requested");
        }

        return $this->data[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Adds data.
     *
     * @param array $data
     */
    public function addData(array $data)
    {
        $this->data = $data + $this->data;
    }

    /**
     * Sets current template decorator.
     *
     * @param string $decorator
     */
    public function decorator($decorator)
    {
        $this->decorator = $decorator;
    }

    /**
     * Renders the template.
     *
     * @param string $templateName
     * @param array  $data
     * @return string
     */
    public function render($templateName, array $data = array())
    {
        $this->decorator    = null;

        $data += $this->data;

        $content = $this->doRender($templateName, $data);
        $content = $this->renderDecorator($content);

        return $content;
    }

    private function renderDecorator($content)
    {
        if (!$this->decorator) {
            return $content;
        }

        $decorator       = $this->decorator;
        $this->decorator = null;

        $data = $this->data;
        $data['subContent'] = $content;

        $content = $this->doRender($decorator, $data);
        $content = $this->renderDecorator($content);

        return $content;
    }

    /**
     * Renders the template.
     *
     * @param string $templateFile
     * @param array  $context
     * @return string
     */
    private function doRender($templateFile, array $context)
    {
        extract($context);

        ob_start();
        include $this->templateDir .'/'. $templateFile;
        return ob_get_clean();
    }
}
