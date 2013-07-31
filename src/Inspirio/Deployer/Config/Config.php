<?php
namespace Inspirio\Deployer\Config;


use Symfony\Component\Yaml\Yaml;

class Config
{
    /**
     * @var array
     */
    private $config;

    /**
     * Constructor.
     *
     * @param string $configFile
     */
    public function __construct($configFile)
    {
        $this->loadConfig($configFile);
    }

    /**
     * Loads the configuration file.
     *
     * @param string $configFile
     */
    private function loadConfig($configFile)
    {
        if (file_exists($configFile)) {
            $this->config = Yaml::parse(file_get_contents($configFile));

        } else {
            $this->config = array();
        }
    }

    /**
     * Returns config value or NULL when config does not exist..
     *
     * @param string $key,...
     * @return mixed|null
     */
    public function get($key)
    {
        $keys   = func_get_args();
        $config = $this->config;

        foreach ($keys as $key) {
            if (!isset($config[$key])) {
                return null;
            }

            $config = $config[$key];
        }

        return $config;
    }
}
