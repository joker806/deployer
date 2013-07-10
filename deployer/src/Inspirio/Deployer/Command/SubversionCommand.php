<?php
namespace Inspirio\Deployer\Command;

use Symfony\Component\Process\ProcessBuilder;

/**
 * Subversion command.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
class SubversionCommand extends ProcessCommand
{
	/**
	 * @var string|null
	 */
	private $username = null;

	/**
	 * @var string|null
	 */
	private $password = null;

	/**
	 * @var array
	 */
	private $urlCache = array();

	/**
	 * Setups the default login credentials.
	 *
	 * @param string|null $username
	 * @param string|null $password
	 */
	public function setDefaultCredentials($username = null, $password = null)
	{
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * Runs SVN checkout.
	 *
	 * @param string $repoUrl
	 * @param string $revision
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
	public function checkout($repoUrl, $revision = null, $username = null, $password = null)
	{
		$builder = $this->createSubversionProcessBuilder('checkout');
		$this->addRevision($builder, $revision);
		$this->addAuthentication($builder, $username, $password);

		$builder->add($repoUrl);
		$builder->add('.');

		return $this->run($builder->getProcess());
	}

	/**
	 * Runs SVN update.
	 *
	 * @param string $revision
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
	public function update($revision = null, $username = null, $password = null)
	{
		$builder = $this->createSubversionProcessBuilder('update');
		$this->addRevision($builder, $revision);
		$this->addAuthentication($builder, $username, $password);

		return $this->run($builder->getProcess());
	}

	/**
	 * Runs SVN info.
	 *
	 * @return bool
	 */
	public function info()
	{
		$builder = $this->createSubversionProcessBuilder('info');

		return $this->run($builder->getProcess());
	}

	/**
	 * Runs SVN status.
	 *
	 * @return bool
	 */
	public function status()
	{
		$builder = $this->createSubversionProcessBuilder('status');

		return $this->run($builder->getProcess());
	}

	/**
	 * Runs SVN revert.
	 *
	 * @param bool $recursive
	 * @return bool
	 */
	public function revert($recursive = true)
	{
		$builder = $this->createSubversionProcessBuilder('revert')
			->add('.');

		if ($recursive) {
			$builder->add('-R');
		}

		return $this->run($builder->getProcess());
	}

	/**
	 * Returns repository URL.
	 *
	 * @param string $dir
	 * @return string|null
	 */
	public function getRepositoryUrl($dir = null)
	{
		if ($dir === null) {
			$dir = $this->workingDir;
		}

		if (array_key_exists($dir, $this->urlCache)) {
			return $this->urlCache[$dir];
		}

		if (!file_exists($dir .'/.svn')) {
			return null;
		}

		$builder = $this->createSubversionProcessBuilder('info');
		$process = $builder->getProcess();
		$process->setWorkingDirectory($dir);
		$process->run();

		$output = $process->getOutput();

		if (preg_match('/^Repository Root: (.+)$/m', $output, $match)) {
			$url = $match[1];
		} else {
			$url = null;
		}

		return $this->urlCache[$dir] = $url;
	}

	/**
	 * Creates process-builder for a Subversion command.
	 *
	 * @param string $cmd
	 * @return ProcessBuilder
	 */
	protected function createSubversionProcessBuilder($cmd)
	{
		return $this->createProcessBuilder()
			->setEnv('LC_CTYPE', 'cs_CZ.UTF-8')
			->add('svn')
			->add($cmd)
			->add('--non-interactive')
			->add('--trust-server-cert')
			->add('--no-auth-cache')
		;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function outputCmd($cmd)
	{
		$cmd = preg_replace("/--password=(?:[^'\\ ]|\\ .)*/", '--password=********', $cmd);

		parent::outputCmd($cmd);
	}

	/**
	 * Adds authentication parameters.
	 *
	 * @param ProcessBuilder $builder
	 * @param string|null    $username
	 * @param string|null    $password
	 */
	protected function addAuthentication(ProcessBuilder $builder, $username = null, $password = null)
	{
		$username = $this->getUsername($username);
		$password = $this->getPassword($password);

		if ($username !== null) {
			$builder->add('--username='.$username);
		}

		if ($password !== null) {
			$builder->add('--password='.$password);
		}
	}

	/**
	 * Adds authentication parameters.
	 *
	 * @param ProcessBuilder $builder
	 * @param string $revision
	 */
	protected function addRevision(ProcessBuilder $builder, $revision = null)
	{
		if ($revision !== null) {
			$builder->add('--revision='.$revision);
		}
	}

	/**
	 * Returns username with the default fallback.
	 *
	 * @param string|null $username
	 * @return string|null
	 */
	protected function getUsername($username)
	{
		return $username !== null ? $username : $this->username;
	}

	/**
	 * Returns password with the default fallback.
	 *
	 * @param string|null $password
	 * @return string|null
	 */
	protected function getPassword($password)
	{
		return $password !== null ? $password : $this->password;
	}

}
