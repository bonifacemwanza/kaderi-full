
-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: May 30, 2021 at 09:40 PM
-- Server version: 5.7.26
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `kaderi`
--

-- --------------------------------------------------------

--
-- Table structure for table `book_store`
--

CREATE TABLE `book_store` (
  `id` int(11) NOT NULL,
  `book_name` varchar(250) NOT NULL,
  `book_description` text,
  `book_cover` varchar(250) NOT NULL,
  `price` varchar(11) NOT NULL,
  `points` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `book_store`
--

INSERT INTO `book_store` (`id`, `book_name`, `book_description`, `book_cover`, `price`, `points`) VALUES
(1, 'Kader İle Seçim Arasındaki Savaş', '&lt;p class=&quot;p1&quot;&gt; &lt;/p&gt; &lt;br&gt;&lt;p class=&quot;p2&quot;&gt;Nereden geldik? Nereye gidiyoruz? Neden dünyamız bu kadar tehlikeli? Her zaman böyle miydi? Bir gün daha iyi olacak mı? İnsanın yaratılışından beri bu sorular insanlığın deneyiminin merkezinde yer almıştır. Din önderleri\r\n', 'upload/photos/2020/12/GtmLdRHaLrtuTAVveIfp_23_d14a9cdc9304eeb96cab63d747f46eed_image.jpg', '11.99', 20);

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `user_one` int(11) NOT NULL DEFAULT '0',
  `user_two` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `user_one`, `user_two`, `time`) VALUES
(1, 8, 9, 1598017554);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL DEFAULT '0',
  `to_id` int(11) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `seen` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `from_deleted` int(11) NOT NULL DEFAULT '0',
  `to_deleted` int(11) NOT NULL DEFAULT '0',
  `sent_push` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `notification_id` varchar(50) NOT NULL DEFAULT '',
  `type_two` varchar(32) NOT NULL DEFAULT '',
  `media` varchar(255) CHARACTER SET utf16 NOT NULL,
  `mediaFileName` varchar(200) CHARACTER SET utf16 NOT NULL,
  `mediaFileNames` varchar(200) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `messages`
--
INSERT INTO `langs` (`id`, `lang_key`, `english`, `arabic`, `dutch`, `french`, `german`, `russian`, `spanish`, `turkish`) VALUES
(NULL, 'year', 'year', 'عام', 'jaar', 'année', 'Jahr', 'год', 'año', 'yıl'),
(NULL, 'month', 'month', 'شهر', 'maand', 'mois', 'Monat', 'месяц', 'mes', 'ay'),
(NULL, 'day', 'day', 'يوم', 'dag', 'journée', 'Tag', 'день', 'día', 'gün'),
(NULL, 'hour', 'hour', 'ساعة', 'uur', 'heure', 'Stunde', 'час', 'hora', 'saat'),
(NULL, 'minute', 'minute', 'اللحظة', 'minuut', 'minute', 'Minute', 'минут', 'minuto', 'dakika'),
(NULL, 'second', 'second', 'ثانيا', 'tweede', 'seconde', 'zweite', 'второй', 'segundo', 'ikinci'),
(NULL, 'years', 'years', 'سنوات', 'jaar', 'années', 'Jahre', 'лет', 'años', 'yıl'),
(NULL, 'months', 'months', 'الشهور', 'maanden', 'mois', 'Monate', 'месяцы', 'meses', 'ay'),
(NULL, 'days', 'days', 'أيام', 'dagen', 'journées', 'Tage', 'дней', 'dias', 'günler'),
(NULL, 'hours', 'hours', 'ساعات', 'uur', 'heures', 'Std', 'часов', 'horas', 'saatler'),
(NULL, 'minutes', 'minutes', 'الدقائق', 'notulen', 'minutes', 'Protokoll', 'минут', 'minutos', 'dakika'),
(NULL, 'seconds', 'seconds', 'ثواني', 'seconden', 'secondes', 'Sekunden', 'секунд', 'segundos', 'saniye'),
(NULL, 'time_ago', 'ago', 'منذ', 'geleden', 'depuis', 'vor', 'тому назад', 'hace', 'önce');

INSERT INTO `messages` (`id`, `from_id`, `to_id`, `text`, `seen`, `time`, `from_deleted`, `to_deleted`, `sent_push`, `notification_id`, `type_two`, `media`, `mediaFileName`, `mediaFileNames`) VALUES
(1, 8, 9, 'ice Chibala', 1597690395, 1597678347, 0, 0, 0, '', '', '', '', NULL),
(2, 9, 8, 'sds', 1597690395, 1597688459, 0, 0, 0, '', '', '', '', NULL),
(3, 8, 9, 'sds', 1597690395, 1597688462, 0, 0, 0, '', '', '', '', NULL);

--
-- Indexes for dumped tables
--
INSERT INTO `config` (`id`, `name`, `value`) VALUES
(NULL, 'server', 'ajax');
--
-- Indexes for table `book_store`
--
ALTER TABLE `book_store`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_one` (`user_one`),
  ADD KEY `user_two` (`user_two`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_id` (`from_id`),
  ADD KEY `to_id` (`to_id`),
  ADD KEY `seen` (`seen`),
  ADD KEY `time` (`time`),
  ADD KEY `from_deleted` (`from_deleted`),
  ADD KEY `to_deleted` (`to_deleted`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book_store`
--
ALTER TABLE `book_store`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
