<?php
    $this->decorator('page.html.php');
    $this['bodyClass'] = 'starter-module';
?>

<div class="container">
    <h1>Project initialization</h1>

    <?= $subContent ?>

    <div id="console-output"></div>
</div>
