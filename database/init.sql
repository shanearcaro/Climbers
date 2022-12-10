CREATE TABLE Users (
    userid INT NOT NULL AUTO_INCREMENT,
    username varchar(50) NOT NULL UNIQUE,
    email varchar(50) NOT NULL,
    hash varchar(100) NOT NULL,
    temp varchar(100) NOT NULL,
    PRIMARY KEY(userid)
);

ALTER TABLE Users
    ADD CONSTRAINT uq_users UNIQUE(username, email);

CREATE TABLE Chats (
    chatid INT NOT NULL AUTO_INCREMENT,
    area varchar(100) NOT NULL,
    time varchar(100) NOT NULL,
    PRIMARY KEY(chatid)
);

ALTER TABLE Chats
    ADD CONSTRAINT uq_chats UNIQUE(area, time);

CREATE TABLE ChatMembers (
    userid INT NOT NULL,
    chatid INT NOT NULL,
    FOREIGN KEY(userid) REFERENCES Users(userid),
    FOREIGN KEY(chatid) REFERENCES Chats(chatid),
    PRIMARY KEY(userid, chatid)
);

-- Shouldn't be set to GroupMembers to avoid foregin key join duplication
CREATE TABLE ChatMessages (
    messageid INT NOT NULL AUTO_INCREMENT,
    userid INT NOT NULL,
    chatid INT NOT NULL,
    message TEXT NOT NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userid) REFERENCES Users(userid),
    FOREIGN KEY (chatid) REFERENCES Chats(chatid),
    PRIMARY KEY(messageid)
);

CREATE TABLE UserFriends (
    connectionid INT NOT NULL AUTO_INCREMENT,
    userid INT NOT NULL,
    friendid INT NOT NULL,
    accepted BIT NOT NULL DEFAULT 0,
    PRIMARY KEY (connectionid),
    FOREIGN KEY(userid) REFERENCES Users(userid),
    FOREIGN KEY(friendid) REFERENCES Users(userid)
);

CREATE TABLE UserBlocks (
    userid INT NOT NULL,
    blockid INT NOT NULL,
    FOREIGN KEY(userid) REFERENCES Users(userid),
    FOREIGN KEY(blockid) REFERENCES Users(userid),
    PRIMARY KEY(userid, blockid)
);

CREATE TABLE UserStats (
    statid INT NOT NULL,
    userid INT NOT NULL,
    climbuuid VARCHAR(40),
    timestamp DATETIME,
    FOREIGN KEY(userid) REFERENCES Users(userid),
    PRIMARY KEY(statid, userid)
);