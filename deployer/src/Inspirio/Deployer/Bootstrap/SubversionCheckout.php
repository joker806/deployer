<?php
namespace Inspirio\Deployer\Bootstrap;

use Inspirio\Deployer\Application\ApplicationInterface;

class SubversionCheckout implements StarterModuleInterface {

    /**
     * @var ApplicationInterface
     */
    private $app;

    /**
     * {@inheritdoc}
     */
    function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function isReady()
    {
        return file_exists($this->app->getRootPath() .'/.svn');
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrapApp()
    {

    }
}
