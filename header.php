<?php
require_once "User.php";
$user = User::SessionUser();
$loggedIn = $user != null;
?>

<header id="header">
    <h1><a href="index.php">Generic Gambing Site</a></h1>

    <?php if ($loggedIn) /* Logged in */ { ?>
        <?php

        $next = $user->GetWallet()->NextDailyReward() - time();
        ?>
        <nav>
            <a href="index.php" class="button">Home</a>
            <a href="leaderboard.php" class="button">Leaderboard</a>
            <button href="leaderboard.php" class="button" id="daily-reward-button" data-next="<?php echo $next ?>">Daily Reward</button>
        </nav>
        <div class="button-row">
            <div>
                <span id="chips"><?php echo $user->GetWallet()->Amount ?></span>
                <img src="public/images/chip.svg" />
            </div>
            <a href="logout.php" class="button button-neg">Log out</a>
            <a href="profile.php" class="button">Profile</a>
        </div>
    <?php } else /* Not logged in */ { ?>
        <nav>
            <a href="index.php" class="button">Home</a>
        </nav>
        <div class="button-row">
            <a href="logout.php" class="button">Log in / Sign up</a>
        </div>
    <?php } ?>
</header>