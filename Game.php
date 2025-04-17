<?php

require_once "db.php";

class Game extends DatabaseObject
{
    public int $Id;
    public string $GameType;

    public function __construct($gameType)
    {
        parent::__construct();

        $this->Id = -1;
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

    public function PushEvent(string $content): void
    {
        $event = new GameEvent($this->Id, $content);
        $event->Save();
    }
}

class GameEvent extends DatabaseObject
{
    public int $Id;
    public int $GameId;
    public string $Content;

    public function __construct($gameId, $content)
    {
        parent::__construct();

        $this->Id = -1;
        $this->GameId = $gameId;
        $this->Content = $content;
    }

    public function Save(): void
    {
        if ($this->Id == -1) {
            $stmt = $this->m_database->PDO->prepare("INSERT INTO GameEvent (gameId, content) VALUES(:gameId, :content)");
            $stmt->execute(["gameId" => $this->GameId, "content" => $this->Content]);
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
    public const urls= [
        GameType::ROLUETTE => "roluette.php",
        GameType::POKER => "poker.php",
        GameType::BLACKJACK => "blackjack.php",
    ];
}