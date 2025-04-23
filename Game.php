<?php

require_once "db.php";

class Game extends DatabaseObject
{
    public int $Id;
    public string $GameType;

    static public function GetGame(int $id): ?Game
    {
        $db = Database::GetInstance();
        $stmt = $db->PDO->prepare("SELECT * FROM Game WHERE id=:id");
        $stmt->execute([
            "id" => $id
        ]);

        if ($stmt->rowCount() == 0)
            return null;

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Game($result["gameType"], $result["id"]);
    }

    public function __construct($gameType, $id = -1)
    {
        parent::__construct();

        $this->Id = $id;
        $this->GameType = $gameType;
    }

    public function Save(): void
    {
        if ($this->Id == -1) {
            $stmt = $this->m_database->PDO->prepare("INSERT INTO Game (gameType) VALUES(:gameType)");
            $stmt->execute(["gameType" => $this->GameType]);
            $this->Id = $this->m_database->PDO->lastInsertId();
        } else {
            // will not happen so wont implement
        }
    }

    public function Events(): array
    {
        return $this->EventsAfter(-1);
    }

    public function EventsAfter(int $eventId): array
    {
        $stmt = $this->m_database->PDO->prepare("SELECT * FROM GameEvent WHERE gameId=:gameId AND id>:id");
        $stmt->execute([
            "gameId" => $this->Id,
            "id" => $eventId,
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $events = [];
        foreach ($results as $result) {
            array_push($events, new GameEvent($this->Id, $result["eventType"], unserialize($result["content"]), $result["id"]));
        }

        return $events;
    }

    public function Clean()
    {
        $this->m_database->PDO->prepare("DELETE From Game WHERE id = :id")->execute([
            "id" => $this->Id,
        ]);
        
        $this->m_database->PDO->prepare("DELETE From GameEvent WHERE gameId = :gameId")->execute([
            "gameId" => $this->Id,
        ]);
    }
}

class GameEvent extends DatabaseObject
{
    public int $Id;
    public int $GameId;
    public string $EventType;
    public $Content;

    public function __construct(int $gameId, string $eventType, $content, $id = -1)
    {
        parent::__construct();

        $this->Id = $id;
        $this->GameId = $gameId;
        $this->EventType = $eventType;
        $this->Content = $content;
    }

    public function Save(): void
    {
        if ($this->Id == -1) {
            $stmt = $this->m_database->PDO->prepare("INSERT INTO GameEvent (gameId, eventType, content) VALUES(:gameId, :eventType, :content)");
            $stmt->execute([
                "gameId" => $this->GameId,
                "eventType" => $this->EventType,
                "content" => serialize($this->Content)
            ]);
        } else {
            // will not happen so wont implement
        }
    }
}

abstract class GameType
{
    public const ROLUETTE = "roluette";
    public const POKER = "poker";
    public const BLACKJACK = "blackjack";
    public const urls = [
        GameType::ROLUETTE => "roluette.php",
        GameType::POKER => "poker.php",
        GameType::BLACKJACK => "blackjack.php",
    ];
}
