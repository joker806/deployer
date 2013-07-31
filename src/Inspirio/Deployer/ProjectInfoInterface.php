<?php
namespace Inspirio\Deployer;

interface ProjectInfoInterface {

    /**
     * Returns project name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns project version.
     *
     * @return string
     */
    public function getVersion();
}
