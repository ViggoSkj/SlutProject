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
    <script src="public/javascript/events.js"></script>
    <script src="public/javascript/chat.js"></script>
    <script src="public/javascript/roluette.js"></script>
    <title>Document</title>
</head>

<body class="game-layout">
    <?php include "game-header.php" ?>
    <div id="social">
        <section id="chat">
            <div id="chat-messages">

            </div>
            <div id="chat-controls">
                <input id="message-input" type="text">
                <button id="send-message">Send</button>
            </div>
        </section>
        <div id="lobby-occupants">
            <?php foreach ($users as $user) { ?>
                <img src="/public/images/user.svg" />
            <?php } ?>
        </div>
    </div>

    <main>
        <section id="game">
            <div id="roluette-bar">
                <?php for ($i = 0; $i < 37; $i++) { ?>
                    <p><?php echo $i; ?></p>
                <?php } ?>
            </div>
            <div id="roluette-pointer"></div>
        </section>
        <section id="controls" class="button-row">
            <p>Red</p>
            <input type="radio" name="color" id="roluette-color-red">
            <p>Green</p>
            <input type="radio" name="color" id="roluette-color-green">
            <p>Black</p>
            <input type="radio" name="color" id="roluette-color-black">
            <input id="bet-amount" placeholder="amount">
            <button id="bet">
                Bet
            </button>
        </section>
    </main>
</body>

</html>