-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2024 at 07:33 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `konseria`
--

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `time` varchar(50) NOT NULL,
  `location` varchar(255) NOT NULL,
  `organizer` varchar(255) NOT NULL,
  `organizerImage` varchar(255) NOT NULL,
  `imageURL` varchar(255) NOT NULL,
  `ticketPrice` int(30) NOT NULL,
  `availableTickets` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id`, `title`, `description`, `date`, `time`, `location`, `organizer`, `organizerImage`, `imageURL`, `ticketPrice`, `availableTickets`) VALUES
(4, 'Seismic', 'event eyf', '2024-12-07', '19.00 - 20.00', 'Hetero Space, Purwokerto', 'Bem FT', 'https://artatix.co.id/img/user_profile/Logo-BEM-FT.png\r\n', 'https://artatix.co.id/img/event_banner/67030706b4625-ARTATIXBANNERTICKETBERMUSIKDIDESEMBER.jpg', 85000, 69),
(5, 'MAKNAFESTIVA', 'Maknafestiva Chapter 3 adalah momen perayaan untuk siapapun yang telah bersuka cita selama 2024. Januari hingga Desember adalah waktu yang tak dapat di ulang, tetapi penuh dengan cerita yang akan selalu terkenang. Untuk siapapun yang merayakan, sampai bertemu di Maknafestiva Chapter 3!', '2024-11-16', '16:00 - 22:00', 'Pekanbaru', 'Major Creator', 'https://artatix.co.id/img/user_profile/avatar.jpeg', 'https://artatix.co.id/img/event_banner/13028851-scriptbannerotello.png', 89000, 996),
(6, 'Pesta Bergoyang', 'Siap goyang sampe gobyos dan lupain penat sejenak?\n\nPesta Bergoyang hadir untuk arek-arek Suroboyo yang mau seru-seruan bareng! Yuk nyanyi bareng Setiaband, ngambyar bareng Guyon Waton, joget nonstop bareng Lala Widy, dan masih banyak lagi yang seru-seru\n\nPokoknya mulai dari lagu nostalgia hingga hits hari ini akan dibawain di tanggal 23 November 2024, di Lapangan Parkir Plaza Surabaya', '2024-11-14', '15:00 - 23:00', 'Plaza Surabaya', 'IMADI MEDIA KREASI', 'https://artatix.co.id/img/user_profile/1080x1080_Logo%20Imadi.png', 'https://artatix.co.id/img/event_banner/905204845-PERGOY3-WEB%20ARTATIX.jpg', 65000, 96),
(7, 'On Fest 2024', 'On Fest adalah event tahunan yang diselenggarakan oleh Vape On, di tahun 2024 ini On Fest berlangsung 2 hari yaitu pada tanggal Sabtu, 30 November dan Minggu, 1 Desember yang berlokasi di Surabaya Convention Center PTC Surabaya.\nCross Culture Music Festival adalah tema yang diambil On Fest pada tahun ini dengan melibatkan banyak genre, scene, dan culture didalamnya. Selain Music Festival, On Fest juga merupakan Vape Expo yang menghadirkan banyak brand baik nasional ataupun internasional.\nYuk rayakan bersama di On Fest 2024!!', '2024-11-21', '6:30 - 23:30', 'Surabaya Convention Center', 'Bang Ipul', 'https://artatix.co.id/img/user_profile/PP%20On%20Fest.jpg', 'https://artatix.co.id/img/event_banner/670cf3612cec0-BannerArtatixUPDATE.jpg', 50000, 483),
(8, 'Paramuda Festival', 'Mengambil tema nostalgia berbagai generasi, Paramuda Vol 6 diharapkan menjadi konsep yang akan membuat nostalgia berbagai generasi mulai dari anak anak, remaja, dewasa, dan bagi siapapun yang selalu berjiwa muda. \n\nParamuda adalah acara rutin dilakukan setiap tahun nya, yang dilaksanakan diberbagai kota diantaranya, Manado, Lamongan, Pasuruan, Surabaya, Sidoarjo,dan kali ini kota yang terpilih adalah Bogor dengan membawa konsep wahana, konser, dan pameran didalam satu acara. Bertujuan agar pengunjung dapat menikmati berbagai kegiatan dalam acara Paramuda Festival.', '2024-11-20', '16:30 - 23:30', 'Vivo Mall Bogor', 'Paramuda Fest Indonesia', 'https://artatix.co.id/img/user_profile/Paramuda%20Festival%20Logo.png', 'https://artatix.co.id/img/event_banner/905781470-resize%20new(1).jpg', 65000, 0),
(9, 'Law Music Project 2024', 'Law Music Project adalah festival musik yang diadakan oleh Fakultas Hukum Universitas Airlangga.  Acara ini bakal seru banget dan juga akan ada penampilan dari artis-artis yang tentunya keren dan top yang siap menghibur kamu. Pas banget nih, kamu bisa nikmatin keseruan dari festival musik kamu dengan teman-teman kamu atau juga pasanganmu.\n\nJadi, jangan lewatkan keseruan ini! Tiket dapat dibeli di Artatix. Pastikan kamu hadir dan have fun bareng, amankan tiketmu sekarang!', '2024-12-20', '6:30 - 23:30', 'Balai Pemuda Surabaya', 'Law Music Project', 'https://artatix.co.id/img/user_profile/S__13451443.jpg', 'https://artatix.co.id/img/event_banner/670cca02b0d66-Designticketartatix.png', 75000, 8),
(29, 'Stereo 2024', 'STEREO adalah sebuah ajang pentas musik eksternal ber-profit dalam bentuk pop-up market dan konser musik yang diadakan oleh Kelompok Studi Mahasiswa Radio CRAST 107,8 FM divisi off air yang terkonsentrasi pada event. STEREO 2024 mengusung tema “Metamorfosa”. yang dapat diartikan sebagai suatu proses yang erat kaitannya dengan perubahan untuk menuju suatu bentuk baru yang berupa penyempurnaan dari bentuk sebelumnya. Dalam stereo 2024, proses inilah yang kami sebut sebagai \"perjalanan\".Harapannya, perjalanan dari stereo 2024 akan dilakukan terus menerus untuk menuju penyempurnaan yang diibaratkan sebagai \"kebahagiaan\". STEREO 2024 ini juga diharapkan dapat menjadi sebagai salah satu wadah bagi masyarakat luas khususnya pada kota Jogja, yang dimana nantinya tidak hanya akan mendapatkan hiburan saja, namun juga bisa mendapatkan edukasi dengan cara yang berkesan. STEREO 2024 kali ini membawakan tagline “A Journey To Happiness!” yang dimana tagline tersebut memiliki makna STEREO 2024 sebagai salah satu bentuk perjalanan untuk menemukan sebuah “kebahagian”.', '2024-11-29', '19:00 - 23:00', 'The Ratan', 'CRAST EVENT', 'https://artatix.co.id/img/user_profile/crast%20event.png', 'https://artatix.co.id/img/event_banner/67177b9605f68-16F657E27B864253B2760961E5E54B71.png', 65000, 278);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `total` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `item_id` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Gilang', 'Gilang@gmail.com', '$2y$10$kFB3K6FUf/QB8B7.1w2t8Oiqj1nAbcj3QKabCOcPPFgmebyeFX/LK', '2024-11-19 06:40:11'),
(4, 'gilang.hy', 'gilang@gam.xa', '$2y$10$GGBB/CGJ5ywnd8pyRShVwOl2XtOz0pIgpf8wTmao5Ab0sLT7ds7OK', '2024-11-19 06:41:13'),
(5, 'gaub', 'adwa@kak.aca', '$2y$10$U9nruiHfTFJD309pubiCtu7lt3OuEuSqZotpRtDCRH1nf7/vf0myi', '2024-11-19 06:50:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
