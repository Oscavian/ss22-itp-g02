-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 26. Jun 2022 um 16:51
-- Server-Version: 10.4.21-MariaDB
-- PHP-Version: 8.0.10

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `itp_database`
--

-- --------------------------------------------------------

--
-- Daten für Tabelle `assignment`
--

INSERT INTO `assignment` (`pk_assignment_id`, `fk_user_id`, `fk_group_id`, `time`, `due_time`, `title`, `text`, `file_path`) VALUES
(3, 47, 5, '2022-06-26 13:31:33', '2022-07-07 16:00:00', 'Alphabet-Übungsblatt', 'Füllt das Alphabet-Übungsblatt aus und findet zu jedem Buchstaben ein Wort. \r\nViel Spaß! :)', 'uploads/assignments/attachments/1arbeitsblatt-alphabet-woerter-aufschreiben.pdf'),
(4, 47, 5, '2022-06-26 13:38:45', '2022-07-05 16:00:00', 'Größer, kleiner oder gleich?', 'Füllt das Übungsblatt über die Zeichen >, < und = aus. \r\n\r\nBei Nummer 1 müsst ihr immer entscheiden, ob die erste Zahl kleiner, größer oder gleich wie die zweite ist.\r\n\r\nBei Nummer zwei steht manchmal schon ein >, < oder = dort. Da müsst ihr dann eine Zahl finden, für die der Vergleich richtig ist.\r\n\r\nStellt Fragen dazu bitte einfach im Klassenchat.\r\nIch wünsche euch viel Erfolg!', 'uploads/assignments/attachments/1arbeitsblatt-groesser-kleiner-z10-zusatzaufgabe.pdf'),
(5, 47, 5, '2022-06-26 13:57:42', '2022-06-30 16:00:00', 'Gerade und ungerade Zahlen', 'Bei diesem Arbeitsblatt müsst ihr unterscheiden, welche Zahlen gerade und welche ungerade sind. Die gerade malt ihr mit violetter Farbe aus, die ungeraden mit gelber Farbe.\r\nViel Freude beim Ausmalen :)', 'uploads/assignments/attachments/1arbeitsblatt-gerade-ungerade-zahlen-01.pdf'),
(6, 47, 5, '2022-06-26 14:02:31', '2022-06-27 16:00:00', 'Buchstaben schreiben', 'Mithilfe von diesem Übungsblatt könnt ihr noch einmal üben, wie man alle Buchstaben in Druckschrift schreibt. Versucht, euch so genau wie möglich an die Vorlage zu halten.\r\n\r\nIhr dürft jede Farbe benutzen, die ihr wollt, solange die Buchstaben gut lesbar sind :)', 'uploads/assignments/attachments/1arbeitsblatt-alphabet-uebersicht.pdf');

-- --------------------------------------------------------

--
-- Daten für Tabelle `chat`
--

INSERT INTO `chat` (`pk_chat_id`, `name`) VALUES
(5, 'Chat: 1A');

-- --------------------------------------------------------

--
-- Daten für Tabelle `groups`
--

INSERT INTO `groups` (`pk_group_id`, `name`, `fk_chat_id`) VALUES
(5, '1A', 5);

-- --------------------------------------------------------

--
-- Daten für Tabelle `message`
--

