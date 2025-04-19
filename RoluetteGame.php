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

    public function GetState(): RoluetteGameState
    {
        $events = $this->Events();

        $last = count($events) - 1;
        if ($last != -1 && $events[$last]->EventType == RoluetteEventSpinResult::EVENT_NAME) {
            return RoluetteGameState::SPUN;
        }

        return RoluetteGameState::BETTING;
    }

    public function GetSpinResult(): ?RoluetteEventSpinResult
    {
        $events = $this->Events();
        $last = count($events) - 1;
        if ($events[$last]->EventType == RoluetteEventBet::EVENT_NAME) {
            return null;
        }

        return $events[$last];
    }

    public function PlaceBet(int $userId, int $amount, RoluetteBet $bet)
    {
        $event = new RoluetteEventBet($this->Id, $userId, $amount, $bet);
        $event->Save();
    }

    public function Spin()
    {
        $event = new RoluetteEventSpinResult($this->Id, rand(0, 36));
        $event->Save();
    }

    public function CalculatePayout(int $userId): int
    {
        $result = $this->GetSpinResult();

        if ($result == 0)
            return 0;

        $events = $this->Events();

        foreach ($events as $event) {
            if ($event->EventType == RoluetteEventBet::EVENT_NAME) {
                $bet = $event->bet;
                if ($event->userId == $userId) {
                    $color = RoluetteGame::INDEX_TO_COLOR[$result->Index];

                    if ($bet->color == $color){
                        return $bet->amount;
                    }
                }
            }
        }

        return 0;
    }
}

class RoluetteEventBet extends GameEvent
{
    public const EVENT_NAME = "roluette_bet";

    public function __construct(int $gameId, int $userId, int $amount, RoluetteBet $bet)
    {
        $content = [
            "userId" => $userId,
            "amount" => $amount,
            "bet" => $bet,
        ];

        parent::__construct($gameId, RoluetteEventBet::EVENT_NAME, $content);
    }
}

class RoluetteEventSpinResult extends GameEvent
{
    public const EVENT_NAME = "roluette_spin_result";
    public int $Index;

    public function __construct(int $gameId, int $index)
    {
        $content = [
            "index" => $index,
        ];

        $this->Index = $index;

        parent::__construct($gameId, RoluetteEventSpinResult::EVENT_NAME, $content);
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

enum RoluetteGameState:string
{
    case BETTING = "roluette_betting";
    case SPUN = "roluette_spun";
}