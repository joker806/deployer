<?php
namespace Inspirio\Deployer\Module\Maintenance;

use Inspirio\Deployer\Module\AbstractActionModule;
use Inspirio\Deployer\Command\ProcessCommand;

class MaintenanceModule extends AbstractActionModule
{
	/**
	 * {@inheritdoc}
	 */
	public function getTitle()
	{
		return '<i class="icon-wrench"></i> Maintenance';
	}

    /**
     * {@inheritdoc}
     */
    protected function getSections()
    {
        return array(
            'permissions' => 'File permissions',
            'wipe'        => 'Project wipe',
        );
    }

    /**
     * Makes all files/dir writable by all users.
     *
     */
    public function makeAllFilesWritableAction()
    {
        $cmd = new ProcessCommand($this->projectDir);
        $cmd->runCmd('find . -type d -exec chmod 777 {} \;');
        $cmd->runCmd('find . -type f -exec chmod 666 {} \;');

        echo 'done'.PHP_EOL;
    }

    /**
     * Cleanups filesystem permissions.
     *
     */
    public function cleanFilePermissionsAction()
    {
        $cmd = new ProcessCommand($this->projectDir);
        $cmd->runCmd('find . -type d -exec chmod 775 {} \;');
        $cmd->runCmd('find . -type f -exec chmod 664 {} \;');

        $cmd->runCmd('find sites/*/public/repository -type d -exec chmod 777 {} \;');
        $cmd->runCmd('find sites/*/public/repository -type f -exec chmod 666 {} \;');

        echo 'done'.PHP_EOL;
    }

    /**
     * Wipes whole project.
     *
     */
    public function wipeProjectAction()
    {
        $cmd = new ProcessCommand($this->projectDir);
        $cmd->runCmd(
            'find .'.
            ' ! -name deployer.phar.php'.
            ' ! -name deployer.yml'.
            ' ! -name .composer'.
            ' -delete'
        );

        echo 'done'.PHP_EOL;
    }
}