INSERT INTO `message` (`pk_message_id`, `fk_user_id`, `fk_chat_id`, `time`, `text`, `file_path`, `image_path`) VALUES
(4, 48, 5, '2022-06-26 14:10:47', 'Frau Lehrerin, dürfen wir bei beim Arbeitsblatt gerade ungerade auch andere Farben benutzen?', NULL, NULL),
(5, 49, 5, '2022-06-26 14:16:54', 'Soll man da einen Farbstift nehmen?', NULL, NULL),
(6, 47, 5, '2022-06-26 14:27:52', 'Lieber Alex, du darfst natürlich andere Farben auch benutzen. Wichtig ist nur, dass du genau eine Farbe für die geraden und eine Farbe für die ungeraden Zahlen verwendest', NULL, NULL),
(7, 47, 5, '2022-06-26 14:28:23', 'Liebe Emma, du kannst Farbstifte verwenden, Filzstifte sind aber auch ok', NULL, NULL),
(8, 50, 5, '2022-06-26 14:32:07', 'Ich verstehe die Nummer zwei bei größer, kleiner oder gleich nicht', NULL, NULL),
(9, 47, 5, '2022-06-26 14:33:40', 'Womit genau hast du denn da ein Problem?', NULL, NULL),
(10, 50, 5, '2022-06-26 14:34:26', 'Ich weiß nicht, welche Zahlen ich einsetzen soll', NULL, NULL),
(11, 47, 5, '2022-06-26 14:36:57', 'Bei den Beispielen, wo du eine Zahl einsetzen musst, steht schon ein Vergleichungszeichen und eine andere Zahl dort. Du musst dir dann das gegebene Zeichen und die Zahl anschauen und überlegen, welche Zahl du einsetzen kannst, damit der Vergleich richtig ist.', NULL, NULL),
(12, 47, 5, '2022-06-26 14:37:37', 'Wenn dort also zum Beispiel __ < 5 steht, dann überlegst du, welche Zahlen kleiner als 5 sind und setzt eine davon ein.', NULL, NULL),
(13, 50, 5, '2022-06-26 14:39:45', 'ok ich versuchs mal', NULL, NULL),
(14, 51, 5, '2022-06-26 14:41:50', 'Frau Lehrerin, mein Drucker geht nicht mehr. Wie soll ich die Arbeitsblätter machen?', NULL, NULL),
(15, 47, 5, '2022-06-26 14:45:10', 'Lieber Lukas, wenn du die Arbeitsblätter nicht ausdrucken kannst, dann kannst du die Angabe in dein Hausübungsheft schreiben und dort erledigen', NULL, NULL);

-- --------------------------------------------------------

--
-- Daten für Tabelle `student_upload`
--

INSERT INTO `student_upload` (`pk_upload_id`, `fk_user_id`, `fk_assignment_id`, `time`, `file_path`) VALUES
(2, 48, 6, '2022-06-26 14:09:31', 'uploads/assignments/submissions/1arbeitsblatt-alphabet-uebersicht-alexmaier.pdf'),
(3, 48, 4, '2022-06-26 14:15:09', 'uploads/assignments/submissions/1arbeitsblatt-groesser-kleiner-z10-zusatzaufgabe-alexmaier.pdf'),
(4, 49, 6, '2022-06-26 14:23:35', 'uploads/assignments/submissions/1arbeitsblatt-alphabet-uebersicht-emmawalz.pdf');

-- --------------------------------------------------------

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`pk_user_id`, `fk_user_type`, `first_name`, `last_name`, `username`, `password`) VALUES
(47, 1, 'Paulina', 'Huber', 'huber.paulina', '$2y$10$b3R75nE8VDUe8TS6RbrjP.xo1lafGjo6GvgDGkeee0ygBqVW4ws2S'),
(48, 2, 'Alex', 'Maier', 'maier.alex', '$2y$10$gR/LXlkXgpxTJzshaIROE.r2D0VYzZ1qv.8ynDjy5h3XphTDFQ9ua'),
(49, 2, 'Emma', 'Walz', 'walz.emma', '$2y$10$CTJBSFK/Oos061KgxWMiIuohz413PkgWxAbBTQfkYP7Ihg2asYEhy'),
(50, 2, 'Bea', 'Stolzer', 'stolzer.bea', '$2y$10$OuAbuwqTAbqCt/h8zVppUeFPYVEP9qvjAp4/yUNDy1ku5BqFAHbti'),
(51, 2, 'Lukas', 'Leyer', 'leyer.lukas', '$2y$10$J6KqKfPJz4RCeVTorkj3NuMFTZmmjYNjiDM.VZH7Jrm4C/U/KK2Ci');

-- --------------------------------------------------------

--
-- Daten für Tabelle `user_group`
--

INSERT INTO `user_group` (`fk_group_id`, `fk_user_id`) VALUES
(5, 47),
(5, 48),
(5, 49),
(5, 50),
(5, 51);

-- --------------------------------------------------------
