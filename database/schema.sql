-- GameRoom database layout (first-pass baseline)
-- Source: phpMyAdmin export shared on 2026-04-19 (deduplicated and normalized for repo use)

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS accounts (
  id INT NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  firstname VARCHAR(255) NOT NULL,
  lastname VARCHAR(255) NOT NULL,
  phone VARCHAR(15) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100) NOT NULL,
  activation_code VARCHAR(50) NOT NULL DEFAULT '',
  rememberme VARCHAR(255) NOT NULL DEFAULT '',
  role ENUM('Member','Admin') NOT NULL DEFAULT 'Member',
  registered DATETIME NOT NULL,
  last_seen DATETIME NOT NULL,
  guid CHAR(36) NOT NULL,
  reset VARCHAR(50) NOT NULL DEFAULT '',
  `2023` INT NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS gamelist (
  gamelistid INT NOT NULL AUTO_INCREMENT,
  yearlistid INT DEFAULT NULL,
  approved INT NOT NULL,
  emailed INT NOT NULL,
  tournamentpin INT DEFAULT NULL,
  checkedin INT NOT NULL,
  showyear VARCHAR(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT '2025',
  ownerid INT DEFAULT NULL,
  gametype VARCHAR(1) DEFAULT NULL,
  gametitle VARCHAR(255) DEFAULT NULL,
  gameid VARCHAR(255) DEFAULT NULL,
  ipdbid VARCHAR(255) DEFAULT NULL,
  manufacturer VARCHAR(255) DEFAULT NULL,
  builtyear VARCHAR(255) DEFAULT NULL,
  awards INT DEFAULT NULL,
  notes MEDIUMTEXT,
  dateadded DATETIME DEFAULT NULL,
  PRIMARY KEY (gamelistid),
  UNIQUE KEY yearlistid_unique (yearlistid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS machineissues (
  Id INT NOT NULL AUTO_INCREMENT,
  status INT NOT NULL DEFAULT 1,
  machineid INT NOT NULL,
  issue TEXT COLLATE utf8mb4_general_ci,
  opentime DATETIME DEFAULT CURRENT_TIMESTAMP,
  closetime DATETIME DEFAULT NULL,
  owner_notes TEXT COLLATE utf8mb4_general_ci,
  PRIMARY KEY (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS tmp_yearlistids (
  id INT NOT NULL AUTO_INCREMENT,
  gamelistid INT DEFAULT NULL,
  lastname VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  gametitle VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  new_yearlistid INT DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS tokens (
  id INT NOT NULL AUTO_INCREMENT,
  provider VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  provider_value TEXT COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS votes (
  voteid INT NOT NULL AUTO_INCREMENT,
  userid INT NOT NULL,
  timestamp DATETIME NOT NULL,
  bis_em INT NOT NULL,
  bis_ss INT NOT NULL,
  bis_modern INT NOT NULL,
  bis_restore INT NOT NULL,
  bis_custom INT NOT NULL,
  bis_arcade INT NOT NULL,
  PRIMARY KEY (voteid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Optional indexes/constraints often expected by the app.
CREATE INDEX idx_gamelist_ownerid ON gamelist (ownerid);
CREATE INDEX idx_votes_userid ON votes (userid);
CREATE INDEX idx_machineissues_machineid ON machineissues (machineid);

-- MySQL views from the export. Definers removed to keep this file portable.
DROP VIEW IF EXISTS gl_merge;
CREATE VIEW gl_merge AS
SELECT
  g.gamelistid,
  g.showyear,
  g.ownerid,
  g.gametype,
  g.gametitle,
  g.gameid,
  g.manufacturer,
  g.builtyear,
  g.awards,
  g.dateadded,
  a.id,
  a.username,
  a.firstname,
  a.lastname,
  a.password,
  a.email,
  a.activation_code,
  a.rememberme,
  a.role,
  a.registered,
  a.last_seen,
  a.guid
FROM gamelist g
JOIN accounts a ON g.ownerid = a.id;

DROP VIEW IF EXISTS gl_merge3;
CREATE VIEW gl_merge3 AS
SELECT
  games.showyear,
  games.gamelistid,
  games.gametitle,
  games.gametype,
  user.id AS uid,
  user.firstname,
  user.lastname,
  games.ownerid,
  games.builtyear,
  games.manufacturer,
  games.awards
FROM gamelist games
JOIN accounts user ON games.ownerid = user.id
WHERE games.showyear = 2023;

DROP VIEW IF EXISTS Shannon_View;
CREATE VIEW Shannon_View AS
SELECT
  games.gamelistid,
  games.yearlistid,
  games.gametitle,
  user.lastname,
  games.ownerid,
  user.id,
  games.showyear
FROM gamelist games
JOIN accounts user ON games.ownerid = user.id
WHERE games.showyear = 2023;
