<?php
namespace Inspirio\Deployer\SecurityModule;

use Inspirio\Deployer\Config;
use Inspirio\Deployer\Module\Security\AbstractSecurityModule;
use Symfony\Component\HttpFoundation\Request;

class IpFilterSecurity extends AbstractSecurityModule
{
    protected $allowedIps = false;

    /**
     * {@inheritdoc}
     */
    public function setConfig(Config $config)
    {
        $ipFilter = $config->get('security', 'ipFilter');

        if ($ipFilter === null || $ipFilter === false) {
            $this->allowedIps = false;

        } elseif (is_array($ipFilter)) {
            $this->allowedIps = $ipFilter;

        } else {
            $type = is_object($ipFilter) ? get_class($ipFilter) : gettype($ipFilter);
            throw new \RuntimeException("Configuration security.ipFilter has to be array, {$type} given.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthorized(Request $request)
    {
        if ($this->allowedIps === false) {
            return true;
        }

        return in_array($request->getClientIp(), $this->allowedIps);
    }
}
