-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 22. Mrz 2022 um 17:43
-- Server-Version: 10.4.22-MariaDB
-- PHP-Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `itp_database`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `assignment`
--

CREATE TABLE `assignment` (
  `pk_assignment_id` int(11) NOT NULL,
  `fk_user_id` int(11) NOT NULL,
  `fk_group_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_time` timestamp NULL DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `text` text DEFAULT NULL,
  `file_path` varchar(260) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat`
--

CREATE TABLE `chat` (
  `pk_chat_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `groups`
--

CREATE TABLE `groups` (
  `pk_group_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `fk_chat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `message`
--

CREATE TABLE `message` (
  `pk_message_id` int(11) NOT NULL,
  `fk_user_id` int(11) NOT NULL,
  `fk_chat_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `text` text NOT NULL,
  `file_path` varchar(260) DEFAULT NULL,
  `image_path` varchar(260) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `student_upload`
--

CREATE TABLE `student_upload` (
  `pk_upload_id` int(11) NOT NULL,
  `fk_user_id` int(11) NOT NULL,
  `fk_assignment_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(260) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `pk_user_id` int(11) NOT NULL,
  `fk_user_type` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(260) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_group`
--

CREATE TABLE `user_group` (
  `fk_group_id` int(11) NOT NULL,
  `fk_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_type`
--

CREATE TABLE `user_type` (
  `pk_user_type_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `user_type`
--

INSERT INTO `user_type` (`pk_user_type_id`, `name`) VALUES
(2, 'student'),
(1, 'teacher');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `assignment`
--
ALTER TABLE `assignment`
  ADD PRIMARY KEY (`pk_assignment_id`),
  ADD KEY `c_assignment_group` (`fk_group_id`),
  ADD KEY `c_assignment_user` (`fk_user_id`);

--
-- Indizes für die Tabelle `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`pk_chat_id`);

--
-- Indizes für die Tabelle `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`pk_group_id`),
  ADD KEY `c_groups_chat` (`fk_chat_id`);

--
-- Indizes für die Tabelle `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`pk_message_id`),
  ADD KEY `c_message_chat` (`fk_chat_id`),
  ADD KEY `c_message_user` (`fk_user_id`);

--
-- Indizes für die Tabelle `student_upload`
--
ALTER TABLE `student_upload`
  ADD PRIMARY KEY (`pk_upload_id`),
  ADD KEY `c_student_upload_assignment` (`fk_assignment_id`),
  ADD KEY `c_student_upload_user` (`fk_user_id`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`pk_user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `c_user_usertype` (`fk_user_type`);

--
-- Indizes für die Tabelle `user_group`
--
ALTER TABLE `user_group`
  ADD PRIMARY KEY (`fk_group_id`,`fk_user_id`),
  ADD KEY `c_user_group_user` (`fk_user_id`);

--
-- Indizes für die Tabelle `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`pk_user_type_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `assignment`
--
ALTER TABLE `assignment`
  MODIFY `pk_assignment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `chat`
--
ALTER TABLE `chat`
  MODIFY `pk_chat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `groups`
--
ALTER TABLE `groups`
  MODIFY `pk_group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `message`
--
ALTER TABLE `message`
  MODIFY `pk_message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `student_upload`
--
ALTER TABLE `student_upload`
  MODIFY `pk_upload_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `pk_user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `user_type`
--
ALTER TABLE `user_type`
  MODIFY `pk_user_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `assignment`
--
ALTER TABLE `assignment`
  ADD CONSTRAINT `c_assignment_group` FOREIGN KEY (`fk_group_id`) REFERENCES `groups` (`pk_group_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `c_assignment_user` FOREIGN KEY (`fk_user_id`) REFERENCES `user` (`pk_user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `c_groups_chat` FOREIGN KEY (`fk_chat_id`) REFERENCES `chat` (`pk_chat_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `c_message_chat` FOREIGN KEY (`fk_chat_id`) REFERENCES `chat` (`pk_chat_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `c_message_user` FOREIGN KEY (`fk_user_id`) REFERENCES `user` (`pk_user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `student_upload`
--
ALTER TABLE `student_upload`
  ADD CONSTRAINT `c_student_upload_assignment` FOREIGN KEY (`fk_assignment_id`) REFERENCES `assignment` (`pk_assignment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `c_student_upload_user` FOREIGN KEY (`fk_user_id`) REFERENCES `user` (`pk_user_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `c_user_usertype` FOREIGN KEY (`fk_user_type`) REFERENCES `user_type` (`pk_user_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `user_group`
--
ALTER TABLE `user_group`
  ADD CONSTRAINT `c_user_group_groups` FOREIGN KEY (`fk_group_id`) REFERENCES `groups` (`pk_group_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `c_user_group_user` FOREIGN KEY (`fk_user_id`) REFERENCES `user` (`pk_user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
