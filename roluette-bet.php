<?php

session_start();

include_once "login-guard.php";
include_once "api-util.php";
include_once "RoluetteGame.php";

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data["color"])) {
    BadReqeust("no color");
}

if (!isset($data["amount"])) {
    BadReqeust("no amount");
}

$color = $data["color"];
$amount = $data["amount"];

if ($color != "green" && $color != "red" && $color != "black") {
    BadReqeust("invalid color");
}

if (!filter_var($amount, FILTER_VALIDATE_INT)) {
    BadReqeust("invalid amount");
}

$user = User::SessionUser();
$lobby = $user->GetActiveLobby();

if ($lobby == null)
    BadReqeust("no lobby");

$game = $lobby->GetGame();

if ($game == null)
    BadReqeust("no game");

if ($game->GameType != GameType::ROLUETTE)
    BadReqeust("wrong gametype");

if ($user->GetWallet()->TryWithdraw($amount)) {
    $roluette = new RoluetteGame($game->Id);

    $roluette->PlaceBet($user->GetId(), $amount, new RoluetteBet($color));

    SendResponse([
        "betPlaced" => true
    ]);
} else {
    SendResponse([
        "betPlaced" => false
    ]);
}
