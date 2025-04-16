<?php
session_start();
require 'User.php';

if (!User::SessionUser()) {
    header("Location: login.php");
    die();
}

if (!User::SessionUser()->IsVerified()) {
    header("Location: verification-needed.php");
    die();
}

?>
