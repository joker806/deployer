<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Module\ModuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActionRunner {

    /**
     * Runs module action.
     *
     * @param ModuleInterface $module
     * @param Request         $request
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @return StreamedResponse
     */
    public function runAction(ModuleInterface $module, Request $request)
    {
        if (!$request->request->has('run')) {
            throw new \InvalidArgumentException("Missing 'run' parameter");
        }

        $action = $request->request->get('run');
        $request->request->remove('run');

        $actionMethodName = $action . 'Action';
        $actionMethod     = null;

        try {
            $actionMethod = new \ReflectionMethod($module, $actionMethodName);
        } catch (\ReflectionException $e) {
            // handled below
        }

        if (!$actionMethod || !$actionMethod->isPublic()) {
            $moduleName = get_class($module);
            throw new \LogicException("Starter module '{$moduleName}' is missing '{$actionMethodName}' method");
        }

        $requestArgs = $request->request->all();
        $methodArgs  = array();

        foreach ($actionMethod->getParameters() as $i => $param) {
            $name = $param->getName();

            if (array_key_exists($name, $requestArgs)) {
                $methodArgs[$i] = $requestArgs[$name];

            } elseif ($param->isOptional()) {
                $methodArgs[$i] = $param->getDefaultValue();

            } else {
                $moduleName = get_class($this);
                throw new \InvalidArgumentException("Missing '{$moduleName}' starter module 'startup' method '{$name}' parameter value");
            }
        }

        return new StreamedResponse(function () use ($actionMethod, $module, $methodArgs) {
            $actionMethod->invokeArgs($module, $methodArgs);
        });
    }
}
