-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Värd: localhost
-- Tid vid skapande: 13 jan 2023 kl 01:44
-- Serverversion: 10.5.18-MariaDB-0+deb11u1
-- PHP-version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `lokaltrafik`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `callTime`
--

CREATE TABLE `callTime` (
  `id` int(11) NOT NULL COMMENT 'Id of this callTime entry.',
  `title` text NOT NULL COMMENT 'Title do display.',
  `stopId` int(11) NOT NULL COMMENT 'Id of stop in db.',
  `firstCall` time NOT NULL DEFAULT '09:00:00' COMMENT 'Time of first allowed call.',
  `lastCall` time NOT NULL DEFAULT '21:00:00' COMMENT 'Time of last allowed call.',
  `daysToCall` varchar(3) NOT NULL DEFAULT '1-5' COMMENT 'Syntax = "a-b" where a is the first day and b is the last day. Monday is 1 and Sunday is 7.',
  `minutesBetweenCalls` int(11) NOT NULL DEFAULT 1 COMMENT 'Minimum amount of minutes between calls',
  `attributes` text DEFAULT NULL COMMENT 'Extra attributes for \r\napi querry'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Tabellstruktur `stops`
--

CREATE TABLE `stops` (
  `id` int(11) NOT NULL COMMENT 'Our id of this stop.',
  `extId` int(11) NOT NULL COMMENT 'external Id used to querry API about data.',
  `name` text NOT NULL DEFAULT 'Okänt namn' COMMENT 'Name',
  `travelTime` int(11) NOT NULL DEFAULT 0 COMMENT 'Time to travel to stop'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='stops to get data for from API';

--
-- Tabellstruktur `timeTable`
--

CREATE TABLE `timeTable` (
  `callId` int(11) NOT NULL,
  `collectionUnixTimeStamp` int(11) DEFAULT NULL,
  `direction` text NOT NULL,
  `lineName` text NOT NULL,
  `unixTimeStamp` int(11) DEFAULT NULL,
  `rtUnixTimeStamp` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Index för dumpade tabeller
--

--
-- Index för tabell `callTime`
--
ALTER TABLE `callTime`
  ADD PRIMARY KEY (`id`),
  ADD KEY `CASCADE_1` (`stopId`);

--
-- Index för tabell `stops`
--
ALTER TABLE `stops`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `timeTable`
--
ALTER TABLE `timeTable`
  ADD KEY `CASCADE_2` (`callId`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `callTime`
--
ALTER TABLE `callTime`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id of this callTime entry.', AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT för tabell `stops`
--
ALTER TABLE `stops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Our id of this stop.', AUTO_INCREMENT=13;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `callTime`
--
ALTER TABLE `callTime`
  ADD CONSTRAINT `CASCADE_1` FOREIGN KEY (`stopId`) REFERENCES `stops` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restriktioner för tabell `timeTable`
--
ALTER TABLE `timeTable`
  ADD CONSTRAINT `CASCADE_2` FOREIGN KEY (`callId`) REFERENCES `callTime` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
