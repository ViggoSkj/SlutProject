<?php

require_once "db.php";
require_once "Game.php";
require_once "User.php";
require_once "Chat.php";

class Lobby extends DatabaseObject
{

    public int $Id;
    public int $GameId;

    public static function JoinAvailableLobby($userId, $gameType): Lobby
    {
        $db = Database::GetInstance();

        $stmt = $db->PDO->prepare("
            SELECT 
                Lobby.id AS lobbyId,
                Game.id AS gameId,
                Game.gameType,
                COUNT(LobbyOccupant.id) AS playerCount
            FROM Lobby
            JOIN Game ON Lobby.gameId = Game.id
            LEFT JOIN LobbyOccupant ON Lobby.id = LobbyOccupant.lobbyId
            WHERE Game.gameType = :gameType
            GROUP BY Lobby.id, Game.id, Game.gameType
            HAVING playerCount BETWEEN 0 AND 3;
        ");

        $stmt->execute(["gameType" => $gameType]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $chosen = null;

        foreach ($results as $result) {
            if ($result > 0)
                $chosen = $result;
        }

        // create new lobby
        if ($chosen == null) {
            $game = new Game($gameType);
            $game->Save();

            $lobby = new Lobby($game->Id);
            $lobby->Save();
            $lobby->Join($userId);

            return $lobby;
        } else {
            $lobby = new Lobby($chosen["gameId"], $chosen["lobbyId"]);
            $lobby->Join($userId);

            return $lobby;
        }
    }

    public function __construct($gameId, $id = -1)
    {
        parent::__construct();

        $this->Id = $id;
        $this->GameId = $gameId;
    }

    public function Save(): void
    {
        if ($this->Id == -1) {
            $stmt = $this->m_database->PDO->prepare("INSERT INTO Lobby (gameId) VALUES(:gameId)");
            $stmt->execute(["gameId" => $this->GameId]);
            $this->Id = $this->m_database->PDO->lastInsertId();
        } else {
            // will not happen so wont implement
        }
    }

    public function GetChat(): Chat
    {
        $chat = Chat::GetLobbyChat($this->Id);

        if ($chat == null) {
            $chat = new Chat($this->Id);
            $chat->Save();
        }
        return $chat;
    }

    public function Users(): array
    {
        $stmt = $this->m_database->PDO->prepare("
        SELECT * FROM AppUser 
        INNER JOIN LobbyOccupant ON LobbyOccupant.userId = AppUser.id
        WHERE LobbyOccupant.lobbyId = :lobbyId
        ");

        $stmt->execute(["lobbyId" => $this->Id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];

        foreach ($results as $result) {
            $user = new User($result["email"], $result["username"], $result["passwordHash"], $result["verified"], $result["id"]);
            array_push($users, $user);
        }

        return $users;
    }

    public function GetGame(): Game
    {
        return Game::GetGame($this->GameId);
    }

    public function Join($userId)
    {
        $occupant = new LobbyOccupant($this->Id, $userId);
        $occupant->Save();
    }

    public function Leave($userId)
    {
        $stmt = $this->m_database->PDO->prepare("DELETE FROM LobbyOccupant WHERE userId=:userId AND lobbyId=:lobbyId");
        $stmt->execute([
            "userId" => $userId,
            "lobbyId" => $this->Id
        ]);
    }
}

class LobbyOccupant extends DatabaseObject
{
    public int $Id;
    public int $LobbyId;
    public int $UserId;

    public function __construct($lobbyId, $userId)
    {
        parent::__construct();

        $this->Id = -1;
        $this->LobbyId = $lobbyId;
        $this->UserId = $userId;
    }

    public function Save(): void
    {
        if ($this->Id == -1) {
            $stmt = $this->m_database->PDO->prepare("INSERT INTO LobbyOccupant (lobbyId, userId) VALUES(:lobbyId, :userId)");
            $stmt->execute(["lobbyId" => $this->LobbyId, "userId" => $this->UserId]);
        } else {
            // will not happen so wont implement
        }
    }
}