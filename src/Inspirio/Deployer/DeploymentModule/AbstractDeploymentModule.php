<?php
namespace Inspirio\Deployer\DeploymentModule;

use Inspirio\Deployer\AbstractModule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Base action implementation.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
abstract class AbstractDeploymentModule extends AbstractModule implements DeploymentModuleInterface
{
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
    public function getName()
    {
        if (!$this->name) {
            $name = $this->getReflection()->getShortName();
            $name = substr($name, 0, -6); // strip the 'Action' suffix
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
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function handleRequest(Request $request)
    {
        if ($request->isMethod('post')) {
            if (!$request->isXmlHttpRequest()) {
                throw new \Exception('Handling of non-ajax POST requests is not implemented yet');
            }

            $args = $request->request->all();

            if (!isset($args['run'])) {
                throw new \LogicException("No action specified (missing 'run' argument)");
            }

            $action = $args['run'];
            unset($args['run']);

            return $this->runAction($action, $args);
        }

        $this->view->setDefaultData(
            array(
                'app'     => $this->app,
                'module'  => $this,
                'request' => $request,
            )
        );

        $this->view->pushDecorator('page.html.php');
        $this->view->pushDecorator('module/decorator.html.php');

        $content = $this->render($request);

        if (is_scalar($content)) {
            return new Response($content);
        }

        $className = get_class($this);
        $className = substr($className, strrpos($className, '\\') + 1);
        $template  = 'module/' . lcfirst($className) . '.html.php';

        $content = $this->view->render($template, $content);
        return new Response($content);
    }

    /**
     * {@inheritdoc}
     */
    public function render(Request $request)
    {
        $sectionName = $request->query->get('section');

        return $this->renderSection($sectionName);
    }

    /**
     * List of module sections.
     *
     * @return array
     */
    abstract protected function getSections();

    /**
     * Runs module action.
     *
     * @param string $action
     * @param array $args
     * @return StreamedResponse
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    private function runAction($action, array $args)
    {
        $actionMethod = null;

        try {
            $actionMethod = new \ReflectionMethod(get_class($this), $action . 'Action');
        } catch (\ReflectionException $e) {
        }

        if (!$actionMethod || !$actionMethod->isPublic()) {
            throw new \LogicException("Module '{$this->getName()}' has no action '{$action}'");
        }

        $realArgs = array();

        foreach ($actionMethod->getParameters() as $i => $param) {
            $name = $param->getName();

            if (array_key_exists($name, $args)) {
                $realArgs[$i] = $args[$name];

            } elseif ($param->isOptional()) {
                $realArgs[$i] = $param->getDefaultValue();

            } else {
                throw new \InvalidArgumentException("Action '{$this->getName(
                )}' is missing mandatory '{$name}' parameter value");
            }
        }

        $module = $this;
        return new StreamedResponse(function () use ($actionMethod, $module, $realArgs) {
            $actionMethod->invokeArgs($module, $realArgs);
        });
    }

    /**
     * Renders module section.
     *
     * @param Request $request
     * @param string|null $sectionName
     * @return Response
     *
     * @throws \LogicException
     */
    private function renderSection(Request $request, $sectionName = null)
    {
        $sections = $this->getSections();

        if ($sectionName === null) {
            $sectionName = reset(array_keys($sections));
        }

        if (!isset($sections[$sectionName])) {
            return new Response('404 Not Found', 404);
        }

        $sectionRenderer = 'render' . ucfirst($sectionName);

        if (!method_exists($this, $sectionRenderer)) {
            throw new \LogicException("Missing section '{$sectionName}' renderer in '{$this->getName()}' module");
        }

        $sectionData = call_user_func(array($this, $sectionRenderer), $request);

        // returned string - custom rendered section
        if (is_string($sectionData)) {
            return $sectionData;
        }

        if ($sectionData === true) {
            $sectionData = array();
        }

        $sectionData['sections'] = $sections;

        $content = $this->view->render('module/layout.html.php', $sectionData);
        return new Response($content);
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
