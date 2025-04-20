<?php

include_once "Game.php";

class RoluetteGame extends Game
{
    public const INDEX_TO_COLOR = [
        0 => RoluetteBet::GREEN,
        1 => RoluetteBet::RED,
        2 => RoluetteBet::BLACK,
        3 => RoluetteBet::RED,
        4 => RoluetteBet::BLACK,
        5 => RoluetteBet::RED,
        6 => RoluetteBet::BLACK,
        7 => RoluetteBet::RED,
        8 => RoluetteBet::BLACK,
        9 => RoluetteBet::RED,
        10 => RoluetteBet::BLACK,
        11 => RoluetteBet::BLACK,
        12 => RoluetteBet::RED,
        13 => RoluetteBet::BLACK,
        14 => RoluetteBet::RED,
        15 => RoluetteBet::BLACK,
        16 => RoluetteBet::RED,
        17 => RoluetteBet::BLACK,
        18 => RoluetteBet::RED,
        19 => RoluetteBet::RED,
        20 => RoluetteBet::BLACK,
        21 => RoluetteBet::RED,
        22 => RoluetteBet::BLACK,
        23 => RoluetteBet::RED,
        24 => RoluetteBet::BLACK,
        25 => RoluetteBet::RED,
        26 => RoluetteBet::BLACK,
        27 => RoluetteBet::RED,
        28 => RoluetteBet::BLACK,
        29 => RoluetteBet::BLACK,
        30 => RoluetteBet::RED,
        31 => RoluetteBet::BLACK,
        32 => RoluetteBet::RED,
        33 => RoluetteBet::BLACK,
        34 => RoluetteBet::RED,
        35 => RoluetteBet::BLACK,
        36 => RoluetteBet::RED,
    ];

    public function __construct($id)
    {
        parent::__construct(GameType::ROLUETTE, $id);
    }

    public function PlaceBet(int $userId, int $amount, RoluetteBet $bet)
    {
        $event = new RoluetteEventBet($this->Id, $userId, $amount, $bet);
        $event->Save();
    }

    public function ActiveBets(): array
    {
        $nextToLastSpin = RoluetteEventSpinResult::NextToLastSpin($this->Id);   
        $nextToLastSpinId = -1;
        if ($nextToLastSpin != null)
            $nextToLastSpinId = $nextToLastSpin->Id;
        return RoluetteEventBet::BetsAfter($this->Id, $nextToLastSpinId);
    }

    public function Spin()
    {
        $event = new RoluetteEventSpinResult($this->Id, rand(0, 36));
        $event->Save();
    }

    public function CalculatePayout(int $userId): int
    {   
        $latestSpin = RoluetteEventSpinResult::LastSpin($this->Id);

        if ($latestSpin == null)
            return 0;

        $bets = $this->ActiveBets();

        foreach($bets as $bet)
        {
            if ($bet->Content["userId"] == $userId)
            {
                $betColor = $bet->Content["bet"]->Color;
                $resultColor = RoluetteGame::INDEX_TO_COLOR[$latestSpin->Index];

                if ($betColor == $resultColor)
                {
                    return $bet->Content["amount"] * 2;
                }
            }
        }

        return 0;
    }
}

class RoluetteEventBet extends GameEvent
{
    public const EVENT_NAME = "roluette_bet";

    static public function BetsAfter($gameId, $eventId) : array
    {
        $db = Database::GetInstance();
        $stmt = $db->PDO->prepare("SELECT * FROM GameEvent WHERE gameId=:gameId AND eventType=:eventType AND id>:id ORDER BY id");
        $stmt->execute([
            "gameId" => $gameId,
            "eventType" => RoluetteEventBet::EVENT_NAME,
            "id" => $eventId,
        ]);

        $bets = [];
        $reuslts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($reuslts as $result)
        {
            $content = unserialize($result["content"]);
            array_push($bets, new RoluetteEventBet($gameId, $content["userId"], $content["amount"], $content["bet"], $result["id"]));
        }

        return $bets;
    }

    public function __construct(int $gameId, int $userId, int $amount, RoluetteBet $bet, int $id=-1)
    {
        $content = [
            "userId" => $userId,
            "amount" => $amount,
            "bet" => $bet,
        ];

        parent::__construct($gameId, RoluetteEventBet::EVENT_NAME, $content, $id);
    }
}

class RoluetteEventSpinResult extends GameEvent
{
    public const EVENT_NAME = "roluette_spin_result";
    public int $Index;
    public int $Time;

    static public function LastSpin($gameId): ?RoluetteEventSpinResult
    {
        $db = Database::GetInstance();
        $stmt = $db->PDO->prepare("SELECT * FROM GameEvent WHERE gameId=:gameId AND eventType=:eventType ORDER BY id DESC LIMIT 1");
        $stmt->execute([
            "gameId" => $gameId,
            "eventType" => RoluetteEventSpinResult::EVENT_NAME
        ]);

        if ($stmt->rowCount() == 0)
            return null;

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $content = unserialize($result["content"]);
        return new RoluetteEventSpinResult($gameId, $content["index"], $content["time"], $result["id"]);
    }

    static public function NextToLastSpin($gameId): ?RoluetteEventSpinResult
    {
        $db = Database::GetInstance();
        $stmt = $db->PDO->prepare("SELECT * FROM GameEvent WHERE gameId=:gameId AND eventType=:eventType ORDER BY id DESC LIMIT 1 OFFSET 1");
        $stmt->execute([
            "gameId" => $gameId,
            "eventType" => RoluetteEventSpinResult::EVENT_NAME
        ]);

        if ($stmt->rowCount() == 0)
            return null;

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $content = unserialize($result["content"]);
        return new RoluetteEventSpinResult($gameId, $content["index"], $content["time"], $result["id"]);
    }

    public function __construct(int $gameId, int $index, int $time = -1, int $id=-1)
    {
        if ($time == -1)
            $time = time();

        $this->Index = $index;
        $this->Time = $time;

        $content = [
            "index" => $this->Index,
            "time" => $this->Time
        ];

        parent::__construct($gameId, RoluetteEventSpinResult::EVENT_NAME, $content, $id);
    }
}

class RoluetteBet
{
    public const GREEN = "green";
    public const RED = "red";
    public const BLACK = "black";

    public string $Color;

    public function __construct($color)
    {
        $this->Color = $color;
    }
}