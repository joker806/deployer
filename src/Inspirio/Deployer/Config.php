<?php
namespace Inspirio\Deployer;

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
     * Returns the config value or the default value when config does not exist.
     *
     * @param string $key,...
     *
     * @return mixed|null
     */
    public function getDefault($key, $defalt = null)
    {
        $keys   = func_get_args();
        $config = $this->config;

        foreach ($keys as $key) {
            if (!isset($config[$key])) {
                return $defalt;
            }

            $config = $config[$key];
        }

        return $config;
    }

    /**
     * Returns config value.
     *
     * @param string $key,...
     *
     * @throws \OutOfBoundException
     * @return mixed
     */
    public function get($key)
    {
        $keys   = func_get_args();
        $config = $this->config;

        foreach ($keys as $key) {
            if (!isset($config[$key])) {
                throw new \OutOfBoundException(sprintf('Config item \'%s\' does\'t exist.', implode('.', $keys)));
            }

            $config = $config[$key];
        }

        return $config;
    }
}
