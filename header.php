<?php
require_once "User.php";
$loggedIn = User::SessionUser() != null;

?>

<header>
    <h1>Generic Gambing Site</h1>

    <nav>
        <a href="/index.php">Home</a>
    </nav>
    <div>
        <?php if ($loggedIn) { ?>
            <a href="/logout.php">log out</a>
            <a href="/profile.php">Profile</a>
        <?php } else { ?>
            <a href="/logout.php">log in/sign up</a>
        <?php } ?>
    </div>
</header>