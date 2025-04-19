<?php
session_start();

header('Content-Type: application/json');



include "login-guard.php";
include "api-util.php";
include "RoluetteGame.php";


$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data["currentEventIndex"])) {
    BadReqeust("no current event index set.");
}

$currentEventIndex = $data["currentEventIndex"];


$user = User::SessionUser();
$lobby = $user->GetActiveLobby();

if ($lobby == null) {
    BadReqeust("no lobby");
}

$game = $lobby->GetGame();

if ($game->GameType != GameType::ROLUETTE) {
    BadReqeust("wrong game type");
}

$roluette = new RoluetteGame($game->Id);

$gameState = $roluette->GetState();

$response = [
    "state" => $gameState,
    "events" => [],
];

$events = $roluette->Events();

foreach ($events as $event) {
    $eventType = $event->eventType;

    switch ($eventType) {
        case (RoluetteEventSpinResult::EVENT_NAME): {
            if ($event->Id > $currentEventIndex) {
                array_push($response["events"], $event);
            }
            break;
        }
    }
}

SendResponse($response);