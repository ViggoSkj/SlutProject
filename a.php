<?php

include_once "db.php";

if (isset($_GET["action"])) {
    $action = $_GET["action"];

    if ($action == "reset-games") {
        $db = Database::GetInstance();

        $db->PDO->exec("
        DELETE FROM Lobby;
        DELETE FROM Game;
        DELETE FROM GameEvent;
        DELETE FROM LobbyOccupant;
        DELETE FROM Chat;
        DELETE FROM ChatMessage;
        ");
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="">
        <input name="action" value="reset-games" hidden>
        <p>Reset lobbies and games</p>
        <button>Rest</button>
    </form>
</body>

</html>