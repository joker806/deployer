<?php
namespace Inspirio\Deployer\DeploymentModule\Info;

use Inspirio\Deployer\DeploymentModule\AbstractDeploymentModule;
use Inspirio\Deployer\Command\SubversionCommand;

class InfoModule extends AbstractDeploymentModule
{
	/**
	 * {@inheritdoc}
	 */
	public function getTitle()
	{
		return '<i class="icon-info-sign"></i> Info';
	}

    /**
     * List of module sections.
     *
     * @return array
     */
    protected function getSections()
    {
        return array(
            'info' => 'Info',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return (bool)$this->app->findFile('.svn');
    }

    /**
	 * Returns 'info' template data.
     *
	 */
	public function renderInfo()
	{
		$data = array();

		if (file_exists($this->projectDir .'/.svn')) {
			$subversion = new SubversionCommand($this->projectDir);
			$subversion->setAutoFlush(false);
			$subversion->setShowCmd(false);

			ob_start();
			$subversion->info();
			$svnInfo = ob_get_clean();

			$data['svnInfo'] = $this->parseSvnInfo($svnInfo);

			ob_start();
			$subversion->status();
			$data['svnInfo']['status'] = ob_get_clean();
		}

		return $data;
	}

	/**
	 * Parses SVN info.
	 *
	 * @param string $svnInfo
	 * @return array
	 */
	private function parseSvnInfo($svnInfo)
	{
		$data = array();
		$data['repositoryUrl'] = self::pregExtract('/^URL: (.+)$/m', $svnInfo, 1);
		$data['revision']      = self::pregExtract('/^Revision: (\d+)$/m', $svnInfo, 1);
		$data['lastChange']    = self::pregExtract('/^Last Changed Author: (.+)$/m', $svnInfo, 1) .' '.
		                         self::pregExtract('/^Last Changed Date: (.+)$/m', $svnInfo, 1);

		return $data;
	}

	/**
	 * Extracts string part using regular expression.
	 *
	 * @param string $pattern
	 * @param string $subject
	 * @param int $item
	 * @return string|null
	 */
	private static function pregExtract($pattern, $subject, $item)
	{
		if (preg_match($pattern, $subject, $match)) {
			if (isset($match[$item])) {
				return $match[$item];
			}
		}

		return null;
	}
}
