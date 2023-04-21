-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2023. Ápr 21. 14:27
-- Kiszolgáló verziója: 10.4.25-MariaDB
-- PHP verzió: 8.1.10

SET FOREIGN_KEY_CHECKS=0;
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

DROP TABLE IF EXISTS `brands`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `categories_to_brands`
--

DROP TABLE IF EXISTS `categories_to_brands`;
CREATE TABLE IF NOT EXISTS `categories_to_brands` (
  `brand_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  KEY `brand_id` (`brand_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `categories_to_ingredients`
--

DROP TABLE IF EXISTS `categories_to_ingredients`;
CREATE TABLE IF NOT EXISTS `categories_to_ingredients` (
  `ingredient_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  KEY `ingredient_id` (`ingredient_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `categories_to_products`
--

DROP TABLE IF EXISTS `categories_to_products`;
CREATE TABLE IF NOT EXISTS `categories_to_products` (
  `product_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  KEY `product_id` (`product_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ingredients`
--

DROP TABLE IF EXISTS `ingredients`;
CREATE TABLE IF NOT EXISTS `ingredients` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `ewg_risk` int(11) NOT NULL,
  `comedogen_index` int(11) NOT NULL,
  `irritation_index` int(11) NOT NULL,
  `image_file` longblob DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ingredients_to_ingredient_functions`
--

DROP TABLE IF EXISTS `ingredients_to_ingredient_functions`;
CREATE TABLE IF NOT EXISTS `ingredients_to_ingredient_functions` (
  `ingredient_id` bigint(20) NOT NULL,
  `ingredient_function_id` bigint(20) NOT NULL,
  KEY `ingredient_id` (`ingredient_id`),
  KEY `ingredient_function_id` (`ingredient_function_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ingredient_functions`
--

DROP TABLE IF EXISTS `ingredient_functions`;
CREATE TABLE IF NOT EXISTS `ingredient_functions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `price_categories`
--

DROP TABLE IF EXISTS `price_categories`;
CREATE TABLE IF NOT EXISTS `price_categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products`
--

DROP TABLE IF EXISTS `products`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products_to_ingredients`
--

DROP TABLE IF EXISTS `products_to_ingredients`;
CREATE TABLE IF NOT EXISTS `products_to_ingredients` (
  `product_id` bigint(20) NOT NULL,
  `ingredient_id` bigint(20) NOT NULL,
  KEY `product_id` (`product_id`),
  KEY `ingridient_id` (`ingredient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products_to_subcategories`
--

DROP TABLE IF EXISTS `products_to_subcategories`;
CREATE TABLE IF NOT EXISTS `products_to_subcategories` (
  `subcategory_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  KEY `product_category_id` (`subcategory_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ratings`
--

DROP TABLE IF EXISTS `ratings`;
CREATE TABLE IF NOT EXISTS `ratings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `rating` int(5) NOT NULL,
  `comment` longtext NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rating` (`user_id`,`product_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `subcategories`
--

DROP TABLE IF EXISTS `subcategories`;
CREATE TABLE IF NOT EXISTS `subcategories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
