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

    <main>
        <form method="POST" action="/auth.php">
            <p>Email</p>
            <input name="email"></input>
            <p>Password</p>
            <input name="password"></input>
            <button>Log in</button>
            <p style="color: red;"><?php echo $error ?></p>
        </form>
        <a class="button" href="/signup.php">sign up</a>
    </main>
</body>

</html>