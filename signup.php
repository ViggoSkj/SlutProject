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
        <form method="POST" action="/create-user.php">
            <p>Email</p>
            <input name="email"></input>
            <p>Username</p>
            <input name="username"></input>
            <p>Password</p>
            <input name="password"></input>
            <button>Sign up</button>
            <p style="color: red;"><?php echo $error ?></p>
        </form> <a class="button" href="/login.php">log in</a>
    </main>
</body>

</html>