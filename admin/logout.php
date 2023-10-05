<?php
// Check if the 'logout' parameter is set in the URL query string
if(isset($_GET['logout'])) {
    // Include the Session class for managing sessions
    include_once("../lib/Session.php");
    
    // Call the destroy method of the Session class to end the user's session
    Session::destroy();
    
    // Terminate the script execution
    die();
} else {
    // If 'logout' parameter is not set, include the configuration file
    include_once("../config/config.php");
    
    // Redirect the user to the homepage specified in the 'HOMEPAGE' constant
    header("Location: ".HOMEPAGE);
}
