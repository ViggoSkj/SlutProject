<?php

session_start();

require_once "login-guard.php";
require_once "User.php";

if (isset($_GET["amount"]) && isset($_GET["previous"])) {
    $user = User::SessionUser();
    $wallet = $user->GetWallet();

    $amount = $_GET["amount"];
    $previous = $_GET["previous"];

    if ($amount < 0) {
        $wallet->TryWithdraw(-$amount);
    } else {
        $wallet->Deposit($amount);
    }

    header("Location: ".$previous);
    exit();
}
?>
invalid request;