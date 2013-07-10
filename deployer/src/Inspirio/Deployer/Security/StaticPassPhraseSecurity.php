<?php
namespace Inspirio\Deployer\Security;

use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Config\ConfigAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StaticPassPhraseSecurity extends PassPhraseSecurity
{
    protected $phrase = false;

    /**
     * {@inheritdoc}
     */
    public function setConfig(Config $config)
    {
        parent::setConfig($config);

        $phrase = $config->get('security', 'passPhrase');

        if ($phrase === false) {
            return;
        }

        if (!is_string($phrase)) {
            throw new \RuntimeException('Configuration security.passPhrase has to be string.');
        }

        $this->phrase = $phrase;
    }

    /**
     * {@inheritdoc}
     */
    protected function validatePassPhrase($phrase)
    {
        return $this->phrase === false || $phrase == $this->phrase;
    }
}
