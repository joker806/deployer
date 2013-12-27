<?php
namespace Inspirio\Deployer\StarterModule;

use Inspirio\Deployer\Application\ApplicationInterface;
use Inspirio\Deployer\Config;
use Symfony\Component\HttpFoundation\Request;

class ChoiceStarter extends AbstractStarterModule
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var StarterModuleInterface[]
     */
    private $children;

    /**
     * Constructor.
     *
     * @param string $title
     */
    public function __construct($title)
    {
        $this->title = $title;
    }

    /**
     * Creates new instance.
     *
     * @param string $title
     * @return ChoiceStarter
     */
    public static function create($title)
    {
        return new self($title);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns module name.
     *
     * @return string
     */
    public function getName()
    {
        return 'choice_starter';
    }

    /**
     * Adds child security module.
     *
     * @param StarterModuleInterface $child
     * @return $this
     */
    public function addChild(StarterModuleInterface $child)
    {
        $this->children[] = $child;
        return $this;
    }

    /**
     * Returns children list.
     *
     * @return StarterModuleInterface
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(Config $config)
    {
        foreach ($this->children as $child) {
            $child->setConfig($config);
        }
    }

    /**
     * Checks if application is started.
     *
     * @param ApplicationInterface $app
     *
     * @return bool
     */
    public function isStarted(ApplicationInterface $app)
    {
        foreach ($this->children as $child) {
            if ($child->isStarted($app)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Request $request)
    {
        $children = array();
        foreach ($this->children as $child) {
            $children[$child->getName()] = $child;
        }

        $hash             = substr(md5(implode('#', array_keys($children))), 0, 6);
        $defaultChildName = key($children);
        $activeChildKey   = "choice-{$hash}-child";
        $activeChildName  = $request->query->get($activeChildKey, $defaultChildName);

        if (!isset($children[$activeChildName])) {
            throw new \RuntimeException("Module {$activeChildName} not found.");
        }

        return array(
            'children'       => $children,
            'activeChildren' => $children[$activeChildName],
        );
    }
}
