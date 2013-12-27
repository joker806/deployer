<?php
namespace Inspirio\Deployer\SecurityModule;

use Inspirio\Deployer\Config\Config;
use Symfony\Component\HttpFoundation\Request;

class StaticPassPhraseSecurity extends AbstractPassPhraseSecurityModule
{
    protected $phrase = false;

    /**
     * {@inheritdoc}
     */
    public function setConfig(Config $config)
    {
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
    public function render(Request $request)
    {
        return array();
    }


    /**
     * {@inheritdoc}
     */
    protected function validatePassPhrase($phrase)
    {
        return $this->phrase === false || $phrase == $this->phrase;
    }
}
