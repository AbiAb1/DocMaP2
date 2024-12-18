-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2024 at 06:23 AM
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
-- Database: `docmap1`
--

-- --------------------------------------------------------

--
-- Table structure for table `age`
--

CREATE TABLE `age` (
  `Age_ID` int(11) NOT NULL,
  `Age_Group_12To15` int(11) DEFAULT NULL,
  `School_Year_ID` int(11) NOT NULL,
  `Grade_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `age`
--

INSERT INTO `age` (`Age_ID`, `Age_Group_12To15`, `School_Year_ID`, `Grade_ID`, `UserID`) VALUES
(2, 31313, 1, 2, 3),
(3, 1112, 1, 1, 3),
(4, 232323, 1, 1, 3);

--
-- Triggers `age`
--
DELIMITER $$
CREATE TRIGGER `after_insert_age` AFTER INSERT ON `age` FOR EACH ROW BEGIN
    DECLARE totalAge INT;

    SELECT 
        SUM(Age_Group_12To15)
    INTO 
        totalAge
    FROM 
        Age
    WHERE 
        School_Year_ID = NEW.School_Year_ID;

    IF EXISTS (
        SELECT 1 
        FROM Total_Age 
        WHERE Age_ID IN (
            SELECT Age_ID 
            FROM Age 
            WHERE School_Year_ID = NEW.School_Year_ID
        )
    ) THEN
        UPDATE Total_Age
        SET Total_Age = totalAge
        WHERE Age_ID IN (
            SELECT Age_ID 
            FROM Age 
            WHERE School_Year_ID = NEW.School_Year_ID
        );
    ELSE
        INSERT INTO Total_Age (Total_Age, Age_ID)
        VALUES (totalAge, NEW.Age_ID);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `attachment`
--

CREATE TABLE `attachment` (
  `Attach_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ContentID` int(11) NOT NULL,
  `TaskID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mimeType` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachment`
--

INSERT INTO `attachment` (`Attach_ID`, `UserID`, `ContentID`, `TaskID`, `name`, `mimeType`, `size`, `uri`, `TimeStamp`) VALUES
(906, 1, 168475, 878734, '579146_BUSINESS PROPOSAL-PipeAR.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 31087, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/579146_BUSINESS PROPOSAL-PipeAR.docx', '2024-12-11 01:01:42'),
(907, 1, 168475, 878734, '659034_BUSINESS PROPOSAL-PipeAR.pdf', 'application/pdf', 126032, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/659034_BUSINESS PROPOSAL-PipeAR.pdf', '2024-12-11 01:01:42'),
(908, 1, 168475, 878734, '218843_ProfTal Logo (5).png', 'image/png', 6079, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/218843_ProfTal Logo (5).png', '2024-12-11 01:01:42'),
(909, 1, 168475, 878734, '948411_Untitled design (1).png', 'image/png', 12543, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/948411_Untitled design (1).png', '2024-12-11 01:01:42'),
(910, 1, 168475, 878734, '848618_6-Platform-Architecture-and-Design-1.pdf', 'application/pdf', 201298, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/848618_6-Platform-Architecture-and-Design-1.pdf', '2024-12-11 01:01:42'),
(911, 1, 168475, 878734, '649393_Chap 1-5(Updated) (1).pdf', 'application/pdf', 3581187, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/649393_Chap 1-5(Updated) (1).pdf', '2024-12-11 01:01:42'),
(912, 1, 168475, 878734, '593192_Chap 1-5(Updated).pdf', 'application/pdf', 3581187, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/593192_Chap 1-5(Updated).pdf', '2024-12-11 01:01:42'),
(913, 1, 168475, 878735, '183234_BUSINESS PROPOSAL-PipeAR.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 31087, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/183234_BUSINESS PROPOSAL-PipeAR.docx', '2024-12-11 01:26:35'),
(914, 1, 168475, 878735, '780710_BUSINESS PROPOSAL-PipeAR.pdf', 'application/pdf', 126032, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/780710_BUSINESS PROPOSAL-PipeAR.pdf', '2024-12-11 01:26:35'),
(915, 1, 168475, 878735, '292811_ProfTal Logo (5).png', 'image/png', 6079, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/292811_ProfTal Logo (5).png', '2024-12-11 01:26:35'),
(916, 1, 168475, 878735, '531682_Untitled design (1).png', 'image/png', 12543, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/531682_Untitled design (1).png', '2024-12-11 01:26:35'),
(917, 1, 168475, 878735, '961425_6-Platform-Architecture-and-Design-1.pdf', 'application/pdf', 201298, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/961425_6-Platform-Architecture-and-Design-1.pdf', '2024-12-11 01:26:35'),
(918, 1, 168475, 878735, '583004_207786_College-Shirt-Name-List.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 56593, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/583004_207786_College-Shirt-Name-List.docx', '2024-12-11 01:26:35'),
(919, 1, 168475, 878735, '645659_493150_BA-3201-HCI LIST.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 984653, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/645659_493150_BA-3201-HCI LIST.docx', '2024-12-11 01:26:35'),
(920, 1, 168475, 878735, '410297_453169160_2652218068270759_7675411732137419678_n.jpg', 'image/jpeg', 99235, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/410297_453169160_2652218068270759_7675411732137419678_n.jpg', '2024-12-11 01:26:35'),
(921, 1, 168475, 878736, '523903_DLL template.pdf', 'application/pdf', 519463, 'C:\\xampp\\htdocs\\DocMaP\\Admin/Attachments/523903_DLL template.pdf', '2024-12-11 01:29:35'),
(922, 1, 168475, 878738, '431990_10-1108_EJMBE-11-2021-0295.pdf', 'application/pdf', 1577021, '431990_10-1108_EJMBE-11-2021-0295.pdf', '2024-12-11 03:47:50'),
(926, 108, 168481, 878746, '846425_BUSINESS PROPOSAL-PipeAR.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 31087, 'C:\\xampp\\htdocs\\DocMaP\\DeptHead/Attachments/846425_BUSINESS PROPOSAL-PipeAR.docx', '2024-12-14 00:43:12');

-- --------------------------------------------------------

--
-- Table structure for table `chairperson`
--

CREATE TABLE `chairperson` (
  `Chairperson_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Grade_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chairperson`
--

INSERT INTO `chairperson` (`Chairperson_ID`, `UserID`, `Grade_ID`) VALUES
(1, 3, 1),
(3, 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `cohort_survival`
--

CREATE TABLE `cohort_survival` (
  `Cohort_ID` int(11) NOT NULL,
  `Cohort_Rate` decimal(5,2) DEFAULT NULL,
  `Cohort_Figure` int(11) DEFAULT NULL,
  `School_Year_ID` int(11) NOT NULL,
  `Grade_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cohort_survival`
--

INSERT INTO `cohort_survival` (`Cohort_ID`, `Cohort_Rate`, `Cohort_Figure`, `School_Year_ID`, `Grade_ID`, `UserID`) VALUES
(8, 999.99, 3123, 1, 2, 3),
(9, 12.00, 12, 1, 1, 3),
(10, 999.99, 23323, 1, 1, 3);

--
-- Triggers `cohort_survival`
--
DELIMITER $$
CREATE TRIGGER `after_insert_cohort_survival` AFTER INSERT ON `cohort_survival` FOR EACH ROW BEGIN
    DECLARE totalFigure INT;
    DECLARE totalRate DECIMAL(5, 2);

    -- Calculate totals for the same school year
    SELECT 
        SUM(Cohort_Figure), SUM(Cohort_Rate)
    INTO 
        totalFigure, totalRate
    FROM 
        Cohort_Survival
    WHERE 
        School_Year_ID = NEW.School_Year_ID;

    -- Check if Total_Cohort entry exists for this School_Year_ID
    IF EXISTS (
        SELECT 1 
        FROM Total_Cohort 
        WHERE Cohort_ID IN (
            SELECT Cohort_ID 
            FROM Cohort_Survival 
            WHERE School_Year_ID = NEW.School_Year_ID
        )
    ) THEN
        -- Update totals for the same School_Year_ID
        UPDATE Total_Cohort
        SET Total_Cohort_Figure = totalFigure, Total_Cohort_Rate = totalRate
        WHERE Cohort_ID IN (
            SELECT Cohort_ID 
            FROM Cohort_Survival 
            WHERE School_Year_ID = NEW.School_Year_ID
        );
    ELSE
        -- Insert new totals for the same School_Year_ID
        INSERT INTO Total_Cohort (Total_Cohort_Figure, Total_Cohort_Rate, Cohort_ID)
        VALUES (totalFigure, totalRate, NEW.Cohort_ID);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `CommentID` int(11) NOT NULL,
  `ContentID` int(11) NOT NULL,
  `TaskID` int(11) NOT NULL,
  `IncomingID` int(11) NOT NULL,
  `OutgoingID` int(11) NOT NULL,
  `Comment` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_ID` int(11) NOT NULL,
  `dept_name` varchar(255) NOT NULL,
  `dept_info` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dept_ID`, `dept_name`, `dept_info`) VALUES
(1, 'Science ', 'Responsible for teaching all science-related subjects, including Biology, Chemistry, and Physics.'),
(2, 'English Department', 'Handles English language and literature courses, including reading, writing, and speaking skills.'),
(5, 'MAPEH Department', 'Covers Music, Arts, Physical Education, and Health education, promoting holistic development in students.'),
(6, 'Filipino Department', 'Teaches the Filipino language, literature, and culture.'),
(7, 'History Department', 'Responsible for teaching history subjects, including Philippine History and World History.'),
(8, 'Technology and Livelihood Education Department', 'Offers practical subjects related to technology, home economics, and entrepreneurship.'),
(9, 'Accountancy, Business, and Management (ABM)', 'Focuses on business-related subjects, preparing students for careers in finance and management.'),
(10, 'Science, Technology, Engineering, and Mathematics (STEM)', 'Emphasizes advanced science and mathematics subjects for students pursuing engineering and technology.'),
(11, 'Humanities and Social Sciences (HUMSS)', 'Covers subjects in social sciences, literature, and communication for those interested in the arts and humanities.'),
(12, 'General Academic Strand (GAS)', 'A flexible curriculum for students exploring various academic subjects.'),
(13, 'Technical-Vocational-Livelihood (TVL)', 'Offers practical skills training in various vocational areas like agriculture, hospitality, and ICT.'),
(14, 'Sports Track', 'Prepares students for careers in sports management, coaching, and physical education.'),
(15, 'Arts and Design Track', 'Concentrates on creative arts and design, suitable for students aiming for careers in the arts.');

-- --------------------------------------------------------

--
-- Table structure for table `departmentfolders`
--

CREATE TABLE `departmentfolders` (
  `DepartmentFolderID` int(11) NOT NULL,
  `dept_ID` int(11) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `CreationTimestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departmentfolders`
--

INSERT INTO `departmentfolders` (`DepartmentFolderID`, `dept_ID`, `Name`, `CreationTimestamp`) VALUES
(1, 1, 'Science ', '2024-10-30 08:10:07'),
(2, 2, 'English Department', '2024-10-30 08:10:07'),
(3, 5, 'MAPEH Department', '2024-10-30 08:10:07'),
(4, 6, 'Filipino Department', '2024-10-30 08:10:07'),
(5, 7, 'History Department', '2024-10-30 08:10:07'),
(6, 8, 'Technology and Livelihood Education Department', '2024-10-30 08:10:07'),
(7, 9, 'Accountancy, Business, and Management (ABM)', '2024-10-30 08:10:07'),
(8, 10, 'Science, Technology, Engineering, and Mathematics (STEM)', '2024-10-30 08:10:07'),
(9, 11, 'Humanities and Social Sciences (HUMSS)', '2024-10-30 08:10:07'),
(10, 12, 'General Academic Strand (GAS)', '2024-10-30 08:10:07'),
(11, 13, 'Technical-Vocational-Livelihood (TVL)', '2024-10-30 08:10:07'),
(12, 14, 'Sports Track', '2024-10-30 08:10:07'),
(13, 15, 'Arts and Design Track', '2024-10-30 08:10:07');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `DocuID` int(11) NOT NULL,
  `GradeLevelFolderID` int(11) DEFAULT NULL,
  `UserFolderID` int(11) DEFAULT NULL,
  `UserID` int(11) NOT NULL,
  `ContentID` int(11) NOT NULL,
  `TaskID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mimeType` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `Status` int(11) NOT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`DocuID`, `GradeLevelFolderID`, `UserFolderID`, `UserID`, `ContentID`, `TaskID`, `name`, `mimeType`, `size`, `uri`, `Status`, `TimeStamp`) VALUES
(81, 1, 74, 3, 168475, 878734, '741851_DLL template.pdf', 'application/pdf', 519463, 'Documents/741851_DLL template.pdf', 1, '2024-12-11 08:20:55'),
(82, 1, 74, 3, 168475, 878735, '026437_IT PROJECT CONTRACT.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 17809, 'Documents/026437_IT PROJECT CONTRACT.docx', 1, '2024-12-11 08:28:01'),
(84, 18, 75, 3, 168481, 878748, '112759_Account Ledger.pdf', 'application/pdf', 30672, 'Documents/112759_Account Ledger.pdf', 1, '2024-12-15 03:54:13');

-- --------------------------------------------------------

--
-- Table structure for table `dropout`
--

CREATE TABLE `dropout` (
  `Dropout_ID` int(11) NOT NULL,
  `Dropout_Rate` decimal(5,2) DEFAULT NULL,
  `Dropout_Figure` int(11) DEFAULT NULL,
  `School_Year_ID` int(11) NOT NULL,
  `Grade_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dropout`
--

INSERT INTO `dropout` (`Dropout_ID`, `Dropout_Rate`, `Dropout_Figure`, `School_Year_ID`, `Grade_ID`, `UserID`) VALUES
(8, 999.99, 13131, 1, 2, 3),
(9, 100.00, 12, 1, 1, 3),
(10, 999.99, 33233, 1, 1, 3);

--
-- Triggers `dropout`
--
DELIMITER $$
CREATE TRIGGER `after_insert_dropout` AFTER INSERT ON `dropout` FOR EACH ROW BEGIN
    DECLARE totalFigure INT;
    DECLARE totalRate DECIMAL(5, 2);

    SELECT 
        SUM(Dropout_Figure), SUM(Dropout_Rate)
    INTO 
        totalFigure, totalRate
    FROM 
        Dropout
    WHERE 
        School_Year_ID = NEW.School_Year_ID;

    IF EXISTS (
        SELECT 1 
        FROM Total_Dropout 
        WHERE Dropout_ID IN (
            SELECT Dropout_ID 
            FROM Dropout 
            WHERE School_Year_ID = NEW.School_Year_ID
        )
    ) THEN
        UPDATE Total_Dropout
        SET Total_Dropout_Figure = totalFigure, Total_Dropout_Rate = totalRate
        WHERE Dropout_ID IN (
            SELECT Dropout_ID 
            FROM Dropout 
            WHERE School_Year_ID = NEW.School_Year_ID
        );
    ELSE
        INSERT INTO Total_Dropout (Total_Dropout_Figure, Total_Dropout_Rate, Dropout_ID)
        VALUES (totalFigure, totalRate, NEW.Dropout_ID);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `enroll`
--

CREATE TABLE `enroll` (
  `Enroll_ID` int(11) NOT NULL,
  `Enroll_Gross` int(11) DEFAULT NULL,
  `Enroll_Net` int(11) DEFAULT NULL,
  `School_Year_ID` int(11) NOT NULL,
  `Grade_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enroll`
--

INSERT INTO `enroll` (`Enroll_ID`, `Enroll_Gross`, `Enroll_Net`, `School_Year_ID`, `Grade_ID`, `UserID`) VALUES
(8, 12, 213, 1, 2, 3),
(9, 12, 12, 1, 1, 3),
(10, 2332, 32323, 1, 1, 3);

--
-- Triggers `enroll`
--
DELIMITER $$
CREATE TRIGGER `after_insert_enroll` AFTER INSERT ON `enroll` FOR EACH ROW BEGIN
    DECLARE totalGross INT;
    DECLARE totalNet INT;

    SELECT 
        SUM(Enroll_Gross), SUM(Enroll_Net)
    INTO 
        totalGross, totalNet
    FROM 
        Enroll
    WHERE 
        School_Year_ID = NEW.School_Year_ID;

    IF EXISTS (
        SELECT 1 
        FROM Total_Enroll 
        WHERE Enroll_ID IN (
            SELECT Enroll_ID 
            FROM Enroll 
            WHERE School_Year_ID = NEW.School_Year_ID
        )
    ) THEN
        UPDATE Total_Enroll
        SET Total_Enroll_Gross = totalGross, Total_Enroll_Net = totalNet
        WHERE Enroll_ID IN (
            SELECT Enroll_ID 
            FROM Enroll 
            WHERE School_Year_ID = NEW.School_Year_ID
        );
    ELSE
        INSERT INTO Total_Enroll (Total_Enroll_Gross, Total_Enroll_Net, Enroll_ID)
        VALUES (totalGross, totalNet, NEW.Enroll_ID);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `feedcontent`
--

CREATE TABLE `feedcontent` (
  `ContentID` int(11) NOT NULL,
  `dept_ID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Captions` text DEFAULT NULL,
  `ContentCode` varchar(255) DEFAULT NULL,
  `ContentColor` varchar(255) NOT NULL DEFAULT '#9b2035'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedcontent`
--

INSERT INTO `feedcontent` (`ContentID`, `dept_ID`, `Title`, `Captions`, `ContentCode`, `ContentColor`) VALUES
(168475, 1, 'Grade 7', 'Rose', 'lcNhLF', '#4285F4'),
(168476, 1, 'Grade 7', 'Daisy', 'rT3k2m', '#4285F4'),
(168477, 1, 'Grade 7', 'Lily', 'sF5g8h', '#4285F4'),
(168478, 1, 'Grade 7', 'Tulip', 'tN9j0p', '#4285F4'),
(168480, 2, 'Grade 7', 'Rose', 'A1B2C3', '#4285F4'),
(168481, 2, 'Grade 7', 'Daisy', 'D4E5F6', '#4285F4'),
(168482, 2, 'Grade 7', 'Lily', 'H7I8J9', '#4285F4'),
(168483, 2, 'Grade 7', 'Tulip', 'K0L1M2', '#4285F4'),
(168484, 2, 'Grade 8', 'Onyx', 'X9Y8Z7', '#9b2035'),
(168485, 2, 'Grade 8', 'Topaz', 'F3G4H5', '#9b2035'),
(168486, 2, 'Grade 8', 'Diamond', 'M6N7O8', '#9b2035'),
(168487, 2, 'Grade 8', 'Emerald', 'Q1R2S3', '#9b2035'),
(168488, 2, 'Grade 9', 'Jupiter', 'D4E5Fu', '#9b2035'),
(168489, 2, 'Grade 9', 'Mars', 'A7B8C9', '#9b2035'),
(168490, 2, 'Grade 9', 'Mercury', 'G1H2I3', '#9b2035'),
(168491, 2, 'Grade 9', 'Venus', 'K5L6M7', '#9b2035'),
(168492, 2, 'Grade 10', 'Shakespeare', 'R1S2T3', '#9b2035'),
(168493, 2, 'Grade 10', 'Austen', 'T4U5V6', '#9b2035'),
(168494, 2, 'Grade 10', 'Picasso', 'V7W8X9', '#9b2035'),
(168495, 2, 'Grade 10', 'Monet', 'Y0Z1A2', '#9b2035'),
(168512, 5, 'Grade 7', 'Rose', 'L9M8N7', '#0000FF'),
(168513, 5, 'Grade 7', 'Daisy', 'R4S3T2', '#9b2035'),
(168514, 5, 'Grade 7', 'Lily', 'U1V0W9', '#9b2035'),
(168515, 5, 'Grade 7', 'Tulip', 'X8Y7Z6', '#9b2035'),
(168516, 5, 'Grade 8', 'Onyx', 'A3B4C5', '#9b2035'),
(168517, 5, 'Grade 8', 'Topaz', 'D6E7F8', '#9b2035'),
(168518, 5, 'Grade 8', 'Diamond', 'G0H1I2', '#9b2035'),
(168519, 5, 'Grade 8', 'Emerald', 'J3K4L5', '#9b2035'),
(168520, 5, 'Grade 9', 'Jupiter', 'M6N7O8', '#9b2035'),
(168521, 5, 'Grade 9', 'Mars', 'P1Q2R3', '#9b2035'),
(168522, 5, 'Grade 9', 'Mercury', 'S4T5U6', '#9b2035'),
(168523, 5, 'Grade 9', 'Venus', 'V7W8X9', '#9b2035'),
(168524, 5, 'Grade 10', 'Shakespeare', 'Y0Z1A2', '#9b2035'),
(168525, 5, 'Grade 10', 'Austen', 'B4C5D6', '#9b2035'),
(168526, 5, 'Grade 10', 'Picasso', 'E8F9G0', '#9b2035'),
(168527, 5, 'Grade 10', 'Monet', 'H1I2J3', '#9b2035'),
(168528, 6, 'Grade 7', 'Rose', 'K5L6M7', '#0000FF'),
(168529, 6, 'Grade 7', 'Daisy', 'N8O9P0', '#9b2035'),
(168530, 6, 'Grade 7', 'Lily', 'Q1R2S3', '#9b2035'),
(168531, 6, 'Grade 7', 'Tulip', 'T4U5V6', '#9b2035'),
(168532, 6, 'Grade 8', 'Onyx', 'W9X8Y7', '#9b2035'),
(168533, 6, 'Grade 8', 'Topaz', 'Z0A1B2', '#9b2035'),
(168534, 6, 'Grade 8', 'Diamond', 'C3D4E5', '#9b2035'),
(168535, 6, 'Grade 8', 'Emerald', 'F6G7H8', '#9b2035'),
(168536, 6, 'Grade 9', 'Jupiter', 'I9J0K1', '#9b2035'),
(168537, 6, 'Grade 9', 'Mars', 'L2M3N4', '#9b2035'),
(168538, 6, 'Grade 9', 'Mercury', 'O5P6Q7', '#9b2035'),
(168539, 6, 'Grade 9', 'Venus', 'R8S9T0', '#9b2035'),
(168540, 6, 'Grade 10', 'Shakespeare', 'U1V2W3', '#9b2035'),
(168541, 6, 'Grade 10', 'Austen', 'X4Y5Z6', '#9b2035'),
(168542, 6, 'Grade 10', 'Picasso', 'A7B8C9', '#9b2035'),
(168543, 6, 'Grade 10', 'Monet', 'D0E1F2', '#9b2035'),
(168544, 7, 'Grade 7', 'Rose', 'K3L4M5', '#0000FF'),
(168545, 7, 'Grade 7', 'Daisy', 'N6O7P8', '#9b2035'),
(168546, 7, 'Grade 7', 'Lily', 'Q1R2S3', '#9b2035'),
(168547, 7, 'Grade 7', 'Tulip', 'T4U5V6', '#9b2035'),
(168548, 7, 'Grade 8', 'Onyx', 'W9X8Y7', '#9b2035'),
(168549, 7, 'Grade 8', 'Topaz', 'Z0A1B2', '#9b2035'),
(168550, 7, 'Grade 8', 'Diamond', 'C3D4E5', '#9b2035'),
(168551, 7, 'Grade 8', 'Emerald', 'F6G7H8', '#9b2035'),
(168552, 7, 'Grade 9', 'Jupiter', 'I9J0K1', '#9b2035'),
(168553, 7, 'Grade 9', 'Mars', 'L2M3N4', '#9b2035'),
(168554, 7, 'Grade 9', 'Mercury', 'O5P6Q7', '#9b2035'),
(168555, 7, 'Grade 9', 'Venus', 'R8S9T0', '#9b2035'),
(168556, 7, 'Grade 10', 'Shakespeare', 'U1V2W3', '#9b2035'),
(168557, 7, 'Grade 10', 'Austen', 'X4Y5Z6', '#9b2035'),
(168560, 8, 'Grade 7', 'Rose', 'K3L4M5', '#0000FF'),
(168561, 8, 'Grade 7', 'Daisy', 'N6O7P8', '#9b2035'),
(168562, 8, 'Grade 7', 'Lily', 'Q1R2S3', '#9b2035'),
(168563, 8, 'Grade 7', 'Tulip', 'T4U5V6', '#9b2035'),
(168564, 8, 'Grade 8', 'Onyx', 'W9X8Y7', '#9b2035'),
(168565, 8, 'Grade 8', 'Topaz', 'Z0A1B2', '#9b2035'),
(168566, 8, 'Grade 8', 'Diamond', 'C3D4E5', '#9b2035'),
(168567, 8, 'Grade 8', 'Emerald', 'F6G7H8', '#9b2035'),
(168568, 8, 'Grade 9', 'Jupiter', 'I9J0K1', '#9b2035'),
(168569, 8, 'Grade 9', 'Mars', 'L2M3N4', '#9b2035'),
(168570, 8, 'Grade 9', 'Mercury', 'O5P6Q7', '#9b2035'),
(168571, 8, 'Grade 9', 'Venus', 'R8S9T0', '#9b2035'),
(168572, 8, 'Grade 10', 'Shakespeare', 'U1V2W3', '#9b2035'),
(168573, 8, 'Grade 10', 'Austen', 'X4Y5Z6', '#9b2035'),
(168574, 8, 'Grade 10', 'Picasso', 'A7B8C9', '#9b2035'),
(168575, 8, 'Grade 10', 'Monet', 'D0E1F2', '#9b2035'),
(168700, 9, 'Grade 11', 'Section A', 'X1Y2Z3', '#9b2035'),
(168701, 9, 'Grade 11', 'Section B', 'A4B5C6', '#9b2035'),
(168702, 9, 'Grade 12', 'Section A', 'D7E8F9', '#9b2035'),
(168703, 9, 'Grade 12', 'Section B', 'G0H1I2', '#9b2035'),
(168704, 10, 'Grade 11', 'Section A', 'J3K4L5', '#9b2035'),
(168705, 10, 'Grade 11', 'Section B', 'M6N7O8', '#9b2035'),
(168706, 10, 'Grade 12', 'Section A', 'P9Q0R1', '#9b2035'),
(168707, 10, 'Grade 12', 'Section B', 'S2T3U4', '#9b2035'),
(168708, 11, 'Grade 11', 'Section A', 'V5W6X7', '#9b2035'),
(168709, 11, 'Grade 11', 'Section B', 'Y8Z9A0', '#9b2035'),
(168710, 11, 'Grade 12', 'Section A', 'B1C2D3', '#9b2035'),
(168711, 11, 'Grade 12', 'Section B', 'E4F5G6', '#9b2035'),
(168712, 12, 'Grade 11', 'Section A', 'H7I8J9', '#9b2035'),
(168713, 12, 'Grade 11', 'Section B', 'K0L1M2', '#9b2035'),
(168714, 12, 'Grade 12', 'Section A', 'N3O4P5', '#9b2035'),
(168715, 12, 'Grade 12', 'Section B', 'Q6R7S8', '#9b2035'),
(168716, 13, 'Grade 11', 'Section A', 'T9U0V1', '#9b2035'),
(168717, 13, 'Grade 11', 'Section B', 'W2X3Y4', '#9b2035'),
(168718, 13, 'Grade 12', 'Section A', 'Z5A6B7', '#9b2035'),
(168719, 13, 'Grade 12', 'Section B', 'C8D9E0', '#9b2035'),
(168720, 14, 'Grade 11', 'Section A', 'F1G2H3', '#9b2035'),
(168721, 14, 'Grade 11', 'Section B', 'I4J5K6', '#9b2035'),
(168722, 14, 'Grade 12', 'Section A', 'L7M8N9', '#9b2035'),
(168723, 14, 'Grade 12', 'Section B', 'O0P1Q2', '#9b2035'),
(168724, 15, 'Grade 11', 'Section A', 'R3S4T5', '#9b2035'),
(168725, 15, 'Grade 11', 'Section B', 'U6V7W8', '#9b2035'),
(168726, 15, 'Grade 12', 'Section A', 'X9Y0Z1', '#9b2035'),
(168727, 15, 'Grade 12', 'Section B', 'A2B3C4', '#9b2035'),
(528118, 1, 'Grade 8', 'Onyx', '4RPZdD', '#9b2035'),
(528119, 1, 'Grade 8', 'Topaz', '8M7xB4', '#9b2035'),
(528120, 1, 'Grade 8', 'Diamond', 'Q1W2E3', '#9b2035'),
(528121, 1, 'Grade 8', 'Emerald', 'Y6U8I0', '#9b2035'),
(528122, 1, 'Grade 9', 'Jupiter', 'F3G5H8', '#9b2035'),
(528123, 1, 'Grade 9', 'Mars', 'T9R2S4', '#9b2035'),
(528124, 1, 'Grade 9', 'Mercury', 'A7B8C9', '#9b2035'),
(528125, 1, 'Grade 9', 'Venus', 'D4E5F6', '#9b2035'),
(528126, 1, 'Grade 10', 'Shakespeare', 'M1N2O3', '#9b2035'),
(528127, 1, 'Grade 10', 'Austen', 'R6T7Y8', '#9b2035'),
(528128, 1, 'Grade 10', 'Picasso', 'H5J6K7', '#9b2035'),
(528129, 1, 'Grade 10', 'Monet', 'S3Q2W1', '#9b2035'),
(528140, 7, 'Grade 8', 'color', 'ewNGzA', '#FBBC04');

-- --------------------------------------------------------

--
-- Table structure for table `grade`
--

CREATE TABLE `grade` (
  `Grade_ID` int(11) NOT NULL,
  `Grade_Level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade`
--

INSERT INTO `grade` (`Grade_ID`, `Grade_Level`) VALUES
(1, 7),
(2, 8),
(3, 9),
(4, 10);

-- --------------------------------------------------------

--
-- Table structure for table `gradelevelfolders`
--

CREATE TABLE `gradelevelfolders` (
  `GradeLevelFolderID` int(11) NOT NULL,
  `DepartmentFolderID` int(11) DEFAULT NULL,
  `ContentID` int(11) DEFAULT NULL,
  `CreationTimestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gradelevelfolders`
--

INSERT INTO `gradelevelfolders` (`GradeLevelFolderID`, `DepartmentFolderID`, `ContentID`, `CreationTimestamp`) VALUES
(1, 1, 168475, '2024-10-30 01:03:48'),
(2, 1, 168476, '2024-10-30 01:03:48'),
(3, 1, 168477, '2024-10-30 01:03:48'),
(4, 1, 168478, '2024-10-30 01:03:48'),
(5, 1, 528118, '2024-10-30 01:03:48'),
(6, 1, 528119, '2024-10-30 01:03:48'),
(7, 1, 528120, '2024-10-30 01:03:48'),
(8, 1, 528121, '2024-10-30 01:03:48'),
(9, 1, 528122, '2024-10-30 01:03:48'),
(10, 1, 528123, '2024-10-30 01:03:48'),
(11, 1, 528124, '2024-10-30 01:03:48'),
(12, 1, 528125, '2024-10-30 01:03:48'),
(13, 1, 528126, '2024-10-30 01:03:48'),
(14, 1, 528127, '2024-10-30 01:03:48'),
(15, 1, 528128, '2024-10-30 01:03:48'),
(16, 1, 528129, '2024-10-30 01:03:48'),
(17, 2, 168480, '2024-10-30 01:03:48'),
(18, 2, 168481, '2024-10-30 01:03:48'),
(19, 2, 168482, '2024-10-30 01:03:48'),
(20, 2, 168483, '2024-10-30 01:03:48'),
(21, 2, 168484, '2024-10-30 01:03:48'),
(22, 2, 168485, '2024-10-30 01:03:48'),
(23, 2, 168486, '2024-10-30 01:03:48'),
(24, 2, 168487, '2024-10-30 01:03:48'),
(25, 2, 168488, '2024-10-30 01:03:48'),
(26, 2, 168489, '2024-10-30 01:03:48'),
(27, 2, 168490, '2024-10-30 01:03:48'),
(28, 2, 168491, '2024-10-30 01:03:48'),
(29, 2, 168492, '2024-10-30 01:03:48'),
(30, 2, 168493, '2024-10-30 01:03:48'),
(31, 2, 168494, '2024-10-30 01:03:48'),
(32, 2, 168495, '2024-10-30 01:03:48'),
(33, 3, 168512, '2024-10-30 01:03:48'),
(34, 3, 168513, '2024-10-30 01:03:48'),
(35, 3, 168514, '2024-10-30 01:03:48'),
(36, 3, 168515, '2024-10-30 01:03:48'),
(37, 3, 168516, '2024-10-30 01:03:48'),
(38, 3, 168517, '2024-10-30 01:03:48'),
(39, 3, 168518, '2024-10-30 01:03:48'),
(40, 3, 168519, '2024-10-30 01:03:48'),
(41, 3, 168520, '2024-10-30 01:03:48'),
(42, 3, 168521, '2024-10-30 01:03:48'),
(43, 3, 168522, '2024-10-30 01:03:48'),
(44, 3, 168523, '2024-10-30 01:03:48'),
(45, 3, 168524, '2024-10-30 01:03:48'),
(46, 3, 168525, '2024-10-30 01:03:48'),
(47, 3, 168526, '2024-10-30 01:03:48'),
(48, 3, 168527, '2024-10-30 01:03:48'),
(49, 4, 168528, '2024-10-30 01:03:48'),
(50, 4, 168529, '2024-10-30 01:03:48'),
(51, 4, 168530, '2024-10-30 01:03:48'),
(52, 4, 168531, '2024-10-30 01:03:48'),
(53, 4, 168532, '2024-10-30 01:03:48'),
(54, 4, 168533, '2024-10-30 01:03:48'),
(55, 4, 168534, '2024-10-30 01:03:48'),
(56, 4, 168535, '2024-10-30 01:03:48'),
(57, 4, 168536, '2024-10-30 01:03:48'),
(58, 4, 168537, '2024-10-30 01:03:48'),
(59, 4, 168538, '2024-10-30 01:03:48'),
(60, 4, 168539, '2024-10-30 01:03:48'),
(61, 4, 168540, '2024-10-30 01:03:48'),
(62, 4, 168541, '2024-10-30 01:03:48'),
(63, 4, 168542, '2024-10-30 01:03:48'),
(64, 4, 168543, '2024-10-30 01:03:48'),
(65, 5, 168544, '2024-10-30 01:03:48'),
(66, 5, 168545, '2024-10-30 01:03:48'),
(67, 5, 168546, '2024-10-30 01:03:48'),
(68, 5, 168547, '2024-10-30 01:03:48'),
(69, 5, 168548, '2024-10-30 01:03:48'),
(70, 5, 168549, '2024-10-30 01:03:48'),
(71, 5, 168550, '2024-10-30 01:03:48'),
(72, 5, 168551, '2024-10-30 01:03:48'),
(73, 5, 168552, '2024-10-30 01:03:48'),
(74, 5, 168553, '2024-10-30 01:03:48'),
(75, 5, 168554, '2024-10-30 01:03:48'),
(76, 5, 168555, '2024-10-30 01:03:48'),
(77, 5, 168556, '2024-10-30 01:03:48'),
(78, 5, 168557, '2024-10-30 01:03:48'),
(81, 6, 168560, '2024-10-30 01:03:48'),
(82, 6, 168561, '2024-10-30 01:03:48'),
(83, 6, 168562, '2024-10-30 01:03:48'),
(84, 6, 168563, '2024-10-30 01:03:48'),
(85, 6, 168564, '2024-10-30 01:03:48'),
(86, 6, 168565, '2024-10-30 01:03:48'),
(87, 6, 168566, '2024-10-30 01:03:48'),
(88, 6, 168567, '2024-10-30 01:03:48'),
(89, 6, 168568, '2024-10-30 01:03:48'),
(90, 6, 168569, '2024-10-30 01:03:48'),
(91, 6, 168570, '2024-10-30 01:03:48'),
(92, 6, 168571, '2024-10-30 01:03:48'),
(93, 6, 168572, '2024-10-30 01:03:48'),
(94, 6, 168573, '2024-10-30 01:03:48'),
(95, 6, 168574, '2024-10-30 01:03:48'),
(96, 6, 168575, '2024-10-30 01:03:48'),
(97, 7, 168700, '2024-10-30 01:03:48'),
(98, 7, 168701, '2024-10-30 01:03:48'),
(99, 7, 168702, '2024-10-30 01:03:48'),
(100, 7, 168703, '2024-10-30 01:03:48'),
(101, 8, 168704, '2024-10-30 01:03:48'),
(102, 8, 168705, '2024-10-30 01:03:48'),
(103, 8, 168706, '2024-10-30 01:03:48'),
(104, 8, 168707, '2024-10-30 01:03:48'),
(105, 9, 168708, '2024-10-30 01:03:48'),
(106, 9, 168709, '2024-10-30 01:03:48'),
(107, 9, 168710, '2024-10-30 01:03:48'),
(108, 9, 168711, '2024-10-30 01:03:48'),
(109, 10, 168712, '2024-10-30 01:03:48'),
(110, 10, 168713, '2024-10-30 01:03:48'),
(111, 10, 168714, '2024-10-30 01:03:48'),
(112, 10, 168715, '2024-10-30 01:03:48'),
(113, 11, 168716, '2024-10-30 01:03:48'),
(114, 11, 168717, '2024-10-30 01:03:48'),
(115, 11, 168718, '2024-10-30 01:03:48'),
(116, 11, 168719, '2024-10-30 01:03:48'),
(117, 12, 168720, '2024-10-30 01:03:48'),
(118, 12, 168721, '2024-10-30 01:03:48'),
(119, 12, 168722, '2024-10-30 01:03:48'),
(120, 12, 168723, '2024-10-30 01:03:48'),
(121, 13, 168724, '2024-10-30 01:03:48'),
(122, 13, 168725, '2024-10-30 01:03:48'),
(123, 13, 168726, '2024-10-30 01:03:48'),
(124, 13, 168727, '2024-10-30 01:03:48'),
(135, 5, 528140, '2024-11-23 16:24:09');

-- --------------------------------------------------------

--
-- Table structure for table `mps`
--

CREATE TABLE `mps` (
  `mpsID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ContentID` int(11) NOT NULL,
  `Quarter_ID` int(11) NOT NULL,
  `TotalNumOfItems` int(11) NOT NULL,
  `TotalNumOfStudents` int(11) NOT NULL,
  `TotalNumTested` int(11) NOT NULL,
  `HighestScore` int(11) NOT NULL,
  `LowestScore` int(11) NOT NULL,
  `TotalScores` int(11) NOT NULL,
  `MPS` float NOT NULL,
  `MPSBelow75` enum('Yes','No') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mps`
--

INSERT INTO `mps` (`mpsID`, `UserID`, `ContentID`, `Quarter_ID`, `TotalNumOfItems`, `TotalNumOfStudents`, `TotalNumTested`, `HighestScore`, `LowestScore`, `TotalScores`, `MPS`, `MPSBelow75`) VALUES
(46, 3, 168475, 4, 2313, 232332, 3232, 232323, 2323, 32323, 0.43, 'Yes'),
(47, 3, 168481, 4, 3123, 3123, 1233, 123123, 123, 123133, 3.2, 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `nat`
--

CREATE TABLE `nat` (
  `NAT_ID` int(11) NOT NULL,
  `NAT_Score` int(11) DEFAULT NULL,
  `School_Year_ID` int(11) NOT NULL,
  `Grade_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nat`
--

INSERT INTO `nat` (`NAT_ID`, `NAT_Score`, `School_Year_ID`, `Grade_ID`, `UserID`) VALUES
(2, 31313, 1, 2, 3),
(3, 1211, 1, 1, 3),
(4, 2323, 1, 1, 3);

--
-- Triggers `nat`
--
DELIMITER $$
CREATE TRIGGER `after_insert_nat` AFTER INSERT ON `nat` FOR EACH ROW BEGIN
    DECLARE totalNAT INT;

    SELECT 
        SUM(NAT_Score)
    INTO 
        totalNAT
    FROM 
        NAT
    WHERE 
        School_Year_ID = NEW.School_Year_ID;

    IF EXISTS (
        SELECT 1 
        FROM Total_NAT 
        WHERE NAT_ID IN (
            SELECT NAT_ID 
            FROM NAT 
            WHERE School_Year_ID = NEW.School_Year_ID
        )
    ) THEN
        UPDATE Total_NAT
        SET Total_NAT = totalNAT
        WHERE NAT_ID IN (
            SELECT NAT_ID 
            FROM NAT 
            WHERE School_Year_ID = NEW.School_Year_ID
        );
    ELSE
        INSERT INTO Total_NAT (Total_NAT, NAT_ID)
        VALUES (totalNAT, NEW.NAT_ID);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `NotifID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ContentID` int(11) NOT NULL,
  `TaskID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Content` text DEFAULT NULL,
  `Status` int(11) NOT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`NotifID`, `UserID`, `ContentID`, `TaskID`, `Title`, `Content`, `Status`, `TimeStamp`) VALUES
(2200, 1, 168475, 878734, 'Jorge Bautista posted a new Task! (Grade 7 - Rose)', 'DLL Submission: Week 1: <p>Please submit your DLL\'s for week 1. Refer to the Attachments below. Thanks!</p>', 1, '2024-12-11 08:01:42'),
(2201, 1, 168475, 878735, 'Jorge Bautista posted a new Task! (Grade 7 - Rose)', 'DLL Submission: Week 2: <p>Submit DLL for week 2. Thanks!</p>', 1, '2024-12-11 08:26:35'),
(2202, 1, 168475, 878736, 'Jorge Bautista posted a new Task! (Grade 7 - Rose)', 'DLL Submission: Week 3: <p>Please submit DLL\'s for week 3.</p>', 1, '2024-12-11 08:29:35'),
(2204, 1, 168475, 878738, 'Jorge Bautista Posted a new Announcement! (Grade 7 - Rose)', 'Christmas Party 2k25: <p>ATTENTION! We will be having a Christmas party at 12/19/2024 @ exactly 1:00 pm onwards. Thanks</p>', 1, '2024-12-11 10:47:50'),
(2205, 1, 168475, 878739, 'Jorge Bautista Posted a new Announcement! (Grade 7 - Rose)', 'December Events: <p>Please refer below for out December calendar. Thankyou!</p>', 1, '2024-12-11 11:00:28'),
(2208, 1, 168475, 878742, 'Jorge Bautista posted a new Announcement! (Grade 7 - Rose)', 'CHANGES IN SCHOOL CALENDAR: <p>See attachments for your reference</p>', 1, '2024-12-11 12:47:52'),
(2212, 108, 168481, 878746, 'Richard Posted a new Task! (Grade 7)', 'fdfsd: <p>fsdfsf</p>', 1, '2024-12-14 09:24:46'),
(2214, 108, 168481, 878749, 'Richard Posted a new Task! (Grade 7)', 'dadadad: <p>dadsadad</p>', 1, '2024-12-14 15:29:47'),
(2215, 108, 168481, 878748, 'Richard Posted a new Task! (Grade 7)', 'dadaad: <p>adadadad</p>', 1, '2024-12-14 15:29:48');

-- --------------------------------------------------------

--
-- Table structure for table `notif_user`
--

CREATE TABLE `notif_user` (
  `Notif_User_ID` int(11) NOT NULL,
  `NotifID` int(11) DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Status` int(11) DEFAULT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notif_user`
--

INSERT INTO `notif_user` (`Notif_User_ID`, `NotifID`, `UserID`, `Status`, `TimeStamp`) VALUES
(184, 2200, 3, 0, '2024-12-11 01:01:42'),
(185, 2201, 3, 1, '2024-12-11 01:26:35'),
(186, 2202, 3, 1, '2024-12-11 01:29:35'),
(188, 2204, 3, 1, '2024-12-11 03:47:50'),
(189, 2205, 3, 1, '2024-12-11 04:00:28'),
(192, 2208, 3, 0, '2024-12-11 05:47:52'),
(196, 2212, 3, 1, '2024-12-14 02:24:46'),
(197, 2214, 3, 1, '2024-12-14 08:29:47'),
(198, 2215, 3, 0, '2024-12-14 08:29:48');

-- --------------------------------------------------------

--
-- Table structure for table `otp`
--

CREATE TABLE `otp` (
  `otp_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp`
--

INSERT INTO `otp` (`otp_ID`, `UserID`, `otp`, `created_at`) VALUES
(14, 3, '510352', '2024-12-14 04:48:07'),
(16, 1, '404537', '2024-12-14 19:29:01'),
(17, 3, '376369', '2024-12-14 20:56:05');

-- --------------------------------------------------------

--
-- Table structure for table `performance_indicator`
--

CREATE TABLE `performance_indicator` (
  `Performance_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `School_Year_ID` int(11) NOT NULL,
  `Grade_ID` int(11) NOT NULL,
  `Enroll_ID` int(11) NOT NULL,
  `Dropout_ID` int(11) NOT NULL,
  `Promotion_ID` int(11) NOT NULL,
  `Cohort_ID` int(11) NOT NULL,
  `Repetition_ID` int(11) NOT NULL,
  `Age_ID` int(11) NOT NULL,
  `NAT_ID` int(11) NOT NULL,
  `Transition_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance_indicator`
--

INSERT INTO `performance_indicator` (`Performance_ID`, `UserID`, `School_Year_ID`, `Grade_ID`, `Enroll_ID`, `Dropout_ID`, `Promotion_ID`, `Cohort_ID`, `Repetition_ID`, `Age_ID`, `NAT_ID`, `Transition_ID`) VALUES
(3, 3, 1, 1, 9, 9, 3, 9, 3, 3, 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `promotion`
--

CREATE TABLE `promotion` (
  `Promotion_ID` int(11) NOT NULL,
  `Promotion_Rate` decimal(5,2) DEFAULT NULL,
  `Promotion_Figure` int(11) DEFAULT NULL,
  `School_Year_ID` int(11) NOT NULL,
  `Grade_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotion`
--

INSERT INTO `promotion` (`Promotion_ID`, `Promotion_Rate`, `Promotion_Figure`, `School_Year_ID`, `Grade_ID`, `UserID`) VALUES
(2, 999.99, 131313, 1, 2, 3),
(3, 999.99, 121, 1, 1, 3),
(4, 999.99, 32323, 1, 1, 3);

--
-- Triggers `promotion`
--
DELIMITER $$
CREATE TRIGGER `after_insert_promotion` AFTER INSERT ON `promotion` FOR EACH ROW BEGIN
    DECLARE totalFigure INT;
    DECLARE totalRate DECIMAL(5, 2);

    SELECT 
        SUM(Promotion_Figure), SUM(Promotion_Rate)
    INTO 
        totalFigure, totalRate
    FROM 
        Promotion
    WHERE 
        School_Year_ID = NEW.School_Year_ID;

    IF EXISTS (
        SELECT 1 
        FROM Total_Promotion 
        WHERE Promotion_ID IN (
            SELECT Promotion_ID 
            FROM Promotion 
            WHERE School_Year_ID = NEW.School_Year_ID
        )
    ) THEN
        UPDATE Total_Promotion
        SET Total_Promotion_Figure = totalFigure, Total_Promotion_Rate = totalRate
        WHERE Promotion_ID IN (
            SELECT Promotion_ID 
            FROM Promotion 
            WHERE School_Year_ID = NEW.School_Year_ID
        );
    ELSE
        INSERT INTO Total_Promotion (Total_Promotion_Figure, Total_Promotion_Rate, Promotion_ID)
        VALUES (totalFigure, totalRate, NEW.Promotion_ID);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `quarter`
--

CREATE TABLE `quarter` (
  `Quarter_ID` int(11) NOT NULL,
  `School_Year_ID` int(11) NOT NULL,
  `Quarter_Name` varchar(255) NOT NULL,
  `Start_Date` date NOT NULL,
  `End_Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quarter`
--

INSERT INTO `quarter` (`Quarter_ID`, `School_Year_ID`, `Quarter_Name`, `Start_Date`, `End_Date`) VALUES
(1, 1, 'First Quarter', '2024-10-02', '2024-10-28'),
(2, 1, 'Second Quarter', '2024-10-29', '2024-11-05'),
(3, 1, 'Third Quarter', '2024-11-06', '2024-11-14'),
(4, 1, 'Fourth Quarter', '2024-11-15', '2024-12-24');

-- --------------------------------------------------------

--
-- Table structure for table `repetition`
--

CREATE TABLE `repetition` (
  `Repetition_ID` int(11) NOT NULL,
  `Repeaters_Rate` decimal(5,2) DEFAULT NULL,
  `Repeaters_Figure` int(11) DEFAULT NULL,
  `School_Year_ID` int(11) NOT NULL,
  `Grade_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repetition`
--

INSERT INTO `repetition` (`Repetition_ID`, `Repeaters_Rate`, `Repeaters_Figure`, `School_Year_ID`, `Grade_ID`, `UserID`) VALUES
(2, 999.99, 3113, 1, 2, 3),
(3, 212.00, 121, 1, 1, 3),
(4, 999.99, 232323, 1, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `schoolyear`
--

CREATE TABLE `schoolyear` (
  `School_Year_ID` int(11) NOT NULL,
  `Year_Range` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schoolyear`
--

INSERT INTO `schoolyear` (`School_Year_ID`, `Year_Range`) VALUES
(1, '2024-2024'),
(2, '2017-2018'),
(3, '2018-2019'),
(4, '2019-2020'),
(5, '2024-2025');

-- --------------------------------------------------------

--
-- Table structure for table `school_details`
--

CREATE TABLE `school_details` (
  `school_details_ID` int(11) NOT NULL,
  `School_ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `City_Muni` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Region` varchar(255) NOT NULL,
  `Country` varchar(255) NOT NULL,
  `Organization` varchar(255) NOT NULL,
  `Logo` varchar(255) NOT NULL DEFAULT 'no_image.jpg',
  `Vision` text NOT NULL,
  `Mission` text NOT NULL,
  `Mobile_ID` int(11) NOT NULL,
  `Social_Media_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_details`
--

INSERT INTO `school_details` (`school_details_ID`, `School_ID`, `Name`, `Address`, `City_Muni`, `Email`, `Region`, `Country`, `Organization`, `Logo`, `Vision`, `Mission`, `Mobile_ID`, `Social_Media_ID`) VALUES
(1, 307706, 'Lian National High School', 'Brgy. Malaruhatan', 'Lian, Batangas', 'liannationalhighscool @yahoo.com', 'IV-A (CALABARZON)', 'Philippines', 'Department of Education (DepEd)', '66fe76c28287b.png', 'We dream of Filipinos\r\nwho passionately love their country\r\nand whose values and competencies\r\nenable them to realize their full potential\r\nand contribute meaningfully to building the nation.\r\n\r\nAs a learner-centered public institution,\r\nthe Department of Education\r\ncontinuously improves itself\r\nto better serve its stakeholders.', 'To protect and promote the right of every Filipino to quality, equitable, culture-based, and complete basic education where:\r\n\r\nStudents learn in a child-friendly, gender-sensitive, safe, and motivating environment.\r\nTeachers facilitate learning and constantly nurture every learner.\r\nAdministrators and staff, as stewards of the institution, ensure an enabling and supportive environment for effective learning to happen.\r\nFamily, community, and other stakeholders are actively engaged and share responsibility for developing life-long learners.', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `school_mobile`
--

CREATE TABLE `school_mobile` (
  `Mobile_ID` int(11) NOT NULL,
  `Mobile_No` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_mobile`
--

INSERT INTO `school_mobile` (`Mobile_ID`, `Mobile_No`) VALUES
(1, '09565127162');

-- --------------------------------------------------------

--
-- Table structure for table `school_photos`
--

CREATE TABLE `school_photos` (
  `photo_id` int(11) NOT NULL,
  `photo_name` varchar(255) NOT NULL,
  `mime_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_photos`
--

INSERT INTO `school_photos` (`photo_id`, `photo_name`, `mime_type`, `file_size`, `file_path`, `uploaded_at`) VALUES
(1, '107_1.jpg', 'image/jpeg', 409751, '../assets/School_Images/107_1.jpg', '2024-12-07 15:39:21'),
(2, '480_2.jpg', 'image/jpeg', 453910, '../assets/School_Images/480_2.jpg', '2024-12-07 15:39:21'),
(3, '522_3.jpg', 'image/jpeg', 565919, '../assets/School_Images/522_3.jpg', '2024-12-07 15:39:21'),
(4, '721_4.jpg', 'image/jpeg', 456864, '../assets/School_Images/721_4.jpg', '2024-12-07 15:39:21'),
(5, '357_5.jpg', 'image/jpeg', 179934, '../assets/School_Images/357_5.jpg', '2024-12-07 15:39:21'),
(26, '397_blog-post-03.jpg', 'image/jpeg', 12307, '../assets/School_Images/397_blog-post-03.jpg', '2024-12-07 18:38:17');

-- --------------------------------------------------------

--
-- Table structure for table `social_media`
--

CREATE TABLE `social_media` (
  `Social_Media_ID` int(11) NOT NULL,
  `Social_Media_Link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `social_media`
--

INSERT INTO `social_media` (`Social_Media_ID`, `Social_Media_Link`) VALUES
(1, 'https://www.facebook.com/depedtayo.liannhs?mibextid=LQQJ4d');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `TaskID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ContentID` int(11) NOT NULL,
  `Type` enum('Task','Announcement','Reminder') NOT NULL,
  `Title` varchar(255) NOT NULL,
  `taskContent` varchar(500) NOT NULL,
  `DueDate` date DEFAULT NULL,
  `DueTime` time DEFAULT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` enum('Assign','Draft','Schedule') NOT NULL,
  `Schedule_Date` date DEFAULT NULL,
  `Schedule_Time` time DEFAULT NULL,
  `ApprovalStatus` enum('Pending','Approved','Rejected') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`TaskID`, `UserID`, `ContentID`, `Type`, `Title`, `taskContent`, `DueDate`, `DueTime`, `TimeStamp`, `Status`, `Schedule_Date`, `Schedule_Time`, `ApprovalStatus`) VALUES
(878734, 1, 168475, 'Task', 'DLL Submission: Week 1', '<p>Please submit your DLL\'s for week 1. Refer to the Attachments below. Thanks!</p>', '2024-12-13', '23:59:00', '2024-12-11 08:01:42', 'Assign', NULL, NULL, 'Approved'),
(878735, 1, 168475, 'Task', 'DLL Submission: Week 2', '<p>Submit DLL for week 2. Thanks!</p>', '2024-12-20', '23:59:00', '2024-12-11 08:26:35', 'Assign', NULL, NULL, 'Approved'),
(878736, 1, 168475, 'Task', 'DLL Submission: Week 3', '<p>Please submit DLL\'s for week 3.</p>', '2024-12-27', '16:29:00', '2024-12-11 08:29:35', 'Assign', NULL, NULL, 'Approved'),
(878738, 1, 168475, 'Announcement', 'Christmas Party 2k25', '<p>ATTENTION! We will be having a Christmas party at 12/19/2024 @ exactly 1:00 pm onwards. Thanks</p>', '0000-00-00', '00:00:00', '2024-12-11 10:47:50', 'Assign', NULL, NULL, 'Approved'),
(878739, 1, 168475, 'Announcement', 'December Events', '<p>Please refer below for out December calendar. Thankyou!</p>', '0000-00-00', '00:00:00', '2024-12-11 11:00:28', 'Assign', NULL, NULL, 'Approved'),
(878742, 1, 168475, 'Announcement', 'CHANGES IN SCHOOL CALENDAR', '<p>See attachments for your reference</p>', NULL, NULL, '2024-12-11 12:47:52', 'Assign', NULL, NULL, 'Approved'),
(878746, 108, 168481, 'Task', 'fdfsd', '<p>fsdfsf</p>', '2025-01-04', '17:45:00', '2024-12-14 07:43:12', 'Assign', NULL, NULL, 'Approved'),
(878748, 108, 168481, 'Task', 'dadaad', '<p>adadadad</p>', '2024-12-28', '18:18:00', '2024-12-14 10:18:41', 'Assign', NULL, NULL, 'Approved'),
(878749, 108, 168481, 'Task', 'dadadad', '<p>dadsadad</p>', '2024-12-27', '18:19:00', '2024-12-14 10:19:29', 'Schedule', '2024-12-22', '18:19:00', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `task_user`
--

CREATE TABLE `task_user` (
  `Task_User_ID` int(11) NOT NULL,
  `ContentID` int(11) NOT NULL,
  `TaskID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Status` varchar(255) DEFAULT NULL,
  `StatusSMS` varchar(255) DEFAULT NULL,
  `Comment` varchar(255) DEFAULT NULL,
  `SubmitDate` timestamp NULL DEFAULT NULL,
  `ApproveDate` timestamp NULL DEFAULT NULL,
  `RejectDate` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_user`
--

INSERT INTO `task_user` (`Task_User_ID`, `ContentID`, `TaskID`, `UserID`, `Status`, `StatusSMS`, `Comment`, `SubmitDate`, `ApproveDate`, `RejectDate`) VALUES
(1694, 168475, 878734, 3, 'Submitted', NULL, NULL, '2024-12-11 08:20:55', NULL, NULL),
(1695, 168475, 878735, 3, 'Approved', NULL, '', '2024-12-11 08:28:01', '2024-12-13 17:18:02', NULL),
(1696, 168475, 878736, 3, 'Assigned', NULL, NULL, '2024-12-13 05:52:44', NULL, NULL),
(1698, 168475, 878738, 3, 'Missing', NULL, NULL, NULL, NULL, NULL),
(1699, 168475, 878739, 3, 'Missing', NULL, NULL, NULL, NULL, NULL),
(1702, 168475, 878742, 3, 'Assigned', NULL, NULL, NULL, NULL, NULL),
(1706, 168481, 878746, 3, 'Assigned', NULL, NULL, NULL, NULL, NULL),
(1707, 168481, 878749, 3, 'Assigned', NULL, NULL, NULL, NULL, NULL),
(1708, 168481, 878748, 3, 'Approved', NULL, '', '2024-12-15 03:54:13', '2024-12-15 03:57:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `TemplateID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `mimetype` varchar(100) NOT NULL,
  `size` int(11) NOT NULL,
  `uri` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`TemplateID`, `UserID`, `name`, `filename`, `mimetype`, `size`, `uri`, `created_at`) VALUES
(4, 1, 'DLL Template', '6724a26c65edf_DLL template.pdf', 'application/pdf', 519463, 'Templates/6724a26c65edf_DLL template.pdf', '2024-11-01 02:42:04');

-- --------------------------------------------------------

--
-- Table structure for table `todo`
--

CREATE TABLE `todo` (
  `TodoID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Due` date NOT NULL,
  `Status` varchar(255) NOT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `todo`
--

INSERT INTO `todo` (`TodoID`, `Title`, `Due`, `Status`, `TimeStamp`) VALUES
(1, 'Pass DLL for Grade 7', '2024-12-13', 'Completed', '2024-10-10 08:29:18'),
(3, 'Pass Manuscript', '2024-12-14', 'Completed', '2024-12-09 07:05:50'),
(4, 'Pass DLL for Grade 7', '2024-12-18', 'Active', '2024-12-14 11:59:45'),
(5, 'Pass DLL for Grade -Daisy', '2024-12-19', 'Active', '2024-12-15 03:53:52');

-- --------------------------------------------------------

--
-- Table structure for table `total_age`
--

CREATE TABLE `total_age` (
  `Total_Age_ID` int(11) NOT NULL,
  `Total_Age` int(11) NOT NULL,
  `Age_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `total_age`
--

INSERT INTO `total_age` (`Total_Age_ID`, `Total_Age`, `Age_ID`) VALUES
(2, 264748, 2);

-- --------------------------------------------------------

--
-- Table structure for table `total_cohort`
--

CREATE TABLE `total_cohort` (
  `Total_Cohort_ID` int(11) NOT NULL,
  `Total_Cohort_Figure` int(11) NOT NULL,
  `Total_Cohort_Rate` decimal(5,2) NOT NULL,
  `Cohort_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `total_cohort`
--

INSERT INTO `total_cohort` (`Total_Cohort_ID`, `Total_Cohort_Figure`, `Total_Cohort_Rate`, `Cohort_ID`) VALUES
(5, 26458, 999.99, 8);

-- --------------------------------------------------------

--
-- Table structure for table `total_dropout`
--

CREATE TABLE `total_dropout` (
  `Total_Dropout_ID` int(11) NOT NULL,
  `Total_Dropout_Figure` int(11) NOT NULL,
  `Total_Dropout_Rate` decimal(5,2) NOT NULL,
  `Dropout_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `total_dropout`
--

INSERT INTO `total_dropout` (`Total_Dropout_ID`, `Total_Dropout_Figure`, `Total_Dropout_Rate`, `Dropout_ID`) VALUES
(4, 46376, 999.99, 8);

-- --------------------------------------------------------

--
-- Table structure for table `total_enroll`
--

CREATE TABLE `total_enroll` (
  `Total_Enroll_ID` int(11) NOT NULL,
  `Total_Enroll_Gross` int(11) NOT NULL,
  `Total_Enroll_Net` int(11) NOT NULL,
  `Enroll_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `total_enroll`
--

INSERT INTO `total_enroll` (`Total_Enroll_ID`, `Total_Enroll_Gross`, `Total_Enroll_Net`, `Enroll_ID`) VALUES
(5, 2356, 32548, 8);

-- --------------------------------------------------------

--
-- Table structure for table `total_nat`
--

CREATE TABLE `total_nat` (
  `Total_NAT_ID` int(11) NOT NULL,
  `Total_NAT` int(11) NOT NULL,
  `NAT_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `total_nat`
--

INSERT INTO `total_nat` (`Total_NAT_ID`, `Total_NAT`, `NAT_ID`) VALUES
(2, 34847, 2);

-- --------------------------------------------------------

--
-- Table structure for table `total_promotion`
--

CREATE TABLE `total_promotion` (
  `Total_Promotion_ID` int(11) NOT NULL,
  `Total_Promotion_Figure` int(11) NOT NULL,
  `Total_Promotion_Rate` decimal(5,2) NOT NULL,
  `Promotion_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `total_promotion`
--

INSERT INTO `total_promotion` (`Total_Promotion_ID`, `Total_Promotion_Figure`, `Total_Promotion_Rate`, `Promotion_ID`) VALUES
(2, 163757, 999.99, 2);

-- --------------------------------------------------------

--
-- Table structure for table `total_repetition`
--

CREATE TABLE `total_repetition` (
  `Total_Repetition_ID` int(11) NOT NULL,
  `Total_Repetition_Figure` int(11) NOT NULL,
  `Total_Repetition_Rate` decimal(5,2) NOT NULL,
  `Repetition_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `total_repetition`
--

INSERT INTO `total_repetition` (`Total_Repetition_ID`, `Total_Repetition_Figure`, `Total_Repetition_Rate`, `Repetition_ID`) VALUES
(2, 235557, 999.99, 2);

-- --------------------------------------------------------

--
-- Table structure for table `total_transition`
--

CREATE TABLE `total_transition` (
  `Total_Transition_ID` int(11) NOT NULL,
  `Total_Transition_Figure` int(11) NOT NULL,
  `Total_Transition_Rate` decimal(5,2) NOT NULL,
  `Transition_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `total_transition`
--

INSERT INTO `total_transition` (`Total_Transition_ID`, `Total_Transition_Figure`, `Total_Transition_Rate`, `Transition_ID`) VALUES
(2, 35657, 999.99, 2);

-- --------------------------------------------------------

--
-- Table structure for table `transition`
--

CREATE TABLE `transition` (
  `Transition_ID` int(11) NOT NULL,
  `Transition_Figure` int(11) DEFAULT NULL,
  `Transition_Rate` decimal(5,2) DEFAULT NULL,
  `School_Year_ID` int(11) NOT NULL,
  `Grade_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transition`
--

INSERT INTO `transition` (`Transition_ID`, `Transition_Figure`, `Transition_Rate`, `School_Year_ID`, `Grade_ID`, `UserID`) VALUES
(2, 31213, 999.99, 1, 2, 3),
(3, 1212, 999.99, 1, 1, 3),
(4, 3232, 999.99, 1, 1, 3);

--
-- Triggers `transition`
--
DELIMITER $$
CREATE TRIGGER `after_insert_transition` AFTER INSERT ON `transition` FOR EACH ROW BEGIN
    DECLARE totalFigure INT;
    DECLARE totalRate DECIMAL(5, 2);

    -- Calculate totals for the same school year
    SELECT 
        SUM(Transition_Figure), SUM(Transition_Rate)
    INTO 
        totalFigure, totalRate
    FROM 
        Transition
    WHERE 
        School_Year_ID = NEW.School_Year_ID;

    -- Check if Total_Transition entry exists for this School_Year_ID
    IF EXISTS (
        SELECT 1 
        FROM Total_Transition 
        WHERE Transition_ID IN (
            SELECT Transition_ID 
            FROM Transition 
            WHERE School_Year_ID = NEW.School_Year_ID
        )
    ) THEN
        -- Update totals for the same School_Year_ID
        UPDATE Total_Transition
        SET Total_Transition_Figure = totalFigure, Total_Transition_Rate = totalRate
        WHERE Transition_ID IN (
            SELECT Transition_ID 
            FROM Transition 
            WHERE School_Year_ID = NEW.School_Year_ID
        );
    ELSE
        -- Insert new totals for the same School_Year_ID
        INSERT INTO Total_Transition (Total_Transition_Figure, Total_Transition_Rate, Transition_ID)
        VALUES (totalFigure, totalRate, NEW.Transition_ID);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `useracc`
--

CREATE TABLE `useracc` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `bday` date NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `address` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile` varchar(255) DEFAULT 'profile.jpg',
  `date_registered` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` enum('Pending','Approved','Rejected') NOT NULL,
  `role` enum('Admin','Department Head','Teacher') NOT NULL DEFAULT 'Teacher',
  `Rank` varchar(255) DEFAULT NULL,
  `dept_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `useracc`
--

INSERT INTO `useracc` (`UserID`, `Username`, `Password`, `fname`, `mname`, `lname`, `bday`, `mobile`, `sex`, `address`, `email`, `profile`, `date_registered`, `Status`, `role`, `Rank`, `dept_ID`) VALUES
(1, 'admin', 'f6fdffe48c908deb0f4c3bd36c032e72', 'Jorge', 'C', 'Bautista', '1975-10-16', '09345678901\r\n', 'Male', 'Calatagan, Batangas', 'jorgebautista@gmail.com', '67065a5205267.png', '2024-10-02 11:15:02', 'Approved', 'Admin', 'Principal II', NULL),
(3, 'user', '5c01703e81daea4260a0278036edcaff', 'Jamir', 'D', 'Hernandez', '2002-10-01', '09566630509', 'Male', 'Landing Townhomes Nasugbu, Batangas', 'jamiradrian.hernandez102602@gmail.com', '671361ec41c94.JPG', '2024-10-02 11:21:07', 'Approved', 'Teacher', 'Professor', NULL),
(5, 'sean', 'sean\r\n', 'Sean', 'I', 'Amorante', '2002-02-15', '09686708872', 'Male', 'Nasugbu, Batangas', 'amorantesean15@gmail.com', 'profile.jpg', '2024-10-05 19:36:25', 'Approved', 'Teacher', 'Professor', NULL),
(6, '9f45f531', '9f45f531', 'Christian', 'L', 'Abiog', '1980-01-23', '09984238249', 'Female', 'Lumaniag, Lian, Batangas', 'abiog.christian01@gmail.com', 'profile.jpg', '2024-10-09 11:57:38', 'Approved', 'Teacher', 'Teacher II', NULL),
(64, 'scienceuser', 'sciencepass', 'James', 'K', 'Smith', '2000-04-10', '09123456789', 'Male', 'Calatagan, Batangas', 'james.smith@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher I', NULL),
(65, 'englishuser', 'englishpass', 'Susan', 'L', 'Johnson', '1998-05-15', '09234567890', 'Female', 'Landing Townhomes Nasugbu', 'susan.johnson@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher II', NULL),
(66, 'mathuser', 'mathpass', 'Robert', 'A', 'Brown', '2001-06-20', '09345678900', 'Male', 'Calatagan, Batangas', 'robert.brown@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher III', NULL),
(67, 'tleuser', 'tlepass', 'Kevin', 'T', 'Lopez', '1997-10-11', '09789012345', 'Male', 'Nasugbu, Batangas', 'kevin.lopez@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher I', NULL),
(68, 'abmuser', 'abmpass', 'Jessica', 'P', 'Taylor', '1996-11-16', '09890123456', 'Female', 'Calatagan, Batangas', 'jessica.taylor@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher II', NULL),
(69, 'stemuser', 'stempass', 'Michael', 'G', 'Anderson', '1998-12-21', '09123456780', 'Male', 'Landing Townhomes Nasugbu', 'michael.anderson@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher III', NULL),
(70, 'sportstrackuser', 'sportstrackpass', 'Alex', 'N', 'Clark', '1997-04-20', '09567890124', 'Male', 'Landing Townhomes Nasugbu', 'alex.clark@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher I', NULL),
(71, 'artsdesignuser', 'artsdesignpass', 'Olivia', 'M', 'Harris', '1998-05-25', '09678901235', 'Female', 'Calatagan, Batangas', 'olivia.harris@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher II', NULL),
(72, 'socialstudiesuser', 'socialstudiespass', 'Emma', 'L', 'Wilson', '2001-06-12', '09161234567', 'Female', 'Calatagan, Batangas', 'emma.wilson@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher III', NULL),
(73, 'healthuser', 'healthpass', 'Sophia', 'R', 'Garcia', '1997-10-05', '09501234571', 'Female', 'Calatagan, Batangas', 'sophia.garcia@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher I', NULL),
(74, 'accountinguser', 'accountingpass', 'Oliver', 'M', 'Wilson', '2001-11-12', '09611234572', 'Male', 'Nasugbu, Batangas', 'oliver.wilson@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher II', NULL),
(75, 'businessuser', 'businesspass', 'Charlotte', 'N', 'Martinez', '1999-12-18', '09721234573', 'Female', 'Calatagan, Batangas', 'charlotte.martinez@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher III', NULL),
(76, 'statisticsuser', 'statisticspass', 'Zoe', 'B', 'Thompson', '2002-04-12', '09415678907', 'Female', 'Calatagan, Batangas', 'zoe.thompson@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher III', NULL),
(77, 'philosophyuser', 'philosophypass', 'Mason', 'J', 'White', '1997-05-30', '09526789018', 'Male', 'Nasugbu, Batangas', 'mason.white@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher II', NULL),
(78, 'literatureuser', 'literaturepass', 'Lily', 'G', 'Harris', '1998-06-25', '09637890129', 'Female', 'Calatagan, Batangas', 'lily.harris@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher I', NULL),
(79, 'technologyuser', 'technologypass', 'Logan', 'M', 'Lewis', '1996-09-18', '09290123452', 'Male', 'Nasugbu, Batangas', 'logan.lewis@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher I', NULL),
(80, 'environmentaluser', 'environmentalpass', 'Ella', 'R', 'Walker', '2002-10-21', '09301234563', 'Female', 'Calatagan, Batangas', 'ella.walker@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher I', NULL),
(81, 'artuser', 'artpass', 'Jackson', 'N', 'Hall', '1997-11-30', '09412345674', 'Male', 'Nasugbu, Batangas', 'jackson.hall@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher II', NULL),
(82, 'danceuser', 'dancepass', 'Sofia', 'H', 'Young', '1998-12-25', '09523456785', 'Female', 'Calatagan, Batangas', 'sofia.young@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher III', NULL),
(83, 'filmuser', 'filmpass', 'Aiden', 'G', 'King', '2001-01-22', '09634567896', 'Male', 'Nasugbu, Batangas', 'aiden.king@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher I', NULL),
(84, 'architectureuser', 'architecturepass', 'Grace', 'T', 'Scott', '1999-02-10', '09145678907', 'Female', 'Calatagan, Batangas', 'grace.scott@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher II', NULL),
(85, 'businessadminuser', 'businessadminpass', 'Samuel', 'M', 'Bennett', '1996-03-15', '09356789019', 'Male', 'Nasugbu, Batangas', 'samuel.bennett@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher III', NULL),
(86, 'journalismuser', 'journalismpass', 'Harper', 'K', 'Murphy', '2000-04-28', '09467890120', 'Female', 'Calatagan, Batangas', 'harper.murphy@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher III', NULL),
(87, 'performingartsuser', 'performingartspass', 'Benjamin', 'R', 'Rivera', '1998-05-18', '09578901231', 'Male', 'Nasugbu, Batangas', 'benjamin.rivera@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher II', NULL),
(88, 'mathematicsuser', 'mathematicspass', 'Scarlett', 'J', 'Cooper', '2002-06-14', '09689012342', 'Female', 'Calatagan, Batangas', 'scarlett.cooper@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher I', NULL),
(89, 'musicuser', 'musicpass', 'Christopher', 'L', 'Reed', '1997-07-22', '09109876543', 'Male', 'Nasugbu, Batangas', 'christopher.reed@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher III', NULL),
(90, 'scienceuser', 'sciencepass', 'Abigail', 'S', 'Cook', '1998-08-30', '09210987654', 'Female', 'Calatagan, Batangas', 'abigail.cook@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher II', NULL),
(91, 'mathematicsuser2', 'mathematicspass2', 'Andrew', 'N', 'Foster', '1996-09-29', '09321234565', 'Male', 'Nasugbu, Batangas', 'andrew.foster@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher I', NULL),
(92, 'musicuser2', 'musicpass2', 'Victoria', 'W', 'Gomez', '2001-10-10', '09432345676', 'Female', 'Calatagan, Batangas', 'victoria.gomez@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher I', NULL),
(93, 'finedartsuser', 'finedartspass', 'Daniel', 'O', 'Woods', '1999-11-11', '09543456787', 'Male', 'Nasugbu, Batangas', 'daniel.woods@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher II', NULL),
(94, 'roboticsuser', 'roboticspass', 'Nora', 'R', 'Hughes', '2000-12-12', '09654567898', 'Female', 'Calatagan, Batangas', 'nora.hughes@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher III', NULL),
(95, 'physicaleducationuser', 'physicaleducationpass', 'George', 'L', 'Wells', '1997-01-01', '09165678909', 'Male', 'Nasugbu, Batangas', 'george.wells@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher I', NULL),
(96, 'civicsuser', 'civicspass', 'Ruth', 'N', 'Clark', '1999-02-14', '09276789010', 'Female', 'Calatagan, Batangas', 'ruth.clark@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Teacher', 'Teacher II', NULL),
(97, 'historyuser', 'historypass', 'Henry', 'K', 'Lee', '1998-03-21', '09387890111', 'Male', 'Nasugbu, Batangas', 'henry.lee@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Rejected', '', 'Teacher III', NULL),
(98, 'englishuser', 'englishpass', 'Megan', 'H', 'Hall', '2002-04-19', '09498901212', 'Female', 'Calatagan, Batangas', 'megan.hall@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Pending', 'Teacher', 'Teacher III', NULL),
(99, 'frenchuser', 'frenchpass', 'Bradley', 'F', 'Lopez', '1996-05-23', '09589012323', 'Male', 'Nasugbu, Batangas', 'bradley.lopez@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Pending', 'Teacher', 'Teacher II', NULL),
(100, 'spanishuser', 'spanishpass', 'Emily', 'G', 'Collins', '2000-06-30', '09690123434', 'Female', 'Calatagan, Batangas', 'emily.collins@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Pending', 'Teacher', 'Teacher I', NULL),
(101, 'swimminguser', 'swimmingpass', 'Evelyn', 'M', 'Nguyen', '1997-07-16', '09101234545', 'Female', 'Nasugbu, Batangas', 'evelyn.nguyen@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Pending', 'Teacher', 'Teacher I', NULL),
(102, 'woodworkinguser', 'woodworkingpass', 'Cameron', 'J', 'Robinson', '1999-08-11', '09212345656', 'Male', 'Calatagan, Batangas', 'cameron.robinson@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Pending', 'Teacher', 'Teacher II', NULL),
(103, 'designuser', 'designpass', 'Leah', 'L', 'Smith', '2002-09-15', '09323456767', 'Female', 'Nasugbu, Batangas', 'leah.smith@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Pending', 'Teacher', 'Teacher III', NULL),
(104, 'historyuser2', 'historypass2', 'Nicholas', 'T', 'Baker', '2001-10-24', '09434567878', 'Male', 'Calatagan, Batangas', 'nicholas.baker@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Pending', 'Teacher', 'Teacher III', NULL),
(105, 'computeruser', 'computerpass', 'Samantha', 'M', 'Adams', '1998-11-05', '09545678989', 'Female', 'Nasugbu, Batangas', 'samantha.adams@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Pending', 'Teacher', 'Teacher II', NULL),
(106, 'athleticsuser', 'athleticspass', 'Isaac', 'N', 'Nelson', '1996-12-30', '09156789090', 'Male', 'Calatagan, Batangas', 'isaac.nelson@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Pending', 'Teacher', 'Teacher I', NULL),
(107, 'mapehuser', 'mapehpass', 'Emily', 'M', 'Wilson', '1999-07-25', '09456789012', 'Female', 'Nasugbu, Batangas', 'emily.wilson@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Department Head', 'Master Teacher I', 1),
(108, 'English', 'f899d5250ba5c721ef78b561445424f5', 'Richard', 'N', 'Garcia', '2002-08-30', '09567890123', 'Male', 'Landing Townhomes Nasugbu', 'hernandezjamiradriandizon@gmail.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Department Head', 'Master Teacher II', 2),
(110, 'humssuser', 'humsspass', 'Sarah', 'H', 'Thompson', '2000-01-26', '09234567891', 'Female', 'Calatagan, Batangas', 'abiog.christian29@gmail.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Department Head', 'Master Teacher I', 5),
(111, 'gasuser', 'gaspass', 'David', 'J', 'Martinez', '2001-02-11', '09345678902', 'Male', 'Nasugbu, Batangas', 'david.martinez@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Department Head', 'Master Teacher II', 6),
(112, 'tvluser', 'tvlpass', 'Emily', 'S', 'White', '1999-03-15', '09456789013', 'Female', 'Calatagan, Batangas', 'emily.white@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Department Head', 'Master Teacher III', 7),
(113, 'psychologyuser', 'psychologypass', 'Liam', 'C', 'Brown', '1999-07-14', '09271234568', 'Male', 'Nasugbu, Batangas', 'liam.brown@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Department Head', 'Master Teacher I', 8),
(114, 'ictuser', 'ictpass', 'Mia', 'K', 'Davis', '2000-08-25', '09381234569', 'Female', 'Calatagan, Batangas', 'mia.davis@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Department Head', 'Master Teacher II', 9),
(115, 'economicsuser', 'economicspass', 'Noah', 'A', 'Martinez', '1998-09-30', '09491234570', 'Male', 'Nasugbu, Batangas', 'noah.martinez@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Department Head', 'Master Teacher III', 10),
(116, 'sociologyuser', 'sociologypass', 'Ethan', 'T', 'Anderson', '2000-01-20', '09182345674', 'Male', 'Nasugbu, Batangas', 'ethan.anderson@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Department Head', 'Master Teacher III', 11),
(117, 'geographyuser', 'geographypass', 'Ava', 'H', 'Robinson', '1996-02-15', '09293456785', 'Female', 'Calatagan, Batangas', 'ava.robinson@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Department Head', 'Master Teacher II', 12),
(118, 'dramauser', 'dramapass', 'Lucas', 'W', 'Taylor', '1998-03-28', '09304567896', 'Male', 'Nasugbu, Batangas', 'lucas.taylor@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Department Head', 'Master Teacher I', 13),
(119, 'chemistryuser', 'chemistrypass', 'James', 'C', 'Martin', '1999-07-19', '09748901230', 'Male', 'Nasugbu, Batangas', 'james.martin@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Department Head', 'Master Teacher III', 14),
(120, 'physicsuser', 'physicspass', 'Isabella', 'A', 'Jackson', '2000-08-10', '09189012341', 'Female', 'Calatagan, Batangas', 'isabella.jackson@school.com', 'profile.jpg', '2024-10-02 11:15:02', 'Approved', 'Department Head', 'Master Teacher II', 15),
(136, '123456', '123456', 'John', 'M.', 'Doe', '1990-01-01', '09123456789', 'Male', '123 Main St', 'johndoe@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher I', NULL),
(137, '234567', '234567', 'Jane', 'A.', 'Smith', '1988-02-02', '09123456788', 'Female', '456 Elm St', 'janesmith@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher II', NULL),
(138, '345678', '345678', 'Michael', 'B.', 'Johnson', '1995-03-03', '09123456787', 'Male', '789 Pine St', 'michaeljohnson@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher III', NULL),
(139, '456789', '456789', 'Emily', 'C.', 'Williams', '1992-04-04', '09123456786', 'Female', '101 Maple St', 'emilywilliams@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher IV', NULL),
(140, '567890', '567890', 'Chris', 'D.', 'Brown', '1985-05-05', '09123456785', 'Male', '202 Oak St', 'chrisbrown@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher V', NULL),
(141, '678901', '678901', 'Anna', 'E.', 'Jones', '1993-06-06', '09123456784', 'Female', '303 Birch St', 'annajones@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher I', NULL),
(142, '789012', '789012', 'David', 'F.', 'Garcia', '1991-07-07', '09123456783', 'Male', '404 Cedar St', 'davidgarcia@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher II', NULL),
(143, '890123', '890123', 'Sophia', 'G.', 'Martinez', '1994-08-08', '09123456782', 'Female', '505 Cherry St', 'sophiamartinez@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher III', NULL),
(144, '901234', '901234', 'Daniel', 'H.', 'Rodriguez', '1987-09-09', '09123456781', 'Male', '606 Walnut St', 'danielrodriguez@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher IV', NULL),
(145, '012345', '012345', 'Olivia', 'I.', 'Lee', '1996-10-10', '09123456780', 'Female', '707 Ash St', 'olivialee@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher V', NULL),
(146, '135790', '135790', 'James', 'J.', 'Perez', '1989-11-11', '09123456779', 'Male', '808 Fir St', 'jamesperez@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher I', NULL),
(147, '246801', '246801', 'Mia', 'K.', 'Wilson', '1990-12-12', '09123456778', 'Female', '909 Spruce St', 'miawilson@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher II', NULL),
(148, '357912', '357912', 'Lucas', 'L.', 'Anderson', '1995-01-13', '09123456777', 'Male', '1010 Elm St', 'lucasanderson@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher III', NULL),
(149, '468023', '468023', 'Ava', 'M.', 'Thomas', '1993-02-14', '09123456776', 'Female', '1111 Maple St', 'avathomas@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher IV', NULL),
(150, '579134', '579134', 'Ethan', 'N.', 'Taylor', '1988-03-15', '09123456775', 'Male', '1212 Oak St', 'ethantaylor@gmail.com', 'profile.jpg', '2024-10-10 05:03:43', 'Approved', 'Teacher', 'Teacher V', NULL),
(152, '5b13b3a5', 'f6225c927ccbcc8d564f35d3e68d55bd', 'Jamir', 'H', 'Roldan', '2002-10-26', '09566630509', 'Male', 'Nasugbu', '21-77830@g.batstate-u.edu.ph', 'profile.jpg', '2024-12-10 16:24:49', 'Approved', 'Teacher', 'Teacher I', NULL),
(153, '6fac7ec8', '7ff486c1d6f875d099c7bee7b233a442', 'paul', 'I', 'Abiog', '2004-12-13', '09566630609', 'Male', 'dasdada', 'geraldine.hernandez1026@gmail.com', 'profile.jpg', '2024-12-15 03:59:03', 'Pending', 'Teacher', 'Teacher I', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `usercontent`
--

CREATE TABLE `usercontent` (
  `UserContentID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ContentID` int(11) NOT NULL,
  `Status` enum('Active','Archived','Removed') NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usercontent`
--

INSERT INTO `usercontent` (`UserContentID`, `UserID`, `ContentID`, `Status`, `Timestamp`) VALUES
(36037, 3, 168475, 'Active', '2024-12-11 07:43:09'),
(36038, 3, 168481, 'Active', '2024-12-14 07:16:48');

-- --------------------------------------------------------

--
-- Table structure for table `userfolders`
--

CREATE TABLE `userfolders` (
  `UserFolderID` int(11) NOT NULL,
  `UserContentID` int(11) DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userfolders`
--

INSERT INTO `userfolders` (`UserFolderID`, `UserContentID`, `Timestamp`) VALUES
(74, 36037, '2024-12-11 07:43:09'),
(75, 36038, '2024-12-14 07:16:48');

-- --------------------------------------------------------

--
-- Table structure for table `usertodo`
--

CREATE TABLE `usertodo` (
  `UserID` int(11) NOT NULL,
  `TodoID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usertodo`
--

INSERT INTO `usertodo` (`UserID`, `TodoID`) VALUES
(3, 1),
(3, 3),
(3, 4),
(3, 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `age`
--
ALTER TABLE `age`
  ADD PRIMARY KEY (`Age_ID`),
  ADD KEY `School_Year_ID` (`School_Year_ID`),
  ADD KEY `Grade_ID` (`Grade_ID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `attachment`
--
ALTER TABLE `attachment`
  ADD PRIMARY KEY (`Attach_ID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `TaskID` (`TaskID`),
  ADD KEY `ContentID` (`ContentID`);

--
-- Indexes for table `chairperson`
--
ALTER TABLE `chairperson`
  ADD PRIMARY KEY (`Chairperson_ID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `Grade_ID` (`Grade_ID`);

--
-- Indexes for table `cohort_survival`
--
ALTER TABLE `cohort_survival`
  ADD PRIMARY KEY (`Cohort_ID`),
  ADD KEY `School_Year_ID` (`School_Year_ID`),
  ADD KEY `Grade_ID` (`Grade_ID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`CommentID`),
  ADD KEY `ContentID` (`ContentID`),
  ADD KEY `TaskID` (`TaskID`),
  ADD KEY `IncomingID` (`IncomingID`),
  ADD KEY `OutgoingID` (`OutgoingID`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_ID`);

--
-- Indexes for table `departmentfolders`
--
ALTER TABLE `departmentfolders`
  ADD PRIMARY KEY (`DepartmentFolderID`),
  ADD KEY `dept_ID` (`dept_ID`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`DocuID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ContentID` (`ContentID`),
  ADD KEY `TaskID` (`TaskID`),
  ADD KEY `GradeLevelFolderID` (`GradeLevelFolderID`),
  ADD KEY `UserFolderID` (`UserFolderID`);

--
-- Indexes for table `dropout`
--
ALTER TABLE `dropout`
  ADD PRIMARY KEY (`Dropout_ID`),
  ADD KEY `School_Year_ID` (`School_Year_ID`),
  ADD KEY `Grade_ID` (`Grade_ID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `enroll`
--
ALTER TABLE `enroll`
  ADD PRIMARY KEY (`Enroll_ID`),
  ADD KEY `School_Year_ID` (`School_Year_ID`),
  ADD KEY `Grade_ID` (`Grade_ID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `feedcontent`
--
ALTER TABLE `feedcontent`
  ADD PRIMARY KEY (`ContentID`),
  ADD KEY `dept_ID` (`dept_ID`);

--
-- Indexes for table `grade`
--
ALTER TABLE `grade`
  ADD PRIMARY KEY (`Grade_ID`);

--
-- Indexes for table `gradelevelfolders`
--
ALTER TABLE `gradelevelfolders`
  ADD PRIMARY KEY (`GradeLevelFolderID`),
  ADD KEY `fk_departmentfolders` (`DepartmentFolderID`),
  ADD KEY `fk_feedcontent` (`ContentID`);

--
-- Indexes for table `mps`
--
ALTER TABLE `mps`
  ADD PRIMARY KEY (`mpsID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ContentID` (`ContentID`),
  ADD KEY `Quarter_ID` (`Quarter_ID`);

--
-- Indexes for table `nat`
--
ALTER TABLE `nat`
  ADD PRIMARY KEY (`NAT_ID`),
  ADD KEY `School_Year_ID` (`School_Year_ID`),
  ADD KEY `Grade_ID` (`Grade_ID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`NotifID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ContentID` (`ContentID`),
  ADD KEY `TaskID` (`TaskID`);

--
-- Indexes for table `notif_user`
--
ALTER TABLE `notif_user`
  ADD PRIMARY KEY (`Notif_User_ID`),
  ADD KEY `NotifID` (`NotifID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `otp`
--
ALTER TABLE `otp`
  ADD PRIMARY KEY (`otp_ID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `performance_indicator`
--
ALTER TABLE `performance_indicator`
  ADD PRIMARY KEY (`Performance_ID`),
  ADD KEY `School_Year_ID` (`School_Year_ID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `Grade_ID` (`Grade_ID`),
  ADD KEY `Enroll_ID` (`Enroll_ID`),
  ADD KEY `Dropout_ID` (`Dropout_ID`),
  ADD KEY `Promotion_ID` (`Promotion_ID`),
  ADD KEY `Cohort_ID` (`Cohort_ID`),
  ADD KEY `Repetition_ID` (`Repetition_ID`),
  ADD KEY `Age_ID` (`Age_ID`),
  ADD KEY `NAT_ID` (`NAT_ID`),
  ADD KEY `Transition_ID` (`Transition_ID`);

--
-- Indexes for table `promotion`
--
ALTER TABLE `promotion`
  ADD PRIMARY KEY (`Promotion_ID`),
  ADD KEY `School_Year_ID` (`School_Year_ID`),
  ADD KEY `Grade_ID` (`Grade_ID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `quarter`
--
ALTER TABLE `quarter`
  ADD PRIMARY KEY (`Quarter_ID`),
  ADD KEY `School_Year_ID` (`School_Year_ID`);

--
-- Indexes for table `repetition`
--
ALTER TABLE `repetition`
  ADD PRIMARY KEY (`Repetition_ID`),
  ADD KEY `School_Year_ID` (`School_Year_ID`),
  ADD KEY `Grade_ID` (`Grade_ID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `schoolyear`
--
ALTER TABLE `schoolyear`
  ADD PRIMARY KEY (`School_Year_ID`);

--
-- Indexes for table `school_details`
--
ALTER TABLE `school_details`
  ADD PRIMARY KEY (`school_details_ID`),
  ADD KEY `Mobile_ID` (`Mobile_ID`),
  ADD KEY `Social_Media_ID` (`Social_Media_ID`);

--
-- Indexes for table `school_mobile`
--
ALTER TABLE `school_mobile`
  ADD PRIMARY KEY (`Mobile_ID`);

--
-- Indexes for table `school_photos`
--
ALTER TABLE `school_photos`
  ADD PRIMARY KEY (`photo_id`);

--
-- Indexes for table `social_media`
--
ALTER TABLE `social_media`
  ADD PRIMARY KEY (`Social_Media_ID`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`TaskID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ContentID` (`ContentID`);

--
-- Indexes for table `task_user`
--
ALTER TABLE `task_user`
  ADD PRIMARY KEY (`Task_User_ID`),
  ADD KEY `TaskID` (`TaskID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ContentID` (`ContentID`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`TemplateID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `todo`
--
ALTER TABLE `todo`
  ADD PRIMARY KEY (`TodoID`);

--
-- Indexes for table `total_age`
--
ALTER TABLE `total_age`
  ADD PRIMARY KEY (`Total_Age_ID`),
  ADD KEY `Age_ID` (`Age_ID`);

--
-- Indexes for table `total_cohort`
--
ALTER TABLE `total_cohort`
  ADD PRIMARY KEY (`Total_Cohort_ID`),
  ADD KEY `Cohort_ID` (`Cohort_ID`);

--
-- Indexes for table `total_dropout`
--
ALTER TABLE `total_dropout`
  ADD PRIMARY KEY (`Total_Dropout_ID`),
  ADD KEY `Dropout_ID` (`Dropout_ID`);

--
-- Indexes for table `total_enroll`
--
ALTER TABLE `total_enroll`
  ADD PRIMARY KEY (`Total_Enroll_ID`),
  ADD KEY `Enroll_ID` (`Enroll_ID`);

--
-- Indexes for table `total_nat`
--
ALTER TABLE `total_nat`
  ADD PRIMARY KEY (`Total_NAT_ID`),
  ADD KEY `NAT_ID` (`NAT_ID`);

--
-- Indexes for table `total_promotion`
--
ALTER TABLE `total_promotion`
  ADD PRIMARY KEY (`Total_Promotion_ID`),
  ADD KEY `Promotion_ID` (`Promotion_ID`);

--
-- Indexes for table `total_repetition`
--
ALTER TABLE `total_repetition`
  ADD PRIMARY KEY (`Total_Repetition_ID`),
  ADD KEY `Repetition_ID` (`Repetition_ID`);

--
-- Indexes for table `total_transition`
--
ALTER TABLE `total_transition`
  ADD PRIMARY KEY (`Total_Transition_ID`),
  ADD KEY `Transition_ID` (`Transition_ID`);

--
-- Indexes for table `transition`
--
ALTER TABLE `transition`
  ADD PRIMARY KEY (`Transition_ID`),
  ADD KEY `School_Year_ID` (`School_Year_ID`),
  ADD KEY `Grade_ID` (`Grade_ID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `useracc`
--
ALTER TABLE `useracc`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_dept` (`dept_ID`);

--
-- Indexes for table `usercontent`
--
ALTER TABLE `usercontent`
  ADD PRIMARY KEY (`UserContentID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ContentID` (`ContentID`);

--
-- Indexes for table `userfolders`
--
ALTER TABLE `userfolders`
  ADD PRIMARY KEY (`UserFolderID`),
  ADD KEY `UserContentID` (`UserContentID`);

--
-- Indexes for table `usertodo`
--
ALTER TABLE `usertodo`
  ADD PRIMARY KEY (`UserID`,`TodoID`),
  ADD KEY `TodoID` (`TodoID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `age`
--
ALTER TABLE `age`
  MODIFY `Age_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `attachment`
--
ALTER TABLE `attachment`
  MODIFY `Attach_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=928;

--
-- AUTO_INCREMENT for table `chairperson`
--
ALTER TABLE `chairperson`
  MODIFY `Chairperson_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cohort_survival`
--
ALTER TABLE `cohort_survival`
  MODIFY `Cohort_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `CommentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `dept_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `departmentfolders`
--
ALTER TABLE `departmentfolders`
  MODIFY `DepartmentFolderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `DocuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `dropout`
--
ALTER TABLE `dropout`
  MODIFY `Dropout_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `enroll`
--
ALTER TABLE `enroll`
  MODIFY `Enroll_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `feedcontent`
--
ALTER TABLE `feedcontent`
  MODIFY `ContentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=528141;

--
-- AUTO_INCREMENT for table `grade`
--
ALTER TABLE `grade`
  MODIFY `Grade_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gradelevelfolders`
--
ALTER TABLE `gradelevelfolders`
  MODIFY `GradeLevelFolderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `mps`
--
ALTER TABLE `mps`
  MODIFY `mpsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `nat`
--
ALTER TABLE `nat`
  MODIFY `NAT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `NotifID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2216;

--
-- AUTO_INCREMENT for table `notif_user`
--
ALTER TABLE `notif_user`
  MODIFY `Notif_User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `otp`
--
ALTER TABLE `otp`
  MODIFY `otp_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `performance_indicator`
--
ALTER TABLE `performance_indicator`
  MODIFY `Performance_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `promotion`
--
ALTER TABLE `promotion`
  MODIFY `Promotion_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `quarter`
--
ALTER TABLE `quarter`
  MODIFY `Quarter_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `repetition`
--
ALTER TABLE `repetition`
  MODIFY `Repetition_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `schoolyear`
--
ALTER TABLE `schoolyear`
  MODIFY `School_Year_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `school_details`
--
ALTER TABLE `school_details`
  MODIFY `school_details_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `school_mobile`
--
ALTER TABLE `school_mobile`
  MODIFY `Mobile_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `school_photos`
--
ALTER TABLE `school_photos`
  MODIFY `photo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `social_media`
--
ALTER TABLE `social_media`
  MODIFY `Social_Media_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `TaskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=878750;

--
-- AUTO_INCREMENT for table `task_user`
--
ALTER TABLE `task_user`
  MODIFY `Task_User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1709;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `TemplateID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `todo`
--
ALTER TABLE `todo`
  MODIFY `TodoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `total_age`
--
ALTER TABLE `total_age`
  MODIFY `Total_Age_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `total_cohort`
--
ALTER TABLE `total_cohort`
  MODIFY `Total_Cohort_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `total_dropout`
--
ALTER TABLE `total_dropout`
  MODIFY `Total_Dropout_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `total_enroll`
--
ALTER TABLE `total_enroll`
  MODIFY `Total_Enroll_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `total_nat`
--
ALTER TABLE `total_nat`
  MODIFY `Total_NAT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `total_promotion`
--
ALTER TABLE `total_promotion`
  MODIFY `Total_Promotion_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `total_repetition`
--
ALTER TABLE `total_repetition`
  MODIFY `Total_Repetition_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `total_transition`
--
ALTER TABLE `total_transition`
  MODIFY `Total_Transition_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transition`
--
ALTER TABLE `transition`
  MODIFY `Transition_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `useracc`
--
ALTER TABLE `useracc`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `usercontent`
--
ALTER TABLE `usercontent`
  MODIFY `UserContentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36039;

--
-- AUTO_INCREMENT for table `userfolders`
--
ALTER TABLE `userfolders`
  MODIFY `UserFolderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `age`
--
ALTER TABLE `age`
  ADD CONSTRAINT `age_ibfk_1` FOREIGN KEY (`School_Year_ID`) REFERENCES `schoolyear` (`School_Year_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `age_ibfk_2` FOREIGN KEY (`Grade_ID`) REFERENCES `grade` (`Grade_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `age_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`);

--
-- Constraints for table `attachment`
--
ALTER TABLE `attachment`
  ADD CONSTRAINT `attachment_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `attachment_ibfk_2` FOREIGN KEY (`TaskID`) REFERENCES `tasks` (`TaskID`) ON DELETE CASCADE,
  ADD CONSTRAINT `attachment_ibfk_3` FOREIGN KEY (`ContentID`) REFERENCES `feedcontent` (`ContentID`) ON DELETE CASCADE;

--
-- Constraints for table `chairperson`
--
ALTER TABLE `chairperson`
  ADD CONSTRAINT `chairperson_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `chairperson_ibfk_2` FOREIGN KEY (`Grade_ID`) REFERENCES `grade` (`Grade_ID`) ON DELETE CASCADE;

--
-- Constraints for table `cohort_survival`
--
ALTER TABLE `cohort_survival`
  ADD CONSTRAINT `cohort_survival_ibfk_1` FOREIGN KEY (`School_Year_ID`) REFERENCES `schoolyear` (`School_Year_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `cohort_survival_ibfk_2` FOREIGN KEY (`Grade_ID`) REFERENCES `grade` (`Grade_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `cohort_survival_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`ContentID`) REFERENCES `feedcontent` (`ContentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`TaskID`) REFERENCES `tasks` (`TaskID`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`IncomingID`) REFERENCES `useracc` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_4` FOREIGN KEY (`OutgoingID`) REFERENCES `useracc` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `departmentfolders`
--
ALTER TABLE `departmentfolders`
  ADD CONSTRAINT `fk_departmentfolders_department` FOREIGN KEY (`dept_ID`) REFERENCES `department` (`dept_ID`) ON DELETE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`ContentID`) REFERENCES `feedcontent` (`ContentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_ibfk_3` FOREIGN KEY (`TaskID`) REFERENCES `tasks` (`TaskID`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_ibfk_4` FOREIGN KEY (`GradeLevelFolderID`) REFERENCES `gradelevelfolders` (`GradeLevelFolderID`),
  ADD CONSTRAINT `documents_ibfk_5` FOREIGN KEY (`UserFolderID`) REFERENCES `userfolders` (`UserFolderID`);

--
-- Constraints for table `dropout`
--
ALTER TABLE `dropout`
  ADD CONSTRAINT `dropout_ibfk_1` FOREIGN KEY (`School_Year_ID`) REFERENCES `schoolyear` (`School_Year_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `dropout_ibfk_2` FOREIGN KEY (`Grade_ID`) REFERENCES `grade` (`Grade_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `dropout_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`);

--
-- Constraints for table `enroll`
--
ALTER TABLE `enroll`
  ADD CONSTRAINT `enroll_ibfk_1` FOREIGN KEY (`School_Year_ID`) REFERENCES `schoolyear` (`School_Year_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `enroll_ibfk_2` FOREIGN KEY (`Grade_ID`) REFERENCES `grade` (`Grade_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `enroll_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`);

--
-- Constraints for table `feedcontent`
--
ALTER TABLE `feedcontent`
  ADD CONSTRAINT `feedcontent_ibfk_1` FOREIGN KEY (`dept_ID`) REFERENCES `department` (`dept_ID`) ON DELETE CASCADE;

--
-- Constraints for table `gradelevelfolders`
--
ALTER TABLE `gradelevelfolders`
  ADD CONSTRAINT `fk_departmentfolders` FOREIGN KEY (`DepartmentFolderID`) REFERENCES `departmentfolders` (`DepartmentFolderID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_feedcontent` FOREIGN KEY (`ContentID`) REFERENCES `feedcontent` (`ContentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `gradelevelfolders_ibfk_1` FOREIGN KEY (`DepartmentFolderID`) REFERENCES `departmentfolders` (`DepartmentFolderID`);

--
-- Constraints for table `mps`
--
ALTER TABLE `mps`
  ADD CONSTRAINT `mps_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `mps_ibfk_2` FOREIGN KEY (`ContentID`) REFERENCES `feedcontent` (`ContentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `mps_ibfk_3` FOREIGN KEY (`Quarter_ID`) REFERENCES `quarter` (`Quarter_ID`) ON DELETE CASCADE;

--
-- Constraints for table `nat`
--
ALTER TABLE `nat`
  ADD CONSTRAINT `nat_ibfk_1` FOREIGN KEY (`School_Year_ID`) REFERENCES `schoolyear` (`School_Year_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `nat_ibfk_2` FOREIGN KEY (`Grade_ID`) REFERENCES `grade` (`Grade_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `nat_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`ContentID`) REFERENCES `feedcontent` (`ContentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`TaskID`) REFERENCES `tasks` (`TaskID`) ON DELETE CASCADE;

--
-- Constraints for table `notif_user`
--
ALTER TABLE `notif_user`
  ADD CONSTRAINT `notif_user_ibfk_1` FOREIGN KEY (`NotifID`) REFERENCES `notifications` (`NotifID`) ON DELETE CASCADE,
  ADD CONSTRAINT `notif_user_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`);

--
-- Constraints for table `otp`
--
ALTER TABLE `otp`
  ADD CONSTRAINT `otp_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `performance_indicator`
--
ALTER TABLE `performance_indicator`
  ADD CONSTRAINT `performance_indicator_ibfk_1` FOREIGN KEY (`School_Year_ID`) REFERENCES `schoolyear` (`School_Year_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `performance_indicator_ibfk_10` FOREIGN KEY (`NAT_ID`) REFERENCES `nat` (`NAT_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `performance_indicator_ibfk_11` FOREIGN KEY (`Transition_ID`) REFERENCES `transition` (`Transition_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `performance_indicator_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`),
  ADD CONSTRAINT `performance_indicator_ibfk_3` FOREIGN KEY (`Grade_ID`) REFERENCES `grade` (`Grade_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `performance_indicator_ibfk_4` FOREIGN KEY (`Enroll_ID`) REFERENCES `enroll` (`Enroll_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `performance_indicator_ibfk_5` FOREIGN KEY (`Dropout_ID`) REFERENCES `dropout` (`Dropout_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `performance_indicator_ibfk_6` FOREIGN KEY (`Promotion_ID`) REFERENCES `promotion` (`Promotion_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `performance_indicator_ibfk_7` FOREIGN KEY (`Cohort_ID`) REFERENCES `cohort_survival` (`Cohort_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `performance_indicator_ibfk_8` FOREIGN KEY (`Repetition_ID`) REFERENCES `repetition` (`Repetition_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `performance_indicator_ibfk_9` FOREIGN KEY (`Age_ID`) REFERENCES `age` (`Age_ID`) ON DELETE CASCADE;

--
-- Constraints for table `promotion`
--
ALTER TABLE `promotion`
  ADD CONSTRAINT `promotion_ibfk_1` FOREIGN KEY (`School_Year_ID`) REFERENCES `schoolyear` (`School_Year_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_ibfk_2` FOREIGN KEY (`Grade_ID`) REFERENCES `grade` (`Grade_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`);

--
-- Constraints for table `quarter`
--
ALTER TABLE `quarter`
  ADD CONSTRAINT `quarter_ibfk_1` FOREIGN KEY (`School_Year_ID`) REFERENCES `schoolyear` (`School_Year_ID`) ON DELETE CASCADE;

--
-- Constraints for table `repetition`
--
ALTER TABLE `repetition`
  ADD CONSTRAINT `repetition_ibfk_1` FOREIGN KEY (`School_Year_ID`) REFERENCES `schoolyear` (`School_Year_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `repetition_ibfk_2` FOREIGN KEY (`Grade_ID`) REFERENCES `grade` (`Grade_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `repetition_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`);

--
-- Constraints for table `school_details`
--
ALTER TABLE `school_details`
  ADD CONSTRAINT `school_details_ibfk_1` FOREIGN KEY (`Mobile_ID`) REFERENCES `school_mobile` (`Mobile_ID`),
  ADD CONSTRAINT `school_details_ibfk_2` FOREIGN KEY (`Social_Media_ID`) REFERENCES `social_media` (`Social_Media_ID`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`ContentID`) REFERENCES `feedcontent` (`ContentID`) ON DELETE CASCADE;

--
-- Constraints for table `task_user`
--
ALTER TABLE `task_user`
  ADD CONSTRAINT `task_user_ibfk_1` FOREIGN KEY (`TaskID`) REFERENCES `tasks` (`TaskID`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_user_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_user_ibfk_3` FOREIGN KEY (`ContentID`) REFERENCES `feedcontent` (`ContentID`) ON DELETE CASCADE;

--
-- Constraints for table `templates`
--
ALTER TABLE `templates`
  ADD CONSTRAINT `templates_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`);

--
-- Constraints for table `total_age`
--
ALTER TABLE `total_age`
  ADD CONSTRAINT `total_age_ibfk_1` FOREIGN KEY (`Age_ID`) REFERENCES `age` (`Age_ID`) ON DELETE CASCADE;

--
-- Constraints for table `total_cohort`
--
ALTER TABLE `total_cohort`
  ADD CONSTRAINT `total_cohort_ibfk_1` FOREIGN KEY (`Cohort_ID`) REFERENCES `cohort_survival` (`Cohort_ID`) ON DELETE CASCADE;

--
-- Constraints for table `total_dropout`
--
ALTER TABLE `total_dropout`
  ADD CONSTRAINT `total_dropout_ibfk_1` FOREIGN KEY (`Dropout_ID`) REFERENCES `dropout` (`Dropout_ID`) ON DELETE CASCADE;

--
-- Constraints for table `total_enroll`
--
ALTER TABLE `total_enroll`
  ADD CONSTRAINT `total_enroll_ibfk_1` FOREIGN KEY (`Enroll_ID`) REFERENCES `enroll` (`Enroll_ID`) ON DELETE CASCADE;

--
-- Constraints for table `total_nat`
--
ALTER TABLE `total_nat`
  ADD CONSTRAINT `total_nat_ibfk_1` FOREIGN KEY (`NAT_ID`) REFERENCES `nat` (`NAT_ID`) ON DELETE CASCADE;

--
-- Constraints for table `total_promotion`
--
ALTER TABLE `total_promotion`
  ADD CONSTRAINT `total_promotion_ibfk_1` FOREIGN KEY (`Promotion_ID`) REFERENCES `promotion` (`Promotion_ID`) ON DELETE CASCADE;

--
-- Constraints for table `total_repetition`
--
ALTER TABLE `total_repetition`
  ADD CONSTRAINT `total_repetition_ibfk_1` FOREIGN KEY (`Repetition_ID`) REFERENCES `repetition` (`Repetition_ID`) ON DELETE CASCADE;

--
-- Constraints for table `total_transition`
--
ALTER TABLE `total_transition`
  ADD CONSTRAINT `total_transition_ibfk_1` FOREIGN KEY (`Transition_ID`) REFERENCES `transition` (`Transition_ID`) ON DELETE CASCADE;

--
-- Constraints for table `transition`
--
ALTER TABLE `transition`
  ADD CONSTRAINT `transition_ibfk_1` FOREIGN KEY (`School_Year_ID`) REFERENCES `schoolyear` (`School_Year_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `transition_ibfk_2` FOREIGN KEY (`Grade_ID`) REFERENCES `grade` (`Grade_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `transition_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`);

--
-- Constraints for table `useracc`
--
ALTER TABLE `useracc`
  ADD CONSTRAINT `fk_dept` FOREIGN KEY (`dept_ID`) REFERENCES `department` (`dept_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `usercontent`
--
ALTER TABLE `usercontent`
  ADD CONSTRAINT `usercontent_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `usercontent_ibfk_2` FOREIGN KEY (`ContentID`) REFERENCES `feedcontent` (`ContentID`) ON DELETE CASCADE;

--
-- Constraints for table `userfolders`
--
ALTER TABLE `userfolders`
  ADD CONSTRAINT `userfolders_ibfk_1` FOREIGN KEY (`UserContentID`) REFERENCES `usercontent` (`UserContentID`);

--
-- Constraints for table `usertodo`
--
ALTER TABLE `usertodo`
  ADD CONSTRAINT `usertodo_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `useracc` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `usertodo_ibfk_2` FOREIGN KEY (`TodoID`) REFERENCES `todo` (`TodoID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
