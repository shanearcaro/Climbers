CREATE TABLE Users (
    userid mediumint NOT NULL AUTO_INCREMENT,
    username varchar(50) NOT NULL,
    email varchar(50) NOT NULL,
    hash varchar(100) NOT NULL,
    salt varchar(50) NOT NULL,
    PRIMARY KEY(userid)
);

CREATE TABLE Chats (
    chatid INT NOT NULL AUTO_INCREMENT,
    area varchar(100) NOT NULL,
    time varchar(100) NOT NULL,
    PRIMARY KEY(chatid)
);

CREATE TABLE ChatMembers (
    userid mediumint NOT NULL,
    chatid INT NOT NULL,
    FOREIGN KEY(userid) REFERENCES Users(userid),
    FOREIGN KEY(chatid) REFERENCES Chats(chatid),
    PRIMARY KEY(userid, chatid)
);

-- Shouldn't be set to GroupMembers to avoid foregin key join duplication
CREATE TABLE ChatMessages(
    messageid INT NOT NULL AUTO_INCREMENT,
    userid mediumint NOT NULL,
    chatid INT NOT NULL,
    message VARCHAR(2000) NOT NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userid) REFERENCES Users(userid),
    FOREIGN KEY (chatid) REFERENCES Chats(chatid),
    PRIMARY KEY(messageid)
);

CREATE TABLE UserFriends(
    connectionid INT NOT NULL AUTO_INCREMENT,
    userid mediumint NOT NULL,
    friendid mediumint NOT NULL,
    accepted BIT NOT NULL DEFAULT 0,
    PRIMARY KEY (connectionid),
    FOREIGN KEY(userid) REFERENCES Users(userid),
    FOREIGN KEY(friendid) REFERENCES Users(userid)
);

CREATE TABLE UserBlocks(
    userid mediumint NOT NULL,
    blockid mediumint NOT NULL,
    FOREIGN KEY(userid) REFERENCES Users(userid),
    FOREIGN KEY(blockid) REFERENCES Users(userid),
    PRIMARY KEY(userid, blockid)
);