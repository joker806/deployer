<?php
namespace Inspirio\Deployer\Command;

use Symfony\Component\Process\ProcessBuilder;

/**
 * MySQL command.
 *
 * @author Josef Martinec <josef.martinec@inspirio.cz>
 */
class MysqlCommand extends ProcessCommand
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
	 * Switches the used parameters file.
	 *
	 * @param string      $databaseName
	 * @param string|null $username
	 * @param string|null $password
	 * @return bool
	 */
	public function dumpDatabase($databaseName, $username = null, $password = null)
	{
		$builder = $this->createProcessBuilder();
		$builder->add('mysqldump');
		$this->addAuthentication($builder, $username, $password);
		$builder->add($databaseName);

		return $this->run($builder->getProcess());
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
