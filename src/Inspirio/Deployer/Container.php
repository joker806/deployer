<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Middleware\ModuleMiddleware;
use Inspirio\Deployer\Middleware\SecurityMiddleware;
use Inspirio\Deployer\Middleware\StarterMiddleware;

class Container extends \Pimple
{
    public function __construct($appDir, $deployerDir, $configFile) {

        $this['dir.deployer'] = $appDir;
        $this['dir.root']     = $deployerDir;
        $this['dir.home']     = $this['dir.root'] .'/.deployer';
        $this['config.file']  = $configFile;

        $this['config'] = $this->share(function(Container $c) {
            return new Config($c['config.file']);
        });

        $this['request_handler'] = $this->share(function(Container $c) {
            $deployer = new RequestHandler($c['module_renderer'], $c['action_runner']);

            $deployer
                ->addMiddleware($c['middleware.security'])
                ->addMiddleware($c['middleware.starter'])
                ->addMiddleware($c['middleware.module'])
            ;

            return $deployer;
        });

        $this['module_renderer'] = $this->share(function(Container $c) {
            return new ModuleRenderer($c['template']);
        });

        $this['action_runner'] = $this->share(function(Container $c) {
            return new ActionRunner();
        });

        $this['middleware.security'] = $this->share(
            function () {
                return new SecurityMiddleware(array(
                    new Security\IpFilterSecurity(),
                    new Security\HttpsSecurity(),
                    new Security\StaticPassPhraseSecurity(),
                ));
            }
        );

        $this['middleware.starter'] = $this->share(
            function (Container $c) {
                return new StarterMiddleware($c['app']);
            }
        );

        $this['middleware.module'] = $this->share(
            function (Container $c) {
                return new ModuleMiddleware($c['app']);
            }
        );

        $this['app'] = $this->share(function(Container $c) {
            /** @var $config Config */
            $config = $c['config'];
            $class  = $config->get('app');

            return new $class($c['dir.root']);
        });

        $this['template'] = $this->share(function(Container $c) {
            $loader = new \Twig_Loader_Filesystem($c['dir.deployer']);

            $twig = new \Twig_Environment($loader, array(
                'cache' => $c['dir.home'] .'/cache',
            ));

            $twig->addGlobal('app', $c['app']);

            return $twig;
        });
    }
}
