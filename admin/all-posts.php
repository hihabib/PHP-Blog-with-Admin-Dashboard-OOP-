<?php
// initialize necessary script
include_once('inc/init.php');

// include category
include_once('classes/Category.php');

// check login. If the user is not logged in user, then redirect the user to the login page
if (false == Session::checkLogin()) {
    header("Location: login.php");
    die();
}

// Menu and submenu name
$mainMenu = "Post";
$subMenu = "All Posts";

include_once('inc/header.php');
?>
Hello all posts

<?php include_once('inc/footer.php'); ?>