<?php
require_once "User.php";
$user = User::SessionUser();
$wallet = $user->GetWallet();
?>

<header id="game-header">
    <h1><a href="index.php">Generic Gambing Site</a></h1>
    <div>
        <span><?php echo $wallet->Amount ?></span>
        <img src="public/images/chip.svg" />
    </div>

    <a href="exit-lobby.php">
        <img src="public/images/exit.svg" />
    </a>
</header>