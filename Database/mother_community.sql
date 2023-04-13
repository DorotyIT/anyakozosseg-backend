-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2023. Ápr 13. 18:22
-- Kiszolgáló verziója: 10.4.27-MariaDB
-- PHP verzió: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `mother_community`
--
CREATE DATABASE IF NOT EXISTS `mother_community` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mother_community`;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `brands`
--

CREATE TABLE IF NOT EXISTS `brands` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `is_cruelty_free` tinyint(1) NOT NULL,
  `is_vegan` tinyint(1) NOT NULL,
  `overall_rating` tinyint(5) NOT NULL,
  `image_file` longblob NOT NULL,
  `price_category_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `price_category_id` (`price_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `categories`
--

INSERT INTO `categories` (`id`, `name`, `image_path`) VALUES
(1, 'SZÉPSÉGÁPOLÁS', 'assets/img/categories/card_szepsegapolas.jpg'),
(2, 'HÁZTARTÁS', 'assets/img/categories/card_haztartas.jpg'),
(3, 'BIO TERMÉKEK', 'assets/img/categories/card_bio_termekek.jpg'),
(4, 'ÉTREND-KIEGÉSZÍTŐK', 'assets/img/categories/card_etrend_kiegeszitok.jpg');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `categories_to_brands`
--

CREATE TABLE IF NOT EXISTS `categories_to_brands` (
  `brand_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  KEY `brand_id` (`brand_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `categories_to_ingredients`
--

CREATE TABLE IF NOT EXISTS `categories_to_ingredients` (
  `ingredient_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  KEY `ingredient_id` (`ingredient_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `categories_to_products`
--

CREATE TABLE IF NOT EXISTS `categories_to_products` (
  `product_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  KEY `product_id` (`product_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ingredients`
--

CREATE TABLE IF NOT EXISTS `ingredients` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `ewg_risk` int(11) NOT NULL,
  `comedogen_index` int(11) NOT NULL,
  `irritation_index` int(11) NOT NULL,
  `image_file` longblob DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ingredients_to_ingredient_functions`
--

CREATE TABLE IF NOT EXISTS `ingredients_to_ingredient_functions` (
  `ingredient_id` bigint(20) NOT NULL,
  `ingredient_function_id` bigint(20) NOT NULL,
  KEY `ingredient_id` (`ingredient_id`),
  KEY `ingredient_function_id` (`ingredient_function_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ingredient_functions`
--

CREATE TABLE IF NOT EXISTS `ingredient_functions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `ingredient_functions`
--

INSERT INTO `ingredient_functions` (`id`, `name`) VALUES
(1, 'Hidratáló'),
(2, 'Bőrpuhító'),
(3, 'Antioxidáns'),
(4, 'Fényvédő'),
(5, 'Gyulladáscsökkentő'),
(6, 'Hámlasztó'),
(7, 'Bőrmegújító'),
(8, 'Bőrfeszesítő'),
(9, 'Hőszabályozó'),
(10, 'Antibakteriális'),
(11, 'Tisztító'),
(12, 'Habzó'),
(13, 'Nedvesítő'),
(14, 'pH szabályozó'),
(15, 'Szagtalanító'),
(16, 'Optikai fehérítő'),
(17, 'Szövetpuhító'),
(18, 'Zsíroldó'),
(19, 'Vízlágyító'),
(20, 'Vízkövesedés eltávolító'),
(21, 'Fertőtlenítő'),
(22, 'Illatosító'),
(23, 'Elektrosztatikus feltöltés gátló'),
(24, 'Fényesítő'),
(25, 'Rovarölő'),
(26, 'Tápláló'),
(27, 'Természetes színezék'),
(28, 'Illatanyag'),
(29, 'Exfoliáló'),
(30, 'Szövetregeneráló'),
(31, 'Hajerősítő'),
(32, 'Hajnövekedést serkentő'),
(33, 'Napvédelem'),
(34, 'Ráncmegelőző'),
(35, 'Pigmentfoltok elleni küzdelem'),
(36, 'Sejtmegújító'),
(37, 'Immunrendszer támogató'),
(38, 'Emésztést segítő'),
(39, 'Máj támogató'),
(40, 'Vese támogató'),
(41, 'Szív- és érrendszer támogató'),
(42, 'Cukorbetegség támogató'),
(43, 'Csont- és ízület támogató'),
(44, 'Stressz-csökkentő'),
(45, 'Alvást segítő'),
(46, 'Szexuális teljesítményt fokozó'),
(47, 'Étvágycsökkentő'),
(48, 'Étvágyserkentő'),
(49, 'Sportteljesítményt fokozó'),
(50, 'Fogyasztást segítő'),
(51, 'Depresszió elleni küzdelem'),
(52, 'Memória javító'),
(53, 'Mozgásszervi fájdalmak elleni küzdelem'),
(54, 'Allergia elleni küzdelem');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `price_categories`
--

CREATE TABLE IF NOT EXISTS `price_categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `price_categories`
--

INSERT INTO `price_categories` (`id`, `name`) VALUES
(1, 'ALACSONY'),
(2, 'KÖZEPES'),
(3, 'MAGAS'),
(4, 'PRÉMIUM');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price_range_min` int(255) NOT NULL,
  `price_range_max` int(255) NOT NULL,
  `packaging` varchar(255) NOT NULL,
  `can_help` varchar(255) NOT NULL,
  `image_file` longblob DEFAULT NULL,
  `brand_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products_to_ingredients`
--

CREATE TABLE IF NOT EXISTS `products_to_ingredients` (
  `product_id` bigint(20) NOT NULL,
  `ingredient_id` bigint(20) NOT NULL,
  KEY `product_id` (`product_id`),
  KEY `ingridient_id` (`ingredient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products_to_subcategories`
--

CREATE TABLE IF NOT EXISTS `products_to_subcategories` (
  `subcategory_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  KEY `product_category_id` (`subcategory_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ratings`
--

CREATE TABLE IF NOT EXISTS `ratings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `rating` int(5) NOT NULL,
  `comment` longtext NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `subcategories`
--

CREATE TABLE IF NOT EXISTS `subcategories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `subcategories`
--

INSERT INTO `subcategories` (`id`, `name`, `category_id`) VALUES
(1, 'Arcápolás', 1),
(2, 'Testápolás', 1),
(3, 'Hajápolás', 1),
(4, 'Smink', 1),
(5, 'Körömlakkok', 1),
(6, 'Parfümök', 1),
(7, 'Napozás', 1),
(8, 'Wellness', 1),
(9, 'Ajakápolás', 1),
(10, 'Szemápolás', 1),
(11, 'Takarítószerek', 2),
(12, 'Tárolóedények', 2),
(13, 'Konyhai eszközök', 2),
(14, 'Fürdőszobai kiegészítők', 2),
(15, 'Zöldségek és Gyümölcsök', 3),
(16, 'Gabonafélék és Lisztek', 3),
(17, 'Hal- és Húskészítmények', 3),
(18, 'Tejtermékek', 3),
(19, 'Cukrászsütemények és Desszertek', 3),
(20, 'Italok', 3),
(21, 'Élelmiszer-Adalékok', 3),
(22, 'Vitaminok', 4),
(23, 'Ásványi anyagok', 4),
(24, 'Fehérjék', 4),
(25, 'Zsírsavak', 4),
(26, 'Szénhidrátok', 4),
(27, 'Probiotikumok', 4),
(28, 'Egyéb kiegészítők', 4);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'Admin', 'admin', 'admin'),
(2, 'User', 'user', 'user');

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `brands`
--
ALTER TABLE `brands`
  ADD CONSTRAINT `brands_ibfk_1` FOREIGN KEY (`price_category_id`) REFERENCES `price_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `categories_to_brands`
--
ALTER TABLE `categories_to_brands`
  ADD CONSTRAINT `categories_to_brands_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categories_to_brands_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `categories_to_ingredients`
--
ALTER TABLE `categories_to_ingredients`
  ADD CONSTRAINT `categories_to_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categories_to_ingredients_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `categories_to_products`
--
ALTER TABLE `categories_to_products`
  ADD CONSTRAINT `categories_to_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categories_to_products_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `ingredients_to_ingredient_functions`
--
ALTER TABLE `ingredients_to_ingredient_functions`
  ADD CONSTRAINT `ingredients_to_ingredient_functions_ibfk_1` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ingredients_to_ingredient_functions_ibfk_2` FOREIGN KEY (`ingredient_function_id`) REFERENCES `ingredient_functions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `products_to_ingredients`
--
ALTER TABLE `products_to_ingredients`
  ADD CONSTRAINT `products_to_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `products_to_ingredients_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `products_to_subcategories`
--
ALTER TABLE `products_to_subcategories`
  ADD CONSTRAINT `products_to_subcategories_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `products_to_subcategories_ibfk_2` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
