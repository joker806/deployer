<?php
namespace Inspirio\Deployer;

use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Middleware\DeploymentMiddleware;
use Inspirio\Deployer\Middleware\SecurityMiddleware;
use Inspirio\Deployer\Middleware\StarterMiddleware;

class Container extends \Pimple
{
    public function __construct($appDir, $deployerDir, $configFile) {

        $this['dir.deployer'] = $deployerDir;
        $this['dir.root']     = $appDir;
        $this['dir.home']     = $this['dir.root'] .'/.deployer';
        $this['dir.template'] = $this['dir.deployer'] .'/template';
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
            /** @var $twig \Twig_Environment */
            $twig = $c['twig'];

            $renderer = new ModuleRenderer($twig);
            $twig->addFunction(new \Twig_SimpleFunction(
                    'render_module',
                    array($renderer, 'subRenderModule'),
                    array('is_safe' => array('all')))
            );

            return $renderer;
        });

        $this['action_runner'] = $this->share(function(Container $c) {
            return new ActionRunner();
        });

        $this['middleware.security'] = $this->share(function () {
            return new SecurityMiddleware(array(
                new SecurityModule\IpFilterSecurity(),
                new SecurityModule\HttpsSecurity(),
                new SecurityModule\StaticPassPhraseSecurity(),
            ));
        });

        $this['middleware.starter'] = $this->share(
            function (Container $c) {
                return new StarterMiddleware($c['app']);
            }
        );

        $this['middleware.module'] = $this->share(
            function (Container $c) {
                return new DeploymentMiddleware($c['app']);
            }
        );

        $this['app'] = $this->share(function(Container $c) {
            /** @var $config Config */
            $config = $c['config'];
            $class  = $config->get('app');

            return new $class($c['dir.root']);
        });

        $this['twig'] = $this->share(function(Container $c) {
            $loader = new \Twig_Loader_Filesystem(array(
                '/', // TODO temporary hack to allow absolute template paths
                $c['dir.template']
            ));

            $twig = new \Twig_Environment($loader, array(
                'cache'            => $c['dir.home'] . '/cache',
                'debug'            => true,
                'strict_valiables' => true,
            ));

            $twig->addGlobal('app', $c['app']);

            return $twig;
        });
    }
}
