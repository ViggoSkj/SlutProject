<?php

include_once "db.php";


class Chat extends DatabaseObject
{
    public int $Id;
    public int $LobbyId;

    static public function GetLobbyChat(int $lobbyId): ?Chat
    {
        $db = Database::GetInstance();
        $stmt = $db->PDO->prepare("SELECT * FROM Chat WHERE lobbyId=:lobbyId");
        $stmt->execute([
            "lobbyId" => $lobbyId
        ]);

        if ($stmt->rowCount() == 0)
            return null;

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Chat($result["lobbyId"], $result["id"]);
    }

    public function __construct(int $lobbyId, int $id = -1)
    {
        parent::__construct();

        $this->Id = $id;
        $this->LobbyId = $lobbyId;
    }

    public function Save(): void
    {
        if ($this->Id == -1) {
            $stmt = $this->m_database->PDO->prepare("INSERT INTO Chat (lobbyId) VALUES(:lobbyId)");
            $stmt->execute(["lobbyId" => $this->LobbyId]);
            $this->Id = $this->m_database->PDO->lastInsertId();
        } else {
            // will not happen so wont implement
        }
    }

    public function WriteMessage(int $userId, string $message)
    {
        $message = new ChatMessage($this->Id, $userId, $message);
        $message->Save();
    }

    public function GetMessagesAfter(int $messageId): array
    {
        $stmt = $this->m_database->PDO->prepare("SELECT * FROM ChatMessage WHERE id > :messageId AND chatId = :chatId");
        $stmt->execute([
            "messageId" => $messageId,
            "chatId" => $this->Id
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $messages = [];

        foreach ($results as $result) {
            array_push($messages, new ChatMessage($this->Id, $result["userId"], $result["message"], $result["id"]));
        }

        return $messages;
    }
}

class ChatMessage extends DatabaseObject
{
    public int $ChatId;
    public int $UserId;
    public string $Message;
    public int $Id;

    public function __construct(int $chatId, int $userId, string $message, int $id = -1)
    {
        parent::__construct();

        $this->ChatId = $chatId;
        $this->UserId = $userId;
        $this->Message = $message;
        $this->Id = $id;
    }

    public function Save(): void
    {
        if ($this->Id == -1) {
            $stmt = $this->m_database->PDO->prepare("INSERT INTO ChatMessage (chatId, userId, message) VALUES(:chatId, :userId, :message)");
            $stmt->execute([
                "chatId" => $this->ChatId,
                "userId" => $this->UserId,
                "message" => $this->Message,
            ]);
            $this->Id = $this->m_database->PDO->lastInsertId();
        } else {
            // will not happen so wont implement
        }
    }
}