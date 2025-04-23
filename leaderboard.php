<?php
session_start();

include_once "login-guard.php";
include_once "Game.php";

$topUsers = User::GetTopUsers();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php" ?>
    <title>Document</title>
</head>

<body>
    <?php include "header.php" ?>
    <main class="center-page">
        <section>
            <?php foreach ($topUsers as $user) { ?>
                <p><?php echo $user["username"] ?> - <?php echo $user["amount"] ?> </p>
            <?php } ?>
        </section>
    </main>
</body>

</html>