<?php
session_start();

include_once "User.php";
include_once "PasswordResetToken.php";
include_once "login-guard.php";
include_once "util.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = User::SessionUser();

    $token = PasswordResetToken::CreateToken($user->GetId());

    echo email($user->Email, "password reset", "http://172.234.100.145/reset-password.php/?token=".$token->UUID);

    exit();
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "head.php"?>
    <title>Document</title>
</head>
<body>
    <?php include "header.php"?>
    <main class="center-page">
        <section>
            <p>Password reset link have been sent to your email.</p>
        </section>
    </main>
</body>
</html>