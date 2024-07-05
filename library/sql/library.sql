-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2024 at 06:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `genre` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `publisher` varchar(100) NOT NULL,
  `publication_date` date NOT NULL,
  `description` text NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('Available','Reserved','Not Available','Overdue') DEFAULT 'Available',
  `cover_dir` varchar(255) NOT NULL DEFAULT '../covers/default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `genre`, `category`, `publisher`, `publication_date`, `description`, `isbn`, `date_added`, `last_modified`, `status`, `cover_dir`) VALUES
(1, 'Dune (Chronicles)', 'Frank Herbert', 'Sci-Fi', 'Fiction', 'Penguin Publishing Group', '2005-08-02', 'Set on the desert planet Arrakis, Dune is the story of the boy Paul Atreides, heir to a noble family tasked with ruling an inhospitable world where the only thing of value is the “spice” melange, a drug capable of extending life and enhancing consciousness. Coveted across the known universe, melange is a prize worth killing for...', '0-44-101359-7', '2024-06-29 05:30:56', '2024-06-30 09:16:14', 'Available', '../covers/testss.jpg'),
(2, 'To Kill a Mockingbird', 'Harper Lee', 'Historical', 'Fiction', 'Harper Perennial', '1960-07-07', 'Compassionate, dramatic, and deeply moving, \"To Kill A Mockingbird\" takes readers to the roots of human behavior - to innocence and experience, kindness and cruelty, love and hatred, humor and pathos. Now with over 18 million copies in print and translated into forty languages, this regional story by a young Alabama woman claims universal appeal. Harper Lee always considered her book to be a simple love story. Today it is regarded as a masterpiece of American literature.', '0-06-093546-4', '2024-06-29 05:36:50', '2024-06-30 09:16:22', 'Available', '../covers/2657.jpg'),
(3, 'Physics I For Dummies', 'Steven Holzner', 'Physics', 'Education', 'For Dummies', '2021-03-29', 'The fun and easy way to get up to speed on the basic concepts of physics For high school and undergraduate students alike, physics classes are recommended or required courses for a wide variety of majors, and continue to be a challenging and often confusing course.', '1-11-987222-7', '2024-06-29 05:41:18', '2024-07-01 06:36:42', 'Available', '../covers/9781119872221.jpg'),
(4, 'Code: The Hidden Language of Computers', 'Charles Petzold', 'Com-Sci', 'Education', 'Microsoft Press', '2022-07-08', 'What do flashlights, the British invasion, black cats, and seesaws have to do with computers? In CODE, they show us the ingenious ways we manipulate language and invent new means of communicating with each other. And through CODE, we see how this ingenuity and our very human compulsion to communicate have driven the technological innovations of the past two centuries. ', '0-13-790910-1', '2024-06-29 05:47:48', '2024-07-04 14:53:28', 'Available', '../covers/9780137909100.jpg'),
(5, 'Campbell Biology', 'Jane Reece', 'Biology', 'Education', 'Pearson Higher Education', '2017-01-01', 'Campbell Biology 11th Edition . AP Edition Loose Leaf in Very good condition. Cover is nice and shiny- All pages are clean and unmarked - Biding is nice and tight. All orders ship out in 24 hours All orders are packed in extra-thick bubble mailers All books come with a tracking number to let you know the status of your order.', '0-13-447864-9', '2024-06-29 05:52:15', '2024-07-05 02:41:40', 'Available', '../covers/9780134478647.jpg'),
(18, 'Throne of Glass', 'Sarah Maas', 'Romance', 'Fiction', 'Bloomsbury Publishing', '2023-06-05', 'In a land without magic, where the king rules with an iron hand, an assassin is summoned to the castle. She comes not to kill the king, but to win her freedom. If she defeats twenty-three killers, thieves, and warriors in a competition, she is released from prison to serve as the king\'s champion. Her name is Celaena Sardothien.\r\n\r\nThe Crown Prince will provoke her. The Captain of the Guard will protect her. But something evil dwells in the castle of glass—and it\'s there to kill. When her competitors start dying one by one, Celaena\'s fight for freedom becomes a fight for survival, and a desperate quest to root out the evil before it destroys her world.', '1-63-973095-8', '2024-06-30 08:51:06', '2024-07-05 02:07:26', 'Reserved', '../covers/919YkFdlilL._SL1500_.jpg'),
(19, 'Psychology of Money', 'Morgan Housel', 'Finance', 'Education', 'Harriman House', '2020-09-08', 'Doing well with money isn\'t necessarily about what you know. It\'s about how you behave. And behavior is hard to teach, even to really smart people. Money--investing, personal finance, and business decisions--is typically taught as a math-based field, where data and formulas tell us exactly what to do. But in the real world people don\'t make financial decisions on a spreadsheet. They make them at the dinner table, or in a meeting room, where personal history, your own unique view of the world, ego, pride, marketing, and odd incentives are scrambled together. In The Psychology of Money, award-winning author Morgan Housel shares 19 short stories exploring the strange ways people think about money and teaches you how to make better sense of one of life\'s most important topics.', '0-85-719768-1', '2024-06-30 08:54:05', '2024-06-30 09:17:02', 'Available', '../covers/71aG0m9XRcL._SL1500_.jpg'),
(21, 'The Nightingale', 'Kristin Hannah', 'Romance', 'Fiction', 'St. Martin\'s Griffin', '2017-04-25', 'In the quiet village of Carriveau, Vianne Mauriac says good-bye to her husband, Antoine, as he heads for the Front. She doesn’t believe that the Nazis will invade France…but invade they do, in droves of marching soldiers, in caravans of trucks and tanks, in planes that fill the skies and drop bombs upon the innocent. When a German captain requisitions Vianne’s home, she and her daughter must live with the enemy or lose everything. Without food or money or hope, as danger escalates all around them, she is forced to make one impossible choice after another to keep her family alive.', '1-25-008040-1', '2024-06-30 09:01:35', '2024-07-04 14:45:09', 'Available', '../covers/81OkWjcf4WL._SL1500_.jpg'),
(22, '48 Laws of Power', 'Robert Greene', 'Psychology', 'Non-Fiction', 'Viking', '2023-10-31', 'In the book that People magazine proclaimed “beguiling” and “fascinating,” Robert Greene and Joost Elffers have distilled three thousand years of the history of power into 48 essential laws by drawing from the philosophies of Machiavelli, Sun Tzu, and Carl Von Clausewitz and also from the lives of figures ranging from Henry Kissinger to P.T. Barnum.\r\n\r\nSome laws teach the need for prudence (“Law 1: Never Outshine the Master”), others teach the value of confidence (“Law 28: Enter Action with Boldness”), and many recommend absolute self-preservation (“Law 15: Crush Your Enemy Totally”). Every law, though, has one thing in common: an interest in total domination. In a bold and arresting two-color package, The 48 Laws of Power is ideal whether your aim is conquest, self-defense, or simply to understand the rules of the game.', '0670881465', '2024-06-30 09:07:01', '2024-07-04 14:53:12', 'Available', '../covers/611X8GI7hpL._SL1500_.jpg'),
(23, 'The Art Of War', 'Sun Tzu', 'Historical', 'Non-Fiction', 'Filiquarian', '2007-11-01', 'Twenty-Five Hundred years ago, Sun Tzu wrote this classic book of military strategy based on Chinese warfare and military thought. Since that time, all levels of military have used the teaching on Sun Tzu to warfare and civilization have adapted these teachings for use in politics, business and everyday life. The Art of War is a book which should be used to gain advantage of opponents in the boardroom and battlefield alike.', '1-59-986977-2', '2024-06-30 09:10:46', '2024-07-04 14:53:16', 'Available', '../covers/10534.jpg'),
(24, 'Iron Flame', 'Rebecca Yarros', 'Fantasy', 'Fiction', 'Entangled: Red Tower Books', '2023-07-11', 'Everyone expected Violet Sorrengail to die during her first year at Basgiath War College—Violet included. But Threshing was only the first impossible test meant to weed out the weak-willed, the unworthy, and the unlucky.\r\n\r\nNow the real training begins, and Violet’s already wondering how she’ll get through. It’s not just that it’s grueling and maliciously brutal, or even that it’s designed to stretch the riders’ capacity for pain beyond endurance. It’s the new vice commandant, who’s made it his personal mission to teach Violet exactly how powerless she is–unless she betrays the man she loves.\r\n\r\nAlthough Violet’s body might be weaker and frailer than everyone else’s, she still has her wits—and a will of iron. And leadership is forgetting the most important lesson Basgiath has taught her: Dragon riders make their own rules.', '1-64-937417-8', '2024-06-30 09:13:12', '2024-07-04 14:53:21', 'Available', '../covers/90202302.jpg'),
(25, 'Reckless', 'Lauren Roberts', 'Fantasy', 'Fiction', 'Simon & Schuster Books for Young Readers', '2024-10-11', 'After surviving the Purging Trials, Ordinary-born Paedyn Gray has killed the King, and kickstarted a Resistance throughout the land. Now she’s running from the one person she had wanted to run to.\r\n\r\nKai Azer is now Ilya’s Enforcer, loyal to his brother Kitt, the new King. He has vowed to find Paedyn and bring her to justice.\r\n\r\nAcross the deadly Scorches, and deep into the hostile city of Dor, Kai pursues the one person he wishes he didn’t have to. But in a city without Elites, the balance between the hunter and hunted shifts – and the battle between duty and desire is deadly.\r\n\r\nBe swept away by this kiss-or-kill romantasy trilogy taking the world by storm.', '1-66-595543-0', '2024-06-30 10:29:30', '2024-07-05 02:06:51', 'Reserved', '../covers/81q6ecxcZUL._SL1500_.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reserve_id` int(11) NOT NULL,
  `pickup_date` date NOT NULL,
  `duration` int(11) NOT NULL,
  `return_date` date NOT NULL,
  `status` enum('Pending','Reserved','Picked Up','Overdue','Returned') DEFAULT 'Pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approval_status` enum('Pending','Approved','Denied') NOT NULL DEFAULT 'Pending',
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `return_requested` tinyint(1) DEFAULT 0,
  `return_approved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) NOT NULL DEFAULT '../img/default.jpg',
  `last_online` datetime DEFAULT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('User','Staff','Admin') DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `username`, `email`, `mobile_number`, `password`, `profile_pic`, `last_online`, `creation_date`, `role`) VALUES
(1, 'Administrator Account', 'Admin', 'admin@library.com', '+630000000000', '$2y$10$3gtSsgHUOvLB0475tjSZ2./g2Wb/AglQbz9.4MRd6Ah90w5LOgAaC', '../img/default.jpg', '2024-07-05 11:57:51', '2024-06-29 05:09:16', 'Admin'),
(2, 'Khaz Tuazon', 'leewenct', 'leewenct@gmail.com', '+639275529510', '$2y$10$uKaGj1Yp5LnoXhMPMB2dH.ylunyAw5lx3dr8XcwljuvblJC3Sru8O', '../img/default.jpg', '2024-07-05 10:15:19', '2024-07-01 06:10:50', 'Staff'),
(3, 'Carlven Tapang', 'ctapang', 'ctapang@gmail.com', '+639359010704', '$2y$10$f8iaydO9n8tLos3pPhuQ9.DE/h8hC20gUshpIIWsSmR0cpc4s8iCO', '../img/default.jpg', NULL, '2024-07-01 08:15:24', 'Staff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reserve_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reserve_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
