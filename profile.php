<?php
session_start();

include_once "login-guard.php";

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
        <form action="/create-reset-password.php" method="POST">
            <button>Reset password</button>
        </form>
    </main>

</body>
</html>