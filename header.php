<?php
require_once "User.php";
$loggedIn = User::SessionUser() != null;

?>

<header>
    <h1><a href="/">Generic Gambing Site</a></h1>

    <nav>
        <a href="/index.php" class="button">Home</a>
    </nav>
    <div class="button-row">
        <?php if ($loggedIn) { ?>
            <a href="/logout.php" class="button button-neg">Log out</a>
            <a href="/profile.php" class="button">Profile</a>
        <?php } else { ?>
            <a href="/logout.php" class="button">Log in / Sign up</a>
        <?php } ?>
    </div>
</header>