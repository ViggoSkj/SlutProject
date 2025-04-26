<?php

include_once "db.php";

class Wallet extends DatabaseObject
{
    public const DAILY_REWARD_AMOUNT = 1000;

    public int $Id;
    public int $UserId;
    public int $Amount;
    public int $LastDailyRewardClaimed;

    static public function GetUsersWallet($userId): ?Wallet
    {
        $db = Database::GetInstance();
        $stmt = $db->PDO->prepare("SELECT * FROM Wallet WHERE userId=:userId");
        $stmt->execute([
            "userId" => $userId
        ]);

        if ($stmt->rowCount() == 0)
            return null;

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Wallet($userId, $result["amount"], $result["lastDailyRewardClaimed"], $result["id"]);
    }

    public function __construct($userId, $amount, $lastDailyRewardClaimed, $id = -1)
    {
        parent::__construct();
        $this->Id = $id;
        $this->UserId = $userId;
        $this->Amount = $amount;
        $this->LastDailyRewardClaimed = $lastDailyRewardClaimed;
    }

    public function NextDailyReward(): int
    {
        return $this->LastDailyRewardClaimed + 3600  * 24;
    }

    public function TryWithdraw(int $amount): bool
    {
        if ($this->Amount < $amount) {
            return false;
        }

        $this->Amount -= $amount;

        $this->Save();

        return true;
    }

    public function Deposit(int $amount)
    {
        $this->Amount += $amount;
        $this->Save();
    }

    public function TryClaimDailyReward(): bool
    {
        if (time() < $this->LastDailyRewardClaimed + 3600 * 24)
            return false;

        $this->LastDailyRewardClaimed = time();

        $this->Amount += Wallet::DAILY_REWARD_AMOUNT;

        $this->Save();

        return true;
    }

    public function Save()
    {
        if ($this->Id == -1) {
            $stmt = $this->m_database->PDO->prepare("
            INSERT INTO Wallet
                (userId, amount, lastDailyRewardClaimed) 
            VALUES
                (:userId, :amount, :lastDailyRewardClaimed) 
            ");

            $stmt->execute([
                "userId" => $this->UserId,
                "amount" => $this->Amount,
                "lastDailyRewardClaimed" => $this->LastDailyRewardClaimed,
            ]);

            $this->Id = $this->m_database->PDO->lastInsertId();
        } else {
            $stmt = $this->m_database->PDO->prepare("
            UPDATE Wallet
                SET userId=:userId, amount=:amount, lastDailyRewardClaimed=:lastDailyRewardClaimed
            WHERE
                id=:id
            ");

            $stmt->execute([
                "id" => $this->Id,
                "userId" => $this->UserId,
                "amount" => $this->Amount,
                "lastDailyRewardClaimed" => $this->LastDailyRewardClaimed,
            ]);
        }
    }
}
