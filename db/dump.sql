-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 27. Jan 2021 um 15:11
-- Server-Version: 10.4.17-MariaDB
-- PHP-Version: 7.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `m426_chatattack_db`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `from_user` int(11) DEFAULT NULL,
  `to_user` int(11) DEFAULT NULL,
  `msg` varchar(1023) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
-- Trigger `chat`
--
DELIMITER $$
CREATE TRIGGER `trigger_chat_log` AFTER INSERT ON `chat` FOR EACH ROW INSERT INTO `log` (`time`, `from_user_name`, `to_user_name`, `msg`) 
SELECT * FROM
   (SELECT chat.time as time
FROM chat
ORDER BY chat.time DESC 
LIMIT 1) as time_select
,
    (SELECT user.username AS from_user
FROM chat 
INNER JOIN user 
ON chat.from_user=user.id 
ORDER BY chat.time DESC 
LIMIT 1) as from_user_select
    ,
    (SELECT user.username as to_user
FROM chat 
INNER JOIN user 
ON chat.to_user=user.id 
ORDER BY chat.time DESC 
LIMIT 1) as to_user_select
	,
   (SELECT chat.msg as msg
FROM chat
ORDER BY chat.time DESC 
LIMIT 1) as msg_select
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `from_user_name` varchar(63) COLLATE utf8mb4_bin NOT NULL,
  `to_user_name` varchar(63) COLLATE utf8mb4_bin NOT NULL,
  `msg` varchar(1023) COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `relation`
--

CREATE TABLE `relation` (
  `id` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `reciever` int(11) NOT NULL,
  `status` enum('requested','accepted','denied') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `id` int(10) NOT NULL,
  `username` varchar(63) NOT NULL,
  `credential` varchar(255) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` enum('public','private') NOT NULL DEFAULT 'private',
  `role` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`id`, `username`, `credential`, `last_activity`, `type`, `role`) VALUES
(0, 'Dev3', 'b3aca92c793ee0e9b1a9b0a5f5fc044e05140df3', '2021-01-27 11:45:31', 'public', 0),
(1, 'Dev2', 'b3aca92c793ee0e9b1a9b0a5f5fc044e05140df3', '2021-01-27 11:45:31', 'public', 1),
(2, 'Dev2', 'b3aca92c793ee0e9b1a9b0a5f5fc044e05140df3', '2021-01-27 12:55:09', 'private', 1),
(3, 'Dev1', 'b3aca92c793ee0e9b1a9b0a5f5fc044e05140df3', '2021-01-27 12:55:19', 'public', 1),
(4, 'Dev', 'b3aca92c793ee0e9b1a9b0a5f5fc044e05140df3', '2021-01-27 11:45:31', 'public', 2);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_Chat_FromUser` (`from_user`),
  ADD KEY `FK_Chat_ToUser` (`to_user`);

--
-- Indizes für die Tabelle `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `relation`
--
ALTER TABLE `relation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `relation_ibfk_11` (`sender`),
  ADD KEY `relation_ibfk_12` (`reciever`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT für Tabelle `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT für Tabelle `relation`
--
ALTER TABLE `relation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `FK_Chat_FromUser` FOREIGN KEY (`from_user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_Chat_ToUser` FOREIGN KEY (`to_user`) REFERENCES `user` (`id`);

--
-- Constraints der Tabelle `relation`
--
ALTER TABLE `relation`
  ADD CONSTRAINT `relation_ibfk_1` FOREIGN KEY (`sender`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `relation_ibfk_11` FOREIGN KEY (`sender`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `relation_ibfk_12` FOREIGN KEY (`reciever`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `relation_ibfk_2` FOREIGN KEY (`reciever`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
