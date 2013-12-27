<?php
namespace Inspirio\Deployer;


use Inspirio\Deployer\Command\Command;
use Inspirio\Deployer\Command\MysqlCommand;
use Inspirio\Deployer\Command\SubversionCommand;
use Inspirio\Deployer\Config;
use Symfony\Component\Yaml\Yaml;

class CommandConfigurator
{
	/**
	 * @var string
	 */
	protected $projectDir;

	/**
	 * @var Config
	 */
	private $config;

    /**
     * Constructor.
     *
     * @param string $projectDir
     * @param Config $config
     */
	public function __construct($projectDir, Config $config)
	{
		$this->config = $config;

        $this->touchComposerConfig($projectDir);
	}

    /**
     * Checks if Composer config exists and creates default one when does not.
     *
     * @param string $projectDir
     */
    private function touchComposerConfig($projectDir)
    {
        if (file_exists($projectDir .'/.composer/config.json')) {
            return;
        }

        $config = $this->config->get('composer');

        if ($config === null) {
            return;
        }

        if (!file_exists($projectDir .'/.composer')) {
            mkdir($projectDir .'/.composer');
        }

        $config = array('config' => $config);
        $config = json_encode($config);

        file_put_contents($projectDir .'/.composer/config.json', $config);
    }

	/**
	 * Configures the Subversion command.
	 *
	 * @param SubversionCommand $cmd
	 * @param string            $repoName
	 */
	public function configureSubversion(SubversionCommand $cmd, $repoName = null)
	{
		$c = $this->config->get('subversion');

        if ($c === null) {
            return;
        }

		if ($repoName === null) {
			$repoUrl = $cmd->getRepositoryUrl();

			foreach ($c as $name => $config) {
				if ($config['url'] == $repoUrl) {
					$repoName = $name;
					break;
				}
			}
		}

		if (!$repoName || !isset($c[$repoName])) {
			return;
		}

		$c = $c[$repoName];

		$cmd->setDefaultCredentials(
			isset($c['username']) ? $c['username'] : null,
			isset($c['password']) ? $c['password'] : null
		);
	}

	/**
	 * Configures the Mysql command.
	 *
	 * @param MysqlCommand $cmd
	 * @param string       $server
	 */
	public function configureMysql(MysqlCommand $cmd, $server)
	{
        $c = $this->config->get('mysql', $server);

		if ($c === null) {
			return;
		}

		$cmd->setDefaultCredentials(
			isset($c['username']) ? $c['username'] : null,
			isset($c['password']) ? $c['password'] : null
		);
	}
}
