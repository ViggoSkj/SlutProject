<?php
session_start();

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

$response = [
    "events" => [],
];


$lastSpin = RoluetteEventSpinResult::LastSpin($roluette->Id);

if ($currentEventIndex == -1)
{
    $currentEventIndex = $lastSpin->Id;
    $response["newEventId"] = $lastSpin->Id;
}


$spin = false;

if ($lastSpin != null) {
    if (time() - $lastSpin->Time > 10) {
        $spin = true;
    }

    $response["timeSinceLastSpin"] = time() - $lastSpin->Time;
}

$response["timeSinceLastSpin"] = -1;

if ($spin == false) {
    $occupants = $lobby->Users();
    $bets = $roluette->ActiveBets();

    $allBetting = true;


    foreach ($occupants as $occupant) {
        $found = false;

        foreach ($bets as $bet) {
            if ($occupant->GetId() == $bet->Content["userId"]) {
                $found = true;
                break;
            }
        }
        if ($found == false)
            $allBetting = false;
    }


    if ($allBetting) {
        $spin = true;
    }
}

if ($spin) {
    $roluette->Spin();
}


$events = $roluette->EventsAfter($currentEventIndex);

foreach ($events as $event) {
    $eventType = $event->EventType;

    switch ($eventType) {
        case (RoluetteEventSpinResult::EVENT_NAME): {
            $payout = $roluette->CalculatePayout($user->GetId());
            $response["payout"] = $payout;
            $user->GetWallet()->Deposit($payout);
            array_push($response["events"], $event);
            break;
        }
    }
}

SendResponse($response);