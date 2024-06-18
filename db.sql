-- Crear la base de datos
DROP DATABASE IF EXISTS eduhacks;
CREATE DATABASE IF NOT EXISTS eduhacks CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE eduhacks;
DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS users (
    `iduser` INT AUTO_INCREMENT,
    `mail` VARCHAR(40) UNIQUE,
    `username` VARCHAR(16) UNIQUE,
    `passHash` VARCHAR(60),
    `userFirstName` VARCHAR(60),
    `userLastName` VARCHAR(120),
    `creationDate` DATETIME,
    `removeDate` DATETIME,
    `lastSignIn` DATETIME,
    `active` TINYINT(1),
    `activationDate` DATETIME,
    `activationCode` CHAR(64),
    `resetPassExpiry` DATETIME,
    `resetPassCode` CHAR(64),
    `Puntuation` INT DEFAULT 0,
    PRIMARY KEY (`iduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS ChallengeCTF;
CREATE TABLE IF NOT EXISTS ChallengeCTF (
    `IdCTF` INT AUTO_INCREMENT,
    `Name` VARCHAR(64) UNIQUE,
    `Description` VARCHAR(508),
    `Flag` VARCHAR(64),
    `DatePublish` DATETIME,
    `Value` INT,
    `FounderUser` VARCHAR(64),
    PRIMARY KEY (`IdCTF`)
);

DROP TABLE IF EXISTS Register;
CREATE TABLE IF NOT EXISTS Register (
    `iduser` INT UNIQUE,
    `IdCTF` INT UNIQUE,
    CONSTRAINT fk_Users_ChallengeCTF_1 foreign key(`iduser`) references users(`iduser`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_ChallengeCTF_Users_1 foreign key(`IdCTF`) references ChallengeCTF(`IdCTF`)
        ON UPDATE CASCADE
        ON DELETE CASCADE 
);

DROP TABLE IF EXISTS CTFinProcess;
CREATE TABLE IF NOT EXISTS CTFinProcess (
    `Name` VARCHAR(64) UNIQUE,
    `StartDate` DATE,
    `NumTries` INT,
    `iduser` INT,
    `IdCTF` INT,
    CONSTRAINT fk_Users_ChallengeCTF_2 foreign key(`iduser`) references users(`iduser`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_ChallengeCTF_Users_2 foreign key(`IdCTF`) references ChallengeCTF(`IdCTF`)
        ON UPDATE CASCADE
        ON DELETE CASCADE 
);

DROP TABLE IF EXISTS CompletedCTF;
CREATE TABLE IF NOT EXISTS CompletedCTF (
    `DateCompleted` DATE,
    `iduser` INT,
    `IdCTF` INT,    
    CONSTRAINT fk_Users_ChallengeCTF_3 foreign key(`iduser`) references users(`iduser`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_ChallengeCTF_Users_3 foreign key(`IdCTF`) references ChallengeCTF(`IdCTF`)
        ON UPDATE CASCADE
        ON DELETE CASCADE 
);

DROP TABLE IF EXISTS CTFFiles;
CREATE TABLE IF NOT EXISTS CTFFiles (
    `URL` VARCHAR(64),
    `IdCTF` INT,
    CONSTRAINT fk_ChallengeCTF_CTFFiles foreign key(`IdCTF`) references ChallengeCTF(`IdCTF`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

DROP TABLE IF EXISTS Category;
CREATE TABLE IF NOT EXISTS Category (
    `IdCategory` INT AUTO_INCREMENT,
    `Name` VARCHAR(64) UNIQUE,
    PRIMARY KEY (`IdCategory`)
);

DROP TABLE IF EXISTS Clasificate;
CREATE TABLE IF NOT EXISTS Clasificate (
    `IdCTF` INT,
    `IdCategory` INT,
    CONSTRAINT fk_ChallengeCTF_Clasificate foreign key(`IdCTF`) references ChallengeCTF(`IdCTF`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_ChallengeCTF_Category foreign key(`IdCategory`) references Category(`IdCategory`)
        ON UPDATE CASCADE
        ON DELETE CASCADE 
);