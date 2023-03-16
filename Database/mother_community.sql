SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `mother_community` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mother_community`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id`, `name`, `image_path`) VALUES
(1, 'SZÉPSÉGÁPOLÁS', 'assets/card_szepsegapolas.jpg'),
(2, 'HÁZTARTÁS', 'assets/card_haztartas.jpg'),
(3, 'BIO TERMÉKEK', 'assets/card_bio_termekek.jpg'),
(4, 'ÉTREND-KIEGÉSZÍTŐK', 'assets/card_etrend_kiegeszitok.jpg');

DROP TABLE IF EXISTS `categories_to_brands`;
CREATE TABLE IF NOT EXISTS `categories_to_brands` (
  `brand_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  KEY `brand_id` (`brand_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `categories_to_ingredients`;
CREATE TABLE IF NOT EXISTS `categories_to_ingredients` (
  `ingredient_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  KEY `ingredient_id` (`ingredient_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `categories_to_products`;
CREATE TABLE IF NOT EXISTS `categories_to_products` (
  `product_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  KEY `product_id` (`product_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `ingredients`;
CREATE TABLE IF NOT EXISTS `ingredients` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `ewg_risk` int(11) NOT NULL,
  `comedogen_index` int(11) NOT NULL,
  `irritation_index` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `ingredients_to_ingredient_functions`;
CREATE TABLE IF NOT EXISTS `ingredients_to_ingredient_functions` (
  `ingredient_id` bigint(20) NOT NULL,
  `ingredient_function_id` bigint(20) NOT NULL,
  KEY `ingredient_id` (`ingredient_id`),
  KEY `ingredient_function_id` (`ingredient_function_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `ingredient_functions`;
CREATE TABLE IF NOT EXISTS `ingredient_functions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ingredient_function` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `price_categories`;
CREATE TABLE IF NOT EXISTS `price_categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `price_categories` (`id`, `name`) VALUES
(1, 'ALACSONY'),
(2, 'KÖZEPES'),
(3, 'MAGAS');

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price_range_min` varchar(255) NOT NULL,
  `price_range_max` varchar(255) NOT NULL,
  `packaging` varchar(255) NOT NULL,
  `can_help` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `products_to_ingredients`;
CREATE TABLE IF NOT EXISTS `products_to_ingredients` (
  `product_id` bigint(20) NOT NULL,
  `ingridient_id` bigint(20) NOT NULL,
  KEY `product_id` (`product_id`),
  KEY `ingridient_id` (`ingridient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `products_to_product_categories`;
CREATE TABLE IF NOT EXISTS `products_to_product_categories` (
  `product_category_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  KEY `product_category_id` (`product_category_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE IF NOT EXISTS `product_categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_category_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `ratings`;
CREATE TABLE IF NOT EXISTS `ratings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `rating` int(5) NOT NULL,
  `comment` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'Mekk Elek', 'Mekmek', 'Admin');


ALTER TABLE `brands`
  ADD CONSTRAINT `brands_ibfk_1` FOREIGN KEY (`price_category_id`) REFERENCES `price_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `categories_to_brands`
  ADD CONSTRAINT `categories_to_brands_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categories_to_brands_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `categories_to_ingredients`
  ADD CONSTRAINT `categories_to_ingredients_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categories_to_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `categories_to_products`
  ADD CONSTRAINT `categories_to_products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categories_to_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ingredients_to_ingredient_functions`
  ADD CONSTRAINT `ingredients_to_ingredient_functions_ibfk_1` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ingredients_to_ingredient_functions_ibfk_2` FOREIGN KEY (`ingredient_function_id`) REFERENCES `ingredient_functions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `products_to_ingredients`
  ADD CONSTRAINT `products_to_ingredients_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `products_to_ingredients_ibfk_2` FOREIGN KEY (`ingridient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `products_to_product_categories`
  ADD CONSTRAINT `products_to_product_categories_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `products_to_product_categories_ibfk_2` FOREIGN KEY (`product_category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
