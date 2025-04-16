<?php

session_start();

require_once "UserVerificationToken.php";
require_once "User.php";
require_once "PasswordResetToken.php";

$resetFase = false;
$success = false;

$errorMessage = "";

if (isset($_POST["password"]) && isset($_GET["token"])) { // this is when you actually reset the password
    $newPassword = isset($_POST["password"]);

    $tokenId = $_GET["token"];
    $token = PasswordResetToken::GetToken($tokenId);

    if (!$token) {
        $errorMessage = "Invalid password reset link.";
    } else if ($token->IsExpired()) {
        // remove user since token is expired
        $user = User::GetUser($token->UserId);
        $user->DeleteUser();
        $token->Destory();
        $errorMessage = "Password reset link is expired.";
    } else {
        $user = User::GetUser($token->UserId);
        $user->SetPassword($newPassword);
        $user->SaveUser();
        $token->Destory();

        header("Location: /reset-password.php?success");
    }
} else if (isset($_GET["token"])) { // this is when you go from the link in the email
    $tokenId = $_GET["token"];
    $token = PasswordResetToken::GetToken($tokenId);

    if (!$token) {
        $errorMessage = "Invalid password reset link.";
    } else if ($token->IsExpired()) {
        // remove user since token is expired
        $user = User::GetUser($token->UserId);
        $user->DeleteUser();
        $token->Destory();
        $errorMessage = "Password reset link is expired.";
    } else {
        $user = User::GetUser($token->UserId);
        $resetFase = true;
    }
} else if (isset($_GET["success"])) { // when the user have successfully reset the password
    $success = true;
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
        <?php if ($resetFase) { /* when the user inputs the new password*/ ?>
            <form method="POST">
                <div>
                    <p>New password</p>
                    <input id="password" name="password">
                </div>
                <button>Submit</button>
            </form>
        <?php } else if ($success) { /* when the user have changed the password */ ?>
                <section>
                    <p class="color-pos">Password successfully changed.</p>
                </section>
        <?php } else { /* misc messages */ ?>
                <section class="flex-centering">
                    <p class="color-neg"><?php echo $errorMessage ?></p>
                </section>
        <?php } ?>
    </main>
</body>

</html>