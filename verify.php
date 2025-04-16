<?php
require_once "UserVerificationToken.php";
require_once "User.php";

if (isset($_GET["token"]))
{
    $tokenId = $_GET["token"];
    $token = UserVerificationToken::GetToken($tokenId);

    if (!$token)
    {
        die("not a valid token");
    }
    
    if ($token->IsExpired())
    {
        // remove user since token is expired
        $user = User::GetUser($token->UserId);
        $user->DeleteUser();
        $token->Destory();
        die("token expired and account deleted");
    }
    
    $user = User::GetUser($token->UserId);
    $user->VerifyUser();
    $user->SaveUser();
    $token->Destory();
}

