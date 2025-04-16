<?php
session_start();

include_once "User.php";
include_once "PasswordResetToken.php";
include_once "login-guard.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = User::SessionUser();

    $token = PasswordResetToken::CreateToken($user->GetId());

    //mail($user->Email, "password reset", "https://labb.vgy.se/....test?token=".$token->UUID);

    header("Location: /create-reset-password.php");
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