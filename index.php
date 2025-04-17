<?php
session_start();

include_once "login-guard.php";
include_once "Game.php"

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "head.php"?>
    <title>Document</title>
</head>
<body>
    <?php include "header.php"?>
    <main class="center-page">
        <section class="button-row">
            <a class="button button-pos game-option" href="/join-lobby.php?game=<?php echo GameType::ROLUETTE?>"><span>Roluette</span></a>
            <a class="button button-pos game-option" href="/join-lobby.php?game=<?php echo GameType::BLACKJACK?>"><span>Blackjack</span></a>
            <a class="button button-pos game-option" href="/join-lobby.php?game=<?php echo GameType::POKER?>"><span>Poker</span></a>
        </section>
    </main>
</body>
</html>