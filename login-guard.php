<?php
require_once 'User.php';

if (!User::SessionUser()) {
    header("Location: login.php");
    die();
}

if (!User::SessionUser()->IsVerified()) {
    header("Location: verification-needed.php");
    die();
}

?>