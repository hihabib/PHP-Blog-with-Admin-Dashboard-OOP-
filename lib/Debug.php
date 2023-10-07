<?php


/**
 * class Debug
 * 
 * This class provide necessary methods for debugging PHP Code
 */

 class Debug {
    public static function consoleLog(mixed $data) {
        ob_start();
        ?>
        <script>
            console.log(`<?php echo var_dump($data); ?>`);
        </script>
        <?php
        $script = ob_get_contents();
        ob_end_clean();
        echo $script;
    }
}
