-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2024 at 11:42 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barangay`
--

-- --------------------------------------------------------

--
-- Table structure for table `evac1`
--

CREATE TABLE `evac1` (
  `residentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evac1`
--

INSERT INTO `evac1` (`residentID`) VALUES
(3),
(3),
(1),
(22);

-- --------------------------------------------------------

--
-- Table structure for table `evac2`
--

CREATE TABLE `evac2` (
  `residentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evac2`
--

INSERT INTO `evac2` (`residentID`) VALUES
(4),
(13);

-- --------------------------------------------------------

--
-- Table structure for table `evac3`
--

CREATE TABLE `evac3` (
  `residentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaccenter`
--

CREATE TABLE `evaccenter` (
  `evac1` int(11) DEFAULT NULL,
  `evac2` int(11) DEFAULT NULL,
  `evac3` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `residentID` int(11) NOT NULL,
  `fName` varchar(255) NOT NULL,
  `Mname` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `sex` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `adminID` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`adminID`, `username`, `password`) VALUES
(1, 'admin', 'admin'),
(2, 'jose', '123'),
(3, 'rej', 'rej123'),
(4, 'anton', 'anton123'),
(5, 'mike', 'mike123'),
(6, 'david', 'david123'),
(7, 'ccs', 'ccs123'),
(8, 'secretary', 'secretary123');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_residents`
--

CREATE TABLE `tbl_residents` (
  `residentID` int(11) NOT NULL,
  `kinship` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `fName` varchar(255) NOT NULL,
  `mName` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `presentAdd` varchar(550) NOT NULL,
  `provAdd` varchar(550) NOT NULL,
  `sex` varchar(15) NOT NULL,
  `civilStat` varchar(255) NOT NULL,
  `dateOfBirth` text NOT NULL,
  `placeOfBirth` text NOT NULL,
  `height` float NOT NULL,
  `weight` float NOT NULL,
  `contactNo` text NOT NULL,
  `religion` varchar(255) NOT NULL,
  `emailAdd` text NOT NULL,
  `famComposition` int(11) NOT NULL,
  `pwd` text NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_residents`
--

INSERT INTO `tbl_residents` (`residentID`, `kinship`, `lastName`, `fName`, `mName`, `age`, `presentAdd`, `provAdd`, `sex`, `civilStat`, `dateOfBirth`, `placeOfBirth`, `height`, `weight`, `contactNo`, `religion`, `emailAdd`, `famComposition`, `pwd`, `latitude`, `longitude`) VALUES
(1, 'Solo Living', 'Mercado', 'Amanda', 'Malones', 27, 'Urban Poor, Petals Ville, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Anilao, Pavia, Iloilo', 'Female', 'Separated', '04/24/1998', 'Iloilo Mission Hospital, Mission Rd, Jaro, Iloilo City, Iloilo', 145, 78, '09789577328', 'Roman Catholic', 'althea@gmail.com', 1, 'YES', 10.73904676, 122.55503833),
(2, 'Head of Family', 'Malonzo', 'Mike', 'Ramos', 47, 'Dama de Noche Road, Petals Ville, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Lumbia, Estancia, Iloilo', 'Male', 'Single', '05/14/1997', 'LCS Center of Health, Balasan, Iloilo', 157, 56, '09789657433', 'Inglesia ni Cristo', 'mMalonzo@gmail.com', 3, 'NO', 10.73790628, 122.55526096),
(3, 'Solo Parent', 'Ramos', 'David', 'James', 37, 'Dirham Street, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Balabag, Sta. Barbara, Iloilo', 'Male', 'Separated', '04/14/1908', 'Western Visayas Sanitarium, Sta. Barbara, Iloilo', 169, 55, '09235578321', 'Roman Catholic', 'davidRamos@gmail.com', 3, 'NO', 10.73477559, 122.55644649),
(4, 'Solo Parent', 'Castaño', 'Dave', 'Pedregosa', 45, 'Won Street, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Lumbia, Estancia, Iloilo', 'Male', 'Separated', '05/21/1979', 'LCS Center of Health, Balasan, Iloilo', 167, 49, '09789567453', 'Roman Catholic', 'dCastan@gmail.com', 2, 'NO', 10.73379000, 122.55611926),
(5, 'Solo Living', 'Andrada', 'Michael', 'Malonzo', 35, 'Sampaguita Street, Petals Ville, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Balabag, Sta. Barbara, Iloilo', 'Male', 'Single', '05/21/1989', 'Western Visayas Sanitarium, Sta. Barbara, Iloilo', 178, 67, '09894567832', 'Roman Catholic', 'micAndrada@gmail.com', 1, 'NO', 10.73768698, 122.55698025),
(6, 'Solo Living', 'Ramos', 'John', 'Alcantara', 31, 'Dollar Extension, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Lumbia, Estancia, Iloilo', 'Male', 'Single', '05/27/1991', 'LCS Center of Health, Balasan, Iloilo', 157, 55, '09678943562', 'Roman Catholic', 'jRamos@gmail.com', 1, 'NO', 10.73404826, 122.55769640),
(7, 'Spouse', 'Apolinario', 'Amanda', 'Alfonso', 55, 'Napoleon Residences, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Lumbia, Estancia, Iloilo', 'Female', 'Married', '06/03/1969', 'LCS Center of Health, Balasan, Iloilo', 145, 55, '097789567893', 'Roman Catholic', 'amanda@gmail.com', 3, 'NO', 10.73486519, 122.55498737),
(8, 'Solo Living', 'Santos', 'Mike', 'Silverio', 34, 'Colon Drive, Bankers Village Ⅳ, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Anilao, Pavia, Iloilo', 'Male', 'Single', '04/24/1998', 'Iloilo Mission Hospital, Mission Rd, Jaro, Iloilo City, Iloilo', 157, 55, '09687456872', 'Roman Catholic', 'mSantos@gmail.com', 1, 'YES', 10.73858090, 122.55822212),
(9, 'Solo Living', 'Dalisay', 'David', 'Apolinario', 35, 'Peso Avenue, Bankers Village Ⅳ, Quintin Salas, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Balabag, Sta. Barbara, Iloilo', 'Male', 'Single', '06/03/1996', 'Western Visayas Sanitarium, Sta. Barbara, Iloilo', 157, 67, '09567894351', 'Roman Catholic', 'dDalisay@gmail.com', 1, 'NO', 10.73715350, 122.56011844),
(10, 'Solo Parent', 'Santos', 'Jake', 'Cruz', 31, 'Judith Lazariaga Tiongco Elementary School, Delfin Gonzales Avenue, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Anilao, Pavia, Iloilo', 'Male', 'Separated', '05/16/1993', 'Iloilo Mission Hospital, Mission Rd, Jaro, Iloilo City, Iloilo', 176, 57, '09678934764', 'Roman Catholic', 'jSantos@gmail.com', 3, 'NO', 10.73352326, 122.55408347),
(11, 'Dependent', 'Padilla', 'Vincent', 'Jimenez', 27, 'Yen Street, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Anilao, Pavia, Iloilo', 'Male', 'Single', '07/08/1997', 'loilo Mission Hospital, Mission Rd, Jaro, Iloilo City, Iloilo', 189, 75, '09589773243', 'Inglesia ni Cristo', 'vincePad@gmail.com', 2, 'NO', 10.73551347, 122.55640894),
(12, 'Solo Living', 'Luna', 'Antonio', 'Diaz', 35, 'Cruzado Street, Bankers Village Ⅳ, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Lumbia, Estancia, Iloilo', 'Male', 'Separated', '05/15/1989', 'LCS Center of Health, Balasan, Iloilo', 155, 45, '09678543261', 'Roman Catholic', 'lunaAntonio@gmail.com', 1, 'NO', 10.73693124, 122.55742818),
(13, 'Solo Living', 'Kim', 'Lisa', 'Lim', 25, 'Pound Street, Banker\'s Village, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Anilao, Pavia, Iloilo', 'Female', 'Single', '06/27/1996', 'Iloilo Mission Hospital, Mission Rd, Jaro, Iloilo City, Iloilo', 155, 48, '09678935672', 'Roman Catholic', 'kimLisa@gmail.com', 1, 'NO', 10.73587186, 122.55788952),
(14, 'Solo Living', 'Romualdez', 'Alex', 'Mercardo', 27, 'Pound Street, Banker\'s Village, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Lumbia, Estancia, Iloilo', 'Male', 'Single', '06/03/1997', 'LCS Center of Health, Balasan, Iloilo', 143, 45, '09845734216', 'Roman Catholic', 'alexRomualdez@gmail.com', 1, 'NO', 10.73569794, 122.55807728),
(15, 'Solo Living', 'Apolinario', 'Amelia', 'Ambrosyo', 27, 'Shekel Street Phase IV, Banker\'s Village, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Balabag, Sta. Barbara, Iloilo', 'Female', 'Single', '06/03/1997', 'Western Visayas Sanitarium, Sta. Barbara, Iloilo', 142, 55, '09678432451', 'Roman Catholic', 'ameliaApolinario@gmail.com', 1, 'NO', 10.73538170, 122.55729944),
(16, 'Head of Family', 'Villanueva', 'Armando', 'Reyes', 47, 'Riyal Street, Banker\'s Village, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Anilao, Pavia, Iloilo', 'Male', 'Married', '06/03/1965', 'Iloilo Mission Hospital, Mission Rd, Jaro, Iloilo City, Iloilo', 176, 57, '0947954321', 'Inglesia ni Cristo', 'armanV@gmail.com', 4, 'NO', 10.73605106, 122.55749792),
(17, 'Dependent', 'Alejandrino', 'Antoinette', 'Salcedo', 37, 'Rupee Street Phase IV, Banker\'s Village, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Anilao, Pavia, Iloilo', 'Female', 'Single', '07/30/1981', 'Iloilo Mission Hospital, Mission Rd, Jaro, Iloilo City, Iloilo', 151, 53, '09458796521', 'Roman Catholic', 'antoinette@gmail.com', 5, 'YES', 10.73617755, 122.55711704),
(18, 'Solo Living', 'Diaz', 'Michael', 'Hechanova', 25, 'Rupee Street Phase IV, Banker\'s Village, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Anilao, Pavia, Iloilo', 'Male', 'Single', '07/15/1999', 'Iloilo Mission Hospital, Mission Rd, Jaro, Iloilo City, Iloilo', 178, 78, '09587934671', 'Roman Catholic', 'michDiaz@gmail.com', 1, 'NO', 10.73614593, 122.55706340),
(19, 'Solo Living', 'Bantolo', 'Jose Miguel', 'Comoda', 29, 'Guarani Street Phase II B, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'N/A', 'Male', 'Single', '04/15/2002', 'Iloilo Mission Hospital, Mission Rd, Jaro, Iloilo City, Iloilo', 178, 68, '09478477321', 'Roman Catholic', 'jMBantolo@gmail.com', 5, 'NO', 10.73462365, 122.55732894),
(20, 'Head of Family', 'Jackson', 'Phil', 'Estrella', 47, 'Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Anilao, Pavia, Iloilo', 'Male', 'Married', '06/25/1965', 'Iloilo Mission Hospital, Mission Rd, Jaro, Iloilo City, Iloilo', 198, 82, '09466783421', 'Roman Catholic', 'phil@gmail.com', 5, 'NO', 10.73409660, 122.55606294),
(21, 'Spouse', 'Poral', 'Liza', 'Alicante', 43, 'Dama de Noche Road, Petals Ville, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Brgy. Lumbia, Estancia, Iloilo', 'Female', 'Married', '07/19/1972', 'LCS Center of Health, Balasan, Iloilo', 145, 54, '09238756341', 'Roman Catholic', 'liz2gmail.com', 5, 'NO', 10.73766911, 122.55507320),
(22, 'Dependent', 'Rosaldes', 'Jerome', 'Viente', 22, 'Casper Street, Remon Ville, Tabuc Suba, Jaro, Iloilo City, Western Visayas, 5000, Philippines', 'Capiz', 'Male', 'Single', '07/15/2001', 'Roxas', 170, 75, '09294828520', 'Catholic', 'rej@gmail.com', 2, 'NO', 10.72975383, 122.56022787);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`residentID`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `tbl_residents`
--
ALTER TABLE `tbl_residents`
  ADD PRIMARY KEY (`residentID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `residentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_residents`
--
ALTER TABLE `tbl_residents`
  MODIFY `residentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
