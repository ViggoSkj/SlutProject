<?php

session_start();

include_once "login-guard.php";
include_once "User.php";
include_once "Lobby.php";

$user = User::SessionUser();

if(isset($_GET["game"]))
{
    $type = $_GET["game"];
    switch($type){
        case (GameType::ROLUETTE):
            break;
        default:
            die("invalid game type");
    }

    $user->LeaveLobby();
    $lobby = Lobby::JoinAvailableLobby($user->GetId(), $type);

    header("Location: ".GameType::urls[$type]);
    exit();
}
die("no game type");