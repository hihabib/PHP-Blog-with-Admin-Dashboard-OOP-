<?php
if(isset($_GET['logout'])) {
    include_once("../lib/Session.php");
    Session::destroy();
    die();
} else {
    include_once("../config/config.php");
    header("Location: ".HOMEPAGE);
}