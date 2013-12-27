<?php
namespace Inspirio\Deployer;

abstract class AbstractModule implements ModuleInterface
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
     * {@inheritdoc}
     */
    public function getName()
    {
        if (!$this->name) {
            $name = get_class($this);

            if (($slashPos = strrpos($name, '\\')) !== false) {
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
}
