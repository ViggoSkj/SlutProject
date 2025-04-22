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
        <form method="POST" action="create-user.php">
            <div>
                <p>Email</p>
                <input name="email"></input>
            </div>
            <div>
                <p>Username</p>
                <input name="username"></input>
            </div>
            <div>
                <p>Password</p>
                <input name="password"></input>
            </div>
            <p style="color: red;"><?php echo $error ?></p>
            <div class="button-row">
                <button class="button-pos">Sign up</button>
                <a class="button" href="login.php">log in</a>
            </div>
        </form>
    </main>
</body>

</html>