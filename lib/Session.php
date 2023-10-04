<?php

/**
 * Class Session
 *
 * This class provides methods for handling user sessions.
 */
class Session {
    /**
     * Initialize the session.
     */
    public static function init(){
        session_start();
    }

    /**
     * Set a session variable.
     *
     * @param string $key The key of the session variable.
     * @param mixed $val The value to set for the session variable.
     */
    public static function set($key, $val){
        $_SESSION[$key] = $val;
    }

    /**
     * Get the value of a session variable.
     *
     * @param string $key The key of the session variable to retrieve.
     *
     * @return mixed|false The value of the session variable if found, or false otherwise.
     */
    public static function get($key){
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }
        return false;
    }

}
?>
