<?php
namespace Inspirio\Deployer\Starter;

use Inspirio\Deployer\AbstractModule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class AbstractStarterModule extends AbstractModule implements StarterModuleInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function handleRequest(Request $request)
    {
        if  ($this->isStarted()) {
            return null;
        }

        if ($request->isMethod('post')) {
            if (!$request->isXmlHttpRequest()) {
                throw new \Exception('Handling of non-ajax POST requests is not implemented yet');
            }

            return $this->runStartup($request);
        }

        $response = $this->render($request);

        if ($response instanceof Response) {
            return $response;
        }

        if (is_scalar($response)) {
            return new Response($response);
        }

        $className = get_class($this);
        $className = substr($className, strrpos($className, '\\') + 1);
        $template  = 'starter/'. lcfirst($className) .'.html.php';

        return $this->createTemplateResponse($template, $response);
    }

    /**
     * Checks if application is started.
     *
     * @return bool
     */
    abstract protected function isStarted();

    /**
     * @param Request $request
     * @return string|array|Response
     */
    abstract protected function render(Request $request);

    /**
     * Runs module action.
     *
     * @param Request $request
     * @return StreamedResponse
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    private function runStartup(Request $request)
    {
        $actionMethod = null;

        try {
            $actionMethod = new \ReflectionMethod(get_class($this), 'startup');
        } catch (\ReflectionException $e) {}

        if (!$actionMethod || !$actionMethod->isPublic()) {
            $moduleName = get_class($this);
            throw new \LogicException("Starter module '{$moduleName}' is missing 'startup' method");
        }

        $args     = $request->request->all();
        $realArgs = array();

        foreach ($actionMethod->getParameters() as $i => $param) {
            $name = $param->getName();

            if (array_key_exists($name, $args)) {
                $realArgs[$i] = $args[$name];

            } elseif ($param->isOptional()) {
                $realArgs[$i] = $param->getDefaultValue();

            } else {
                $moduleName = get_class($this);
                throw new \InvalidArgumentException("Missing '{$moduleName}' starter module 'startup' method '{$name}' parameter value");
            }
        }

        $module = $this;
        return new StreamedResponse(function() use ($actionMethod, $module, $realArgs) {
            $actionMethod->invokeArgs($module, $realArgs);
        });
    }
}
