<?php


/**
 * class Debug
 * 
 * This class provide necessary methods for debugging PHP Code
 */

 class Debug {
    public static function consoleLog(mixed $data) {
        ob_start();

        
        var_dump($data); 

   
        $log = ob_get_contents();
        ob_end_clean();

        ob_start();
        ?>
        <script>
            console.log(`<?php echo str_replace("\\","\\\\",$log); ?>`);
        </script>
        <?php
        $script = ob_get_contents();
        ob_end_clean();
        echo $script;
    }
}
