<?php
session_start();

include_once "User.php";
include_once "UserVerificationToken.php";

if (isset($_POST) && isset($_POST["email"]) && isset($_POST["username"]) && isset($_POST["password"])) {
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    $user = new User($email, $username, $password);

    if (User::IsDuplicateEntry($user)) {
        header("Location: /signup.php?error=user already exists");
        die();
    }

    $user->SaveUser();
    $user = User::GetUserByEmail($email);

    if ($user) {
        $user->MakeSessionUser();     

        $userVerificationToken = UserVerificationToken::CreateToken($user->GetId());

        mail($user->Email, "verification", "https://labb.vgy.se/....test?token=".$userVerificationToken->UUID);
    } else {
        die("somthing whent wrong.");
    }

    
    header("Location: /verification-needed.php");
    die();
}
