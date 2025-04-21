<?php
session_start();

include_once "login-guard.php";
include_once "api-util.php";

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data["message"])) {
    BadReqeust("no message");
}

$message = $data["message"];
$user = User::SessionUser();
$lobby = $user->GetActiveLobby();

if ($lobby == null)
    BadReqeust("not lobby");

$chat = $lobby->GetChat();

$chat->WriteMessage($user->GetId(), $message);

SendResponse([
    "messageSent" => true
]);