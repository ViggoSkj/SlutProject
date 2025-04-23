<?php

session_start();

include "login-guard.php";
include "api-util.php";
include "RpsGame.php";


$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data["move"])) {
    BadReqeust("no move.");
}

$move = $data["move"];


$user = User::SessionUser();
$lobby = $user->GetActiveLobby();

if ($lobby == null) {
    BadReqeust("no lobby");
}

$game = $lobby->GetGame();

if ($game->GameType != GameType::ROLUETTE) {
    BadReqeust("wrong game type");
}

$rps = new RpsGame($game->Id);

if ($rps->WaitingForMove())
{
    $rps->WaitingForMove();
}