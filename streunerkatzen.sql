-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 28. Mrz 2019 um 22:55
-- Server-Version: 10.1.38-MariaDB
-- PHP-Version: 7.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `streunerkatzen`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `streunerkatzen_cats`
--

CREATE TABLE `streunerkatzen_cats` (
  `ID` int(11) NOT NULL,
  `ClassName` enum('Streunerkatzen\\Cat') DEFAULT 'Streunerkatzen\\Cat',
  `LastEdited` datetime DEFAULT NULL,
  `Created` datetime DEFAULT NULL,
  `Title` varchar(250) DEFAULT NULL,
  `PublishTime` datetime DEFAULT NULL,
  `Age` varchar(250) DEFAULT NULL,
  `Gender` enum('nicht bekannt','männlich','weiblich') DEFAULT 'nicht bekannt',
  `HasPetCollar` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `PetCollarDescription` varchar(250) DEFAULT NULL,
  `Characteristics` varchar(500) DEFAULT NULL,
  `ColorCharacteristics` varchar(500) DEFAULT NULL,
  `EyeColor` varchar(50) DEFAULT NULL,
  `ChipNumber` varchar(100) DEFAULT NULL,
  `Tattoo` varchar(250) DEFAULT NULL,
  `Breed` varchar(250) DEFAULT NULL,
  `IsCastrated` enum('nicht bekannt','ja','nein') DEFAULT 'nicht bekannt',
  `IsHouseCat` enum('nicht bekannt','ja','nein') DEFAULT 'nicht bekannt',
  `IsChipped` enum('nicht bekannt','ja','nein') DEFAULT 'nicht bekannt',
  `BehaviourOwner` varchar(500) DEFAULT NULL,
  `BehaviourStranger` varchar(500) DEFAULT NULL,
  `LostFoundDate` date DEFAULT NULL,
  `Street` varchar(250) DEFAULT NULL,
  `Town` varchar(250) DEFAULT NULL,
  `Zipcode` int(11) NOT NULL DEFAULT '0',
  `Country` varchar(250) DEFAULT NULL,
  `LostFoundDescription` varchar(1000) DEFAULT NULL,
  `MoreInfo` varchar(1000) DEFAULT NULL,
  `LostFoundTime` varchar(250) DEFAULT NULL,
  `LostFoundStatus` varchar(250) DEFAULT NULL,
  `HairLength` varchar(250) DEFAULT NULL,
  `ReporterID` int(11) NOT NULL DEFAULT '0',
  `OwnerID` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `streunerkatzen_cats`
--
ALTER TABLE `streunerkatzen_cats`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ClassName` (`ClassName`),
  ADD KEY `ReporterID` (`ReporterID`),
  ADD KEY `OwnerID` (`OwnerID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `streunerkatzen_cats`
--
ALTER TABLE `streunerkatzen_cats`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
