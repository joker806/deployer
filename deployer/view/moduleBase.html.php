<?php if (count($sections) > 1) { ?>
    <ul class="nav nav-tabs">
        <?php
            $isFirst = true;

            foreach($sections as $name => $title) {
                if(!isset($sectionContent[$name])) {
                    echo "<li class=\"disabled\"><a>{$title}</a></li>";

                } else {
                    $classes = array();

                    if($isFirst) {
                        $classes[] = 'active';
                        $isFirst = false;
                    }

                    if (count($classes) > 0) {
                        $class = ' class="'. implode(' ', $classes). '"';
                    } else {
                        $class = '';
                    }

                    echo "<li{$class}><a href=\"#section-{$name}\" data-toggle=\"tab\">{$title}</a></li>";
                }
            }
        ?>
    </ul>

    <div class="tab-content">
        <?php
        $isFirst = true;

        foreach($sections as $name => $title) {
            if (!isset($sectionContent[$name])) {
                continue;
            }

            $classes = array('tab-pane');

            if ($isFirst) {
                $classes[] = 'active';
                $isFirst = false;
            }

            $class = ' class="'. implode(' ', $classes). '"';

            echo "<div{$class} id=\"section-{$name}\">\n";
            echo $sectionContent[$name];
            echo "</div>\n";
        }
        ?>
    </div>

<?php
    } else {
        echo reset($sectionContent);
    }
?>

<div id="console-output"></div>
