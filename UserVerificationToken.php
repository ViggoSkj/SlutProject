<?php
require_once "db.php";
require_once "util.php";

class UserVerificationToken extends DatabaseObject
{
    public string $UUID;
    public int $UserId;
    public int $Expires;

    static public function GetToken($uuid): UserVerificationToken | null
    {
        $db = Database::GetInstance();

        $stmt = $db->PDO->prepare("SELECT * FROM UserVerificationToken WHERE uuid=:uuid");
        $stmt->execute([
            "uuid" => $uuid,
        ]);

        if (!$stmt->rowCount())
            return null;

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return new UserVerificationToken($result["uuid"], $result["userId"], $result["expires"]);
    }

    static public function CreateToken($userId): UserVerificationToken
    {
        $db = Database::GetInstance();

        $token = new UserVerificationToken(uuidv4(), $userId, time() + 3600 * 24);

        $stmt = $db->PDO->prepare("INSERT UserVerificationToken (uuid, userId, expires) VALUE(:uuid, :userId, :expires)");
        $stmt->execute([
            "uuid" => $token->UUID,
            "userId" => $token->UserId,
            "expires" => $token->Expires,
        ]);

        return $token;
    }
    
    public function __construct(string $uuid, int $userId, int $expires)
    {
        parent::__construct();
        
        $this->UUID = $uuid;
        $this->UserId = $userId;
        $this->Expires = $expires;
    }

    public function IsExpired()
    {
        return time() > $this->Expires;
    }

    public function Destory()
    {
        $stmt = $this->m_database->PDO->prepare("DELETE FROM UserVerificationToken WHERE uuid=:uuid");
        $stmt->execute(["uuid" => $this->UUID]);
    }
}
