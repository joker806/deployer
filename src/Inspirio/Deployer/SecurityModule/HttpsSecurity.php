<?php
namespace Inspirio\Deployer\SecurityModule;

use Inspirio\Deployer\Config;
use Inspirio\Deployer\Module\Security\AbstractSecurityModule;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class HttpsSecurity extends AbstractSecurityModule
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

        $this->configLoaded = true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthorized(Request $request)
    {
        if (!$request->isSecure() && $this->redirect) {
            if (($qs = $request->getQueryString()) !== null) {
                $qs = '?'.$qs;
            }

            return new RedirectResponse(
                'https://' . $request->getHttpHost() . $request->getBaseUrl() . $request->getPathInfo() . $qs,
                301
            );
        }

        if (!$request->isSecure() && $this->require) {
            return false;
        }

        return true;
    }
}
