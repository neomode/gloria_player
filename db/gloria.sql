-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 03, 2018 at 05:18 AM
-- Server version: 5.7.19
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gloria`
--

-- --------------------------------------------------------

--
-- Table structure for table `authentication`
--

DROP TABLE IF EXISTS `authentication`;
CREATE TABLE IF NOT EXISTS `authentication` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `firsname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `authentication`
--

INSERT INTO `authentication` (`username`, `password`, `firsname`, `lastname`, `email`) VALUES
('closari', 'eecd7bba6359228116b73d6e1510f05d', 'Christian', 'Losari', 'cl@zrl.ca'),
('harys', 'c905f0629943b8ba4eea96ab7eb5a66a', 'Harys', 'Wijaya', 'haryswijaya@gmail.com'),
('echaris', '8ae0048825a4f5d6faa917be98bd5b0c', 'Evan', 'Charis', 'charisevan@yahoo.com'),
('samtic', '5ff4ae9ad0229a161c8d262dac3633de', 'Samuel', 'Ticoalu', 'samnding@gmail.com'),
('Pin2', '', 'Pin-Pin', 'Kurniawan', 'Pin2_5777addict@yahoo.com'),
('PinPin', 'c85b2ea9a678e74fdc8bafe5d0707c31', 'PinPin', 'Kurniawan', 'Pin2_5777addict@yahoo.com'),
('rheva', '9dc32d2b541f50c1649f5c47eac9099c', 'rheva', 'adhitiya', 'rhevaaw@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
CREATE TABLE IF NOT EXISTS `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `duration` int(11) NOT NULL,
  `day` varchar(10) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `hour` varchar(6) NOT NULL,
  `datetime` datetime NOT NULL,
  `coordinator` varchar(255) NOT NULL,
  `theme` varchar(255) NOT NULL,
  `speaker` varchar(255) NOT NULL,
  `place` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=604 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `datetime` datetime NOT NULL,
  `activity` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=529 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `media_file`
--

DROP TABLE IF EXISTS `media_file`;
CREATE TABLE IF NOT EXISTS `media_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `src` varchar(255) NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `md5sum` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=300 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `media_file`
--

INSERT INTO `media_file` (`id`, `name`, `src`, `duration`, `md5sum`) VALUES
(299, 'Church Simple Logo', 'player_rsc/video_files/church_simple_logo_20180303044333.mp4', 10, '1a445f0e1f84844c38878405b36164ee');

-- --------------------------------------------------------

--
-- Table structure for table `player`
--

DROP TABLE IF EXISTS `player`;
CREATE TABLE IF NOT EXISTS `player` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `reboot` tinyint(4) NOT NULL,
  `restart` tinyint(4) NOT NULL,
  `sync` tinyint(4) NOT NULL,
  `health` tinyint(4) NOT NULL,
  `last_checked_in` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `player`
--

INSERT INTO `player` (`id`, `name`, `ip`, `reboot`, `restart`, `sync`, `health`, `last_checked_in`) VALUES
(1, 'Main Player', '127.0.0.1', 0, 0, 0, 0, '2016-11-23 13:22:25');

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

DROP TABLE IF EXISTS `playlist`;
CREATE TABLE IF NOT EXISTS `playlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `content` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `playlist`
--

INSERT INTO `playlist` (`id`, `player_id`, `type`, `content`) VALUES
(2, 1, 'media', '299');

-- --------------------------------------------------------

--
-- Table structure for table `verse`
--

DROP TABLE IF EXISTS `verse`;
CREATE TABLE IF NOT EXISTS `verse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `src` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `duration` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `verse`
--

INSERT INTO `verse` (`id`, `title`, `src`, `content`, `duration`) VALUES
(48, 'Bible Verse', 'Roma 8:37', 'Tetapi dalam semuanya itu kita lebih dari pada orang-orang yang menang, oleh Dia yang telah mengasihi kita. ', 8),
(50, 'Bible Verse', '1 Korintus 6:20', 'Sebab kamu telah dibeli dan harganya telah lunas dibayar: Karena itu muliakanlah Allah dengan tubuhmu! ', 8),
(51, 'Bible Verse', 'Efesus 1:7', 'Sebab di dalam Dia dan oleh darah-Nya kita beroleh penebusan, yaitu pengampunan dosa, menurut kekayaan kasih karunia-Nya, ', 8),
(52, 'Bible Verse', 'Matius 10:24', 'Seorang murid tidak lebih dari pada gurunya, atau seorang hamba dari pada tuannya.* ', 5),
(46, 'Bible Verse', 'Mazmur 62:2', ' Hanya Dialah gunung batuku dan keselamatanku, kota bentengku, aku tidak akan goyah. ', 5),
(44, 'Bible Verse', '1 Tesalonika 5:18', 'Mengucap syukurlah dalam segala hal, sebab itulah yang dikehendaki Allah di dalam Kristus Yesus bagi kamu. ', 6),
(45, 'Bible Verse', '1 Tesalonika 1:2', 'Kami selalu mengucap syukur kepada Allah karena kamu semua dan menyebut kamu dalam doa kami. ', 6),
(43, 'Bible Verse', 'Amsal 23:4', 'Jangan bersusah payah untuk menjadi kaya, tinggalkan niatmu ini. ', 5),
(42, 'Bible Verse', 'Matius 4:9', 'dan berkata kepada-Nya: \"Semua itu akan kuberikan kepada-Mu, jika Engkau sujud menyembah aku.\" ', 5),
(41, 'Bible Verse', 'Ulangan 1:34', '\"Ketika TUHAN mendengar gerutumu itu, Ia menjadi murka dan bersumpah: ', 5),
(39, 'Bible Verse', 'Amsal 3:27', 'Janganlah menahan kebaikan dari pada orang-orang yang berhak menerimanya, padahal engkau mampu melakukannya. ', 6),
(40, 'Bible Verse', 'Amsal 3:29', 'Janganlah merencanakan kejahatan terhadap sesamamu, sedangkan tanpa curiga ia tinggal bersama-sama dengan engkau. ', 6),
(38, 'Bible Verse', 'Yohanes 14:1', '\"Janganlah gelisah hatimu; percayalah kepada Allah, percayalah juga kepada-Ku.* ', 5),
(36, 'Bible Verse', 'Yohanes 14:1', '\"Janganlah gelisah hatimu; percayalah kepada Allah, percayalah juga kepada-Ku.* ', 5),
(37, 'Bible Verse', 'Amsal 3:27', 'Janganlah menahan kebaikan dari pada orang-orang yang berhak menerimanya, padahal engkau mampu melakukannya. ', 5),
(53, 'Bible Verse', 'Matius 10:24', 'Seorang murid tidak lebih dari pada gurunya, atau seorang hamba dari pada tuannya.* ', 5),
(55, 'Bible Verse', 'Filipi 2:6', 'yang walaupun dalam rupa Allah, tidak menganggap kesetaraan dengan Allah itu sebagai milik yang harus dipertahankan, ', 8),
(58, 'Bible Verse', 'Filipi 4:13', 'Segala perkara dapat kutanggung di dalam Dia yang memberi kekuatan kepadaku. ', 8),
(59, 'Bible Verse', 'Kejadian 15:1', 'Kemudian datanglah firman TUHAN kepada Abram dalam suatu penglihatan: \"Janganlah takut, Abram, Akulah perisaimu; upahmu akan sangat besar.\" ', 8),
(60, 'Bible Verse', 'Keluaran 1:7', 'Orang-orang Israel beranak cucu dan tak terbilang jumlahnya; mereka bertambah banyak dan dengan dahsyat berlipat ganda, sehingga negeri itu dipenuhi mereka. ', 10),
(62, 'Bible Verse', '1 Samuel 12:24', 'Hanya takutlah akan TUHAN dan setialah beribadah kepada-Nya dengan segenap hatimu, sebab ketahuilah, betapa besarnya hal-hal yang dilakukan-Nya di antara kamu. ', 10),
(63, 'Bible Verse', '1 Petrus 2:2', 'Dan jadilah sama seperti bayi yang baru lahir, yang selalu ingin akan air susu yang murni dan yang rohani, supaya olehnya kamu bertumbuh dan beroleh keselamatan', 8);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
