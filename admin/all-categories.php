<?php 
// initialize necessary script
include_once('inc/init.php');

// check login. If the user is not logged in user, then redirect the user to the login page
if(false == Session::checkLogin()){
    header("Location: login.php");
    die();
}

// Menu and submenu name
$mainMenu = "Category";
$subMenu = "All Caregories";

include_once('inc/header.php'); 
?>
hello world
<?php include_once('inc/footer.php'); ?>