<?php
session_start();

include "login-guard.php";
include "api-util.php";
include "RoluetteGame.php";


$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data["currentEventIndex"])) {
    BadReqeust("no current event index set.");
}

if (!isset($data["currentChatIndex"])) {
    BadReqeust("no current chat index set.");
}

$currentEventIndex = $data["currentEventIndex"];
$currentChatIndex = $data["currentChatIndex"];

if (filter_var($currentEventIndex, FILTER_VALIDATE_INT)) {
    BadReqeust("invalid current event index");
}

if (filter_var($currentChatIndex, FILTER_VALIDATE_INT)) {
    BadReqeust("invalid current chat index");
}

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

if ($currentEventIndex == -1) {
    $currentEventIndex = $lastSpin->Id;
    $response["newEventId"] = $lastSpin->Id;
}


// region chat

if ($currentChatIndex == -1) {
    $currentChatIndex = 0;
}

$chat = $lobby->GetChat();
$messages = $chat->GetMessagesAfter($currentChatIndex);

if (count($messages) > 0) {
    $response["newMessageId"] = $messages[count($messages) - 1]->Id;
}

for ($i = 0; $i < count($messages); $i++) {
    $message = $messages[$i];

    $messageUser = User::GetUser($message->UserId);

    $messages[$i] = [
        "message" => $message->Message,
        "user" => $messageUser->Username,
        "you" => $message->UserId == $user->GetId()
    ];
}

$response["newMessages"] = $messages;


// endregion chat



$spin = false;

if ($lastSpin != null) {
    if (time() - $lastSpin->Time > 10) {
        $spin = true;
    }

    $response["timeSinceLastSpin"] = time() - $lastSpin->Time;
}

$response["timeSinceLastSpin"] = -1;
$occupants = $lobby->Users();
$response["userCount"] = count($occupants);

if ($spin == false) {
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

if (count($events) > 0) {
    $response["newEventId"] = $events[count($events) - 1]->Id;
}

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
