<?php
session_start();

include "User.php";

if (isset($_POST) && isset($_POST["email"]) && isset($_POST["password"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $user = User::GetUserByEmail($email);

    if (!$user) {
        header("Location: /login.php?error=password and email does not match");
        die();
    }

    if (!$user->VerifyPassword($password)) {
        
        var_dump(password_verify("test", password_hash("test", PASSWORD_BCRYPT)));
        die();
    }

    $user->MakeSessionUser();

    if ($user->IsVerified()) {
        header("Location: /index.php");
        die();
    } else {
        header("Location: /verification-needed.php");
        die();
    }
}
