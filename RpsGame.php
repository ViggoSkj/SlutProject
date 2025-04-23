<?php

include "Game.php";

class RpsGame extends Game
{
    public function __construct($id)
    {
        parent::__construct(GameType::ROLUETTE, $id);
    }

    public function WaitingForMove(): bool {
        $stmt = $this->m_database->PDO->prepare("SELECT id FROM GameEvent WHERE gameId=:gameId");
        $stmt->execute([
            "gameId" => $this->Id
        ]);

        return $stmt->rowCount()%2 == 1;
    }

    public function MakeMove(int $userId, string $move)
    {
        $move = new RpsGameMoveEvent($this->Id, $userId, $move);
        $move->Save();
    }
}

class RpsGameMoveEvent extends GameEvent
{
    public const EVENT_NAME = "rps_move";

    public function __construct(int $gameId, int $userId, int $move)
    {
        $content = [
            "userId" => $userId,
            "move" => $move,
        ];

        parent::__construct($gameId, RpsGameMoveEvent::EVENT_NAME, $content);
    }
}
