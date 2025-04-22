<?php
require_once "UserVerificationToken.php";
require_once "User.php";

if (isset($_GET["token"])) {
    $tokenId = $_GET["token"];
    $token = UserVerificationToken::GetToken($tokenId);

    $errorMessage = "";
    $successMessage = "";

    if (!$token) {
        $errorMessage = "Invalid verification link.";
    } else if ($token->IsExpired()) {
        // remove user since token is expired
        $user = User::GetUser($token->UserId);
        $user->DeleteUser();
        $token->Destory();
        $errorMessage = "Verification link is expired.";
    } else {
        $user = User::GetUser($token->UserId);
        $user->VerifyUser();
        $user->SaveUser();
        $token->Destory();

        $successMessage = "Account verified.";
    }

}

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
        <section class="flex-centering">
            <?php if ($errorMessage != "") { ?>
                <p class="color-neg"><?php echo $errorMessage ?></p>
            <?php } ?>
            
            <?php if ($successMessage != "") { ?>
                <p class="color-success"><?php echo $successMessage ?></p>
                <a href="" class="button button-pos">Home</a>
            <?php } ?>
        </section>
    </main>
</body>

</html>