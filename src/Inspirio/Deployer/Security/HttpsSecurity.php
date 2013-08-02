<?php
namespace Inspirio\Deployer\Security;

use Inspirio\Deployer\Config\Config;
use Inspirio\Deployer\Config\ConfigAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpsSecurity implements SecurityModuleInterface, ConfigAware
{
    /**
     * @var bool
     */
    protected $require = false;

    /**
     * @var bool
     */
    protected $redirect = false;

    /**
     * {@inheritdoc}
     */
    public function setConfig(Config $config)
    {
        $https = $config->get('security', 'https');

        if ($https === null) {
            return;
        }

        if (is_array($https)) {
            if (isset($https['redirect'])) {
                $this->redirect = (bool)$https['redirect'];
            }

            if (isset($https['require'])) {
                $this->require = (bool)$https['require'];
            }

            return;
        }

        if ($https) {
            $this->require = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function authorize(Request $request)
    {
        if (!$request->isSecure() && $this->redirect) {
            if (($qs = $request->getQueryString()) !== null) {
                $qs = '?'.$qs;
            }

            return new Response('301 Moved Permanently', 301, array(
                'Location' => 'https://'.$request->getHttpHost().$request->getBaseUrl().$this->$request().$qs
            ));
        }

        if (!$request->isSecure() && $this->require) {
            return false;
        }

        return true;
    }
}
