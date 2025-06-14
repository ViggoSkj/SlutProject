CREATE TABLE AppUser (
    id integer unsigned PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(255) NOT NULL,
    passwordHash VARCHAR(255) NOT NULL,
    verified BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE UserVerificationToken (
    uuid VARCHAR(255) PRIMARY KEY,
    userId integer unsigned NOT NULL,
    expires integer unsigned NOT NULL,
    FOREIGN KEY (userId) REFERENCES AppUser(id) ON DELETE CASCADE
);

CREATE TABLE UserPasswordResetToken (
    uuid VARCHAR(255) PRIMARY KEY,
    userId integer unsigned NOT NULL,
    expires integer unsigned NOT NULL,
    FOREIGN KEY (userId) REFERENCES AppUser(id) ON DELETE CASCADE
);

CREATE TABLE Wallet (
    id integer unsigned PRIMARY KEY AUTO_INCREMENT,
    userId integer unsigned NOT NULL,
    amount integer unsigned NOT NULL,
    lastDailyRewardClaimed integer unsigned NOT NULL,
    FOREIGN KEY (userId) REFERENCES AppUser(id) ON DELETE CASCADE
);

CREATE TABLE Game (
    id integer unsigned PRIMARY KEY AUTO_INCREMENT,
    gameType VARCHAR(255) NOT NULL
);

CREATE TABLE Lobby (
    id integer unsigned PRIMARY KEY AUTO_INCREMENT,
    gameId integer unsigned NOT NULL,
    FOREIGN KEY (gameId) REFERENCES Game(id) ON DELETE CASCADE
);

CREATE TABLE LobbyOccupant (
    id integer unsigned PRIMARY KEY AUTO_INCREMENT,
    lobbyId integer unsigned NOT NULL,
    userId integer unsigned NOT NULL,
    FOREIGN KEY (lobbyId) REFERENCES Lobby(id) ON DELETE CASCADE,
    FOREIGN KEY (userId) REFERENCES AppUser(id) ON DELETE CASCADE
);

CREATE TABLE Chat (
    id integer unsigned PRIMARY KEY AUTO_INCREMENT,
    lobbyId integer unsigned NOT NULL,
    FOREIGN KEY (lobbyId) REFERENCES Lobby(id) ON DELETE CASCADE
);

CREATE TABLE ChatMessage (
    id integer unsigned PRIMARY KEY AUTO_INCREMENT,
    chatId integer unsigned NOT NULL,
    message VARCHAR(255) NOT NULL,
    userId integer unsigned NOT NULL,
    FOREIGN KEY (chatId) REFERENCES Chat(id) ON DELETE CASCADE,
    FOREIGN KEY (userId) REFERENCES AppUser(id) ON DELETE CASCADE
);

CREATE TABLE GameEvent (
    id integer unsigned PRIMARY KEY AUTO_INCREMENT,
    gameId integer unsigned NOT NULL,
    eventType VARCHAR(100) NOT NULL,
    content VARCHAR(1000) NOT NULL,
    FOREIGN KEY (gameId) REFERENCES Game(id) ON DELETE CASCADE
);