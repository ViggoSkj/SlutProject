<?php
session_start();

include "login-guard.php";
include "api-util.php";


$user = User::SessionUser();
SendResponse([
    "claimed" => $user->GetWallet()->TryClaimDailyReward()
]);