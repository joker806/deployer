<?php
    $this->addDefaultData('bodyClass', 'action-module');
?>

<div class="container">
    <div class="masthead">
        <p class="pull-right muted"><?= $app->getRootPath() ?></p>
        <h3><a href="?"><?= $app->getProjectInfo()->getName() ?> <small><?= $app->getProjectInfo()->getVersion() ?></small></a></h3>
    </div>

    <hr>

    <div class="row-fluid">
        <div class="span3">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <?php
                    foreach ($app->getModules() as $name => $appModule) {
                        $class = ($appModule == $module) ? ' class="active"' : '';

                        echo "<li{$class}>";

                        if ($appModule->isEnabled()) {
                            echo "<a href=\"?module={$name}\">{$appModule->getTitle()}</a>";
                        } else {
                            echo $appModule->getTitle();
                        }

                        echo '</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>

        <div class="span9">
            <?= $subContent ?>
        </div>
    </div>
</div>
