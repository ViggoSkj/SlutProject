<?php

session_start();

include_once "login-guard.php";
include_once "User.php";
include_once "Lobby.php";

$lobby = User::SessionUser()->GetActiveLobby();

$users = $lobby->Users();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php" ?>
    <link rel="stylesheet" href="/public/styles/roluette.css">
    <script src="/public/javascript/roluette.js"></script>
    <title>Document</title>
</head>

<body>
    <?php include "game-header.php" ?>
    <main class="center-page">
    </main>

    <div id="lobby-occupants">
        <?php foreach ($users as $user) { ?>
            <img src="/public/images/user.svg" />
        <?php } ?>
    </div>
</body>

</html>