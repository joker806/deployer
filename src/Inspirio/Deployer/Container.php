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
        $this['dir.app']      = $deployerDir;
        $this['dir.home']     = $this['dir.app'] .'/.deployer';
        $this['config.file']  = $configFile;

        $this['config'] = $this->share(function(Container $c) {
            return new Config($c['config.file']);
        });

        $this['request_handler'] = $this->share(function(Container $c) {
            $deployer = new RequestHandler();

            $deployer
                ->addMiddleware($c['middleware.security'])
                ->addMiddleware($c['middleware.starter'])
                ->addMiddleware($c['middleware.module'])
            ;

            return $deployer;
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

            return new $class();
        });

        $this['template'] = $this->share(function(Container $c) {
            $loader = new \Twig_Loader_Filesystem($c['dir/deployer']);

            $twig = new \Twig_Environment($loader, array(
                'cache' => $c['home_dir'] .'/cache',
            ));

            $twig->addGlobal('app', $c['app']);

            return $twig;
        });
    }
}
