<?php
session_start();

include_once "User.php";
include_once "UserVerificationToken.php";
include_once "util.php";

if (isset($_POST) && isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["password"])) {
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];


    // verify email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        header("Location: signup.php?error=invalid email");
        die();
    }

    $user = new User($email, $username, $password);

    if (User::IsDuplicateEntry($user)) {
        header("Location: signup.php?error=user already exists");
        die();
    }

    $user->SaveUser();
    $user = User::GetUserByEmail($email);

    if ($user) {
        $user->MakeSessionUser();     

        $userVerificationToken = UserVerificationToken::CreateToken($user->GetId());

        echo email($user->Email, "verification", "https://172.234.100.145/verify.php?token=".$userVerificationToken->UUID);
    } else {
        die("somthing whent wrong.");
    }
    header("Location: verification-needed.php");
    die();
}