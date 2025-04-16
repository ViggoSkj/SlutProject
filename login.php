<?php
session_start();

$error = isset($_GET["error"]) ? $_GET["error"] : ""

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
        <form method="POST" action="/auth.php">
            <div>
                <p>Email</p>
                <input name="email"></input>
            </div>
            <div>
                <p>Password</p>
                <input name="password"></input>
            </div>
            <p style="color: red;"><?php echo $error ?></p>
            <div class="button-row">
                <button class="button-pos">Log in</button>
                <a class="button" href="/signup.php">Sign up</a>
            </div>
        </form>
    </main>
</body>

</html>