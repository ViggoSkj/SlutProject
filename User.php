<?php

require_once "db.php";
require_once "Lobby.php";
require_once "Wallet.php";

class User extends DatabaseObject
{
    static private User | null $s_sessionUser = null;

    public string $Email;
    public string $Username;

    private bool $m_verified;
    private int $m_id;
    private string $m_passwordHash;

    static public function GetTopUsers(): array {    
        $db = Database::GetInstance();

        $stmt = $db->PDO->prepare("
            SELECT 
                Wallet.amount as amount,
                AppUser.username as username
            FROM AppUser
            INNER JOIN Wallet On AppUser.id = Wallet.userId
            ORDER BY amount DESC LIMIT 10
        ");
        $stmt->execute();

        $results = [];

        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $result)
        {
            array_push($results, [
                "username" => $result["username"],
                "amount" => $result["amount"],
            ]);
        }

        return $results;
    }

    static public function SessionUser(): User | null
    {
        if (self::$s_sessionUser == null && isset($_SESSION["user_id"]))
            !self::$s_sessionUser = User::GetUser($_SESSION["user_id"]);
        return self::$s_sessionUser;
    }

    static public function GetUser(int $userId): User | null
    {
        $db = Database::GetInstance();
        $stmt = $db->PDO->prepare("SELECT * from AppUser WHERE id=:id");
        $stmt->execute(["id" => $userId]);

        if ($stmt->rowCount()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return new User(
                $result["email"],
                $result["username"],
                $result["passwordHash"],
                $result["verified"],
                $result["id"],
            );
        }

        return null;
    }

    static public function GetUserByEmail(string $email): User | null
    {
        $db = Database::GetInstance();
        $stmt = $db->PDO->prepare("SELECT * from AppUser WHERE email=:email");
        $stmt->execute(["email" => $email]);

        if ($stmt->rowCount()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return new User(
                $result["email"],
                $result["username"],
                $result["passwordHash"],
                $result["verified"],
                $result["id"],
            );
        }

        return null;
    }

    static public function IsDuplicateEntry(User $user): bool
    {
        $db = Database::GetInstance();
        $stmt = $db->PDO->prepare("SELECT id from AppUser where email=:email or username=:username");
        $stmt->execute([
            "email" => $user->Email,
            "username" => $user->Username,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function __construct($email, $username, $passwordHash, $verified = false, $id = -1)
    {
        parent::__construct();

        $this->m_id = $id;
        $this->m_verified = $verified;
        $this->Email = $email;
        $this->Username = $username;
        $this->m_passwordHash = $passwordHash;
    }

    public function GetActiveLobby(): Lobby | null
    {
        $stmt = $this->m_database->PDO->prepare("
        SELECT 
            Lobby.Id as lobbyId,
            Lobby.gameId as gameId 
        FROM LobbyOccupant
        INNER JOIN Lobby on LobbyOccupant.lobbyId = Lobby.Id
        WHERE LobbyOccupant.userId=:userId
        ");

        $stmt->execute([
            "userId" => $this->m_id
        ]);

        if ($stmt->rowCount() == 0)
            return null;

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Lobby($result["gameId"], $result["lobbyId"]);
    }

    public function GetWallet(): Wallet
    {
        $wallet = Wallet::GetUsersWallet($this->m_id);

        if ($wallet == null) {
            $wallet = new Wallet($this->m_id, 1000, 0);
            $wallet->Save();
        }

        return $wallet;
    }

    public function GetId(): int
    {
        return $this->m_id;
    }

    public function MakeSessionUser()
    {
        $_SESSION["user_id"] = $this->m_id;
    }

    public function SetPassword($textPassword)
    {
        $this->m_passwordHash = password_hash($textPassword, PASSWORD_BCRYPT, ["cost" => 13]);
    }

    public function VerifyPassword($passwordGuess): bool
    {
        return password_verify($passwordGuess, $this->m_passwordHash);
    }

    public function IsVerified(): bool
    {
        return $this->m_verified;
    }

    public function VerifyUser()
    {
        $this->m_verified = true;
    }

    public function SaveUser()
    {
        if ($this->m_id == -1) {
            $this->InsertUser();
            return;
        }
        $this->UpdateUser();
    }

    public function DeleteUser()
    {
        $stmt = $this->m_database->PDO->prepare("DELETE FROM AppUser WHERE id=:id");
        $stmt->execute(["id" => $this->m_id]);
    }

    public function LeaveLobby(): bool
    {
        $activeLobby = $this->GetActiveLobby();

        if ($activeLobby == null) {
            return false;
        }

        $activeLobby->Leave($this->m_id);

        return true;
    }

    private function InsertUser()
    {
        $stmt = $this->m_database->PDO->prepare("
        INSERT AppUser
               (email, username, passwordHash, verified)
        VALUES (:email, :username, :passwordHash, :verified)
        ");
        $stmt->execute([
            'email' => $this->Email,
            'username' => $this->Username,
            'passwordHash' => $this->m_passwordHash,
            'verified' => $this->m_verified,
        ]);
    }

    private function UpdateUser()
    {
        $stmt = $this->m_database->PDO->prepare("
        UPDATE AppUser
            SET email=:email, username=:username, passwordHash=:passwordHash, verified=:verified
        WHERE id=:id
        
        ");
        $stmt->execute([
            'email' => $this->Email,
            'username' => $this->Username,
            'passwordHash' => $this->m_passwordHash,
            'verified' => $this->m_verified,
            'id' => $this->m_id,
        ]);
    }
}
