<?php
namespace Inspirio\Deployer\View;

interface ViewAware
{
    /**
     * Sets view renderer instance.
     *
     * @param View $view
     */
    public function setView(View $view);
}
