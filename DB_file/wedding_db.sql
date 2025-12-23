-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2025 at 10:25 PM
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
-- Database: `wedding_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `booking_date` date NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `spent_amount` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budget_categories`
--

CREATE TABLE `budget_categories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `budget_amount` decimal(10,2) DEFAULT 0.00,
  `color` varchar(20) DEFAULT '#91AC8F',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budget_expenses`
--

CREATE TABLE `budget_expenses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','overdue') DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budget_items`
--

CREATE TABLE `budget_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `estimated_cost` decimal(10,2) DEFAULT 0.00,
  `actual_cost` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cakes`
--

CREATE TABLE `cakes` (
  `id` int(11) NOT NULL,
  `package_type` enum('luxury','regular','medium') NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT 'Cakes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cakes`
--

INSERT INTO `cakes` (`id`, `package_type`, `name`, `description`, `image_url`, `category`) VALUES
(1, 'regular', 'Lemon Poppy Seed Cake', 'Light and refreshing with a citrus twist.', 'imgs/Lemon Poppy Seed Cake.jpg', 'Cakes'),
(2, 'regular', 'Orange Poppy Seed Cake with Mascarpone Frosting', 'Elegant orange cake with creamy frosting.', 'imgs/Orange Poppy Seed Cake with Mascarpone Frosting.jpg', 'Cakes'),
(3, 'regular', 'Strawberry Shortcake Cake with Mascarpone Cream', 'Rustic cake with fresh strawberries and cream.', 'imgs/Strawberry Shortcake Cake with Mascarpone Cream.jpg', 'Cakes'),
(4, 'regular', 'Cinnamon Toast Crunch Cake', 'Warm cinnamon flavor with a crunchy twist.', 'imgs/Cinnamon Toast Crunch Cake.jpg', 'Cakes'),
(5, 'regular', 'Pina Colada Cake', 'Tropical pineapple and coconut delight.', 'imgs/Pina Colada Cake.jpg', 'Cakes'),
(6, 'regular', 'Cranberry Orange Cake', 'Seasonal and refreshing citrus blend.', 'imgs/Cranberry Orange Cake.jpg', 'Cakes'),
(7, 'regular', 'Eggnog Cake', 'Festive winter cake with eggnog flavor.', 'imgs/Eggnog Cake.jpg', 'Cakes'),
(8, 'regular', 'Blueberry Shortcake Cake', 'Fresh blueberry layers with a shortcake base.', 'imgs/Blueberry Shortcake Cake.jpg', 'Cakes'),
(9, 'regular', 'Cinnamon Roll Cake', 'Warm cinnamon roll flavor in cake form.', 'imgs/Cinnamon Roll Cake.jpg', 'Cakes'),
(10, 'regular', 'Banana Pudding Cake', 'Classic banana pudding in cake layers.', 'imgs/Banana Pudding Cake.jpg', 'Cakes'),
(11, 'regular', 'Milk & Cookies Cake', 'Cookie dough and milk flavor combo.', 'imgs/Milk & Cookies Cake.jpg', 'Cakes'),
(12, 'regular', 'Poppy Seed Cake', 'Light cake with poppy seed texture.', 'imgs/Poppy Seed Cake.jpg', 'Cakes'),
(13, 'regular', 'Chocolate Chip Cookie Cake', 'Soft cookie dough with chocolate chips.', 'imgs/Chocolate Chip Cookie Cake.jpg', 'Cakes'),
(14, 'regular', 'Water', 'Pure and refreshing hydration.', 'imgs/water (2).jpg', 'Drinks'),
(15, 'regular', 'Iced Latte', 'Smooth espresso with chilled milk.', 'imgs/icedLate.png', 'Drinks'),
(16, 'regular', 'Iced Tea', 'Crisp and refreshing tea infusion.', 'imgs/icedTea.png', 'Drinks'),
(17, 'medium', 'Coffee & Baileys Cake', 'Rich coffee cake with a Baileys kick.', 'imgs/Coffee & Baileys Cake.jpg', 'Cakes'),
(18, 'medium', 'White Chocolate Candy Cane Cake', 'Decadent white chocolate with candy cane.', 'imgs/White Chocolate Candy Cane Cake.jpg', 'Cakes'),
(19, 'medium', 'Eggnog Latte Cake', 'A festive blend of eggnog and latte flavors.', 'imgs/Eggnog Latte Cake.jpg', 'Cakes'),
(20, 'medium', 'Pear & Walnut Cake with Honey Buttercream', 'A fall-inspired cake with honey buttercream.', 'imgs/Pear & Walnut Cake with Honey Buttercream.jpg', 'Cakes'),
(21, 'medium', 'Pecan Pie Cake', 'Rich pecan pie filling in cake layers.', 'imgs/Pecan Pie Cake.jpg', 'Cakes'),
(22, 'medium', 'Biscoff Cake (Cookie Butter Cake)', 'Elegant Biscoff cake displayed on a stand.', 'imgs/Biscoff Cake (Cookie Butter Cake).jpg', 'Cakes'),
(23, 'medium', 'Vanilla Latte Cake', 'Smooth vanilla with a coffee latte twist.', 'imgs/Vanilla Latte Cake.jpg', 'Cakes'),
(24, 'medium', 'White Chocolate Mocha Cake', 'Creamy white chocolate with mocha notes.', 'imgs/White Chocolate Mocha Cake.jpg', 'Cakes'),
(25, 'medium', 'White Chocolate Cake', 'Decadent white chocolate in layers and drip.', 'imgs/White Chocolate Cake.jpg', 'Cakes'),
(26, 'medium', 'Bakewell Cake (Raspberry Almond Cake)', 'Raspberry and almond in a traditional style.', 'imgs/Bakewell Cake (Raspberry Almond Cake).jpg', 'Cakes'),
(27, 'medium', 'Lime & Coconut Cake', 'Zesty lime paired with coconut layers.', 'imgs/Lime & Coconut Cake.jpg', 'Cakes'),
(28, 'medium', 'Chai Cake with Cream Cheese Frosting', 'Spiced chai with creamy frosting.', 'imgs/Chai Cake with Cream Cheese Frosting.jpg', 'Cakes'),
(29, 'medium', 'Almond Amaretto Cake', 'Almond cake with amaretto liqueur.', 'imgs/Almond Amaretto Cake.jpg', 'Cakes'),
(30, 'medium', 'Earl Grey Cake With Vanilla Bean Buttercream', 'Earl Grey tea with vanilla bean frosting.', 'imgs/Earl Grey Cake With Vanilla Bean Buttercream.jpg', 'Cakes'),
(31, 'medium', 'Froot Loops Cake', 'Colorful cereal-inspired cake.', 'imgs/Froot Loops Cake.jpg', 'Cakes'),
(32, 'medium', 'Spice Cake with Cinnamon Streusel', 'Spiced cake with cinnamon topping.', 'imgs/Spice Cake with Cinnamon Streusel.jpg', 'Cakes'),
(33, 'medium', 'Blueberry Banana Cake with Cream Cheese Frosting', 'Blueberry and banana with creamy frosting.', 'imgs/Blueberry Banana Cake with Cream Cheese Frosting.jpg', 'Cakes'),
(34, 'medium', 'Chocolate Chip Cake With Whipped Chocolate Buttercream', 'Chocolate chips with whipped frosting.', 'imgs/Chocolate Chip Cake With Whipped Chocolate Buttercream.jpg', 'Cakes'),
(35, 'medium', 'Peanut Butter & Jelly Cake', 'Classic PB&J sandwich in cake form.', 'imgs/Peanut Butter & Jelly Cake.jpg', 'Cakes'),
(36, 'medium', 'Chocolate Orange Cake', 'Rich chocolate with zesty orange.', 'imgs/Chocolate Orange Cake.jpg', 'Cakes'),
(37, 'medium', 'Walnut Cake With Brown Sugar Buttercream', 'Walnut cake with brown sugar frosting.', 'imgs/Walnut Cake With Brown Sugar Buttercream.jpg', 'Cakes'),
(38, 'medium', 'Apple Pie Cake', 'Apple pie filling in cake layers.', 'imgs/Apple Pie Cake.jpg', 'Cakes'),
(39, 'medium', 'Water', 'Pure and refreshing hydration.', 'imgs/water (2).jpg', 'Drinks'),
(40, 'medium', 'Iced Shaken Espresso', 'Bold espresso shaken with ice.', 'imgs/IcedShakenEspresso.jpg', 'Drinks'),
(41, 'medium', 'Iced Passion Tango Tea', 'Vibrant passion fruit tea with ice.', 'imgs/IcedPassionTangoTea.jpg', 'Drinks'),
(42, 'medium', 'Strawberry Acai Lemonade Refresher', 'Refreshing strawberry and acai blend.', 'imgs/SBX20211210_StrawberryAcaiLemonadeRefreshers.jpg', 'Drinks');

-- --------------------------------------------------------

--
-- Table structure for table `cake_menu`
--

CREATE TABLE `cake_menu` (
  `id` int(11) NOT NULL,
  `package_type` enum('luxury','regular','medium') NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT 'Cakes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cake_menu`
--

INSERT INTO `cake_menu` (`id`, `package_type`, `name`, `description`, `image_url`, `category`) VALUES
(1, 'regular', 'Lemon Poppy Seed Cake', 'Light and refreshing with a citrus twist.', 'imgs/Lemon Poppy Seed Cake.jpg', 'Cakes'),
(2, 'regular', 'Orange Poppy Seed Cake with Mascarpone Frosting', 'Elegant orange cake with creamy frosting.', 'imgs/Orange Poppy Seed Cake with Mascarpone Frosting.jpg', 'Cakes'),
(3, 'regular', 'Strawberry Shortcake Cake with Mascarpone Cream', 'Rustic cake with fresh strawberries and cream.', 'imgs/Strawberry Shortcake Cake with Mascarpone Cream.jpg', 'Cakes'),
(4, 'regular', 'Cinnamon Toast Crunch Cake', 'Warm cinnamon flavor with a crunchy twist.', 'imgs/Cinnamon Toast Crunch Cake.jpg', 'Cakes'),
(5, 'regular', 'Pina Colada Cake', 'Tropical pineapple and coconut delight.', 'imgs/Pina Colada Cake.jpg', 'Cakes'),
(6, 'regular', 'Cranberry Orange Cake', 'Seasonal and refreshing citrus blend.', 'imgs/Cranberry Orange Cake.jpg', 'Cakes'),
(7, 'regular', 'Eggnog Cake', 'Festive winter cake with eggnog flavor.', 'imgs/Eggnog Cake.jpg', 'Cakes'),
(8, 'regular', 'Blueberry Shortcake Cake', 'Fresh blueberry layers with a shortcake base.', 'imgs/Blueberry Shortcake Cake.jpg', 'Cakes'),
(9, 'regular', 'Cinnamon Roll Cake', 'Warm cinnamon roll flavor in cake form.', 'imgs/Cinnamon Roll Cake.jpg', 'Cakes'),
(10, 'regular', 'Banana Pudding Cake', 'Classic banana pudding in cake layers.', 'imgs/Banana Pudding Cake.jpg', 'Cakes'),
(11, 'regular', 'Milk & Cookies Cake', 'Cookie dough and milk flavor combo.', 'imgs/Milk & Cookies Cake.jpg', 'Cakes'),
(12, 'regular', 'Poppy Seed Cake', 'Light cake with poppy seed texture.', 'imgs/Poppy Seed Cake.jpg', 'Cakes'),
(13, 'regular', 'Chocolate Chip Cookie Cake', 'Soft cookie dough with chocolate chips.', 'imgs/Chocolate Chip Cookie Cake.jpg', 'Cakes'),
(14, 'regular', 'Water', 'Pure and refreshing hydration.', 'imgs/water (2).jpg', 'Drinks'),
(15, 'regular', 'Iced Latte', 'Smooth espresso with chilled milk.', 'imgs/icedLate.png', 'Drinks'),
(16, 'regular', 'Iced Tea', 'Crisp and refreshing tea infusion.', 'imgs/icedTea.png', 'Drinks'),
(17, 'medium', 'Coffee & Baileys Cake', 'Rich coffee cake with a Baileys kick.', 'imgs/Coffee & Baileys Cake.jpg', 'Cakes'),
(18, 'medium', 'White Chocolate Candy Cane Cake', 'Decadent white chocolate with candy cane.', 'imgs/White Chocolate Candy Cane Cake.jpg', 'Cakes'),
(19, 'medium', 'Eggnog Latte Cake', 'A festive blend of eggnog and latte flavors.', 'imgs/Eggnog Latte Cake.jpg', 'Cakes'),
(20, 'medium', 'Pear & Walnut Cake with Honey Buttercream', 'A fall-inspired cake with honey buttercream.', 'imgs/Pear & Walnut Cake with Honey Buttercream.jpg', 'Cakes'),
(21, 'medium', 'Pecan Pie Cake', 'Rich pecan pie filling in cake layers.', 'imgs/Pecan Pie Cake.jpg', 'Cakes'),
(22, 'medium', 'Biscoff Cake (Cookie Butter Cake)', 'Elegant Biscoff cake displayed on a stand.', 'imgs/Biscoff Cake (Cookie Butter Cake).jpg', 'Cakes'),
(23, 'medium', 'Vanilla Latte Cake', 'Smooth vanilla with a coffee latte twist.', 'imgs/Vanilla Latte Cake.jpg', 'Cakes'),
(24, 'medium', 'White Chocolate Mocha Cake', 'Creamy white chocolate with mocha notes.', 'imgs/White Chocolate Mocha Cake.jpg', 'Cakes'),
(25, 'medium', 'White Chocolate Cake', 'Decadent white chocolate in layers and drip.', 'imgs/White Chocolate Cake.jpg', 'Cakes'),
(26, 'medium', 'Bakewell Cake (Raspberry Almond Cake)', 'Raspberry and almond in a traditional style.', 'imgs/Bakewell Cake (Raspberry Almond Cake).jpg', 'Cakes'),
(27, 'medium', 'Lime & Coconut Cake', 'Zesty lime paired with coconut layers.', 'imgs/Lime & Coconut Cake.jpg', 'Cakes'),
(28, 'medium', 'Chai Cake with Cream Cheese Frosting', 'Spiced chai with creamy frosting.', 'imgs/Chai Cake with Cream Cheese Frosting.jpg', 'Cakes'),
(29, 'medium', 'Almond Amaretto Cake', 'Almond cake with amaretto liqueur.', 'imgs/Almond Amaretto Cake.jpg', 'Cakes'),
(30, 'medium', 'Earl Grey Cake With Vanilla Bean Buttercream', 'Earl Grey tea with vanilla bean frosting.', 'imgs/Earl Grey Cake With Vanilla Bean Buttercream.jpg', 'Cakes'),
(31, 'medium', 'Froot Loops Cake', 'Colorful cereal-inspired cake.', 'imgs/Froot Loops Cake.jpg', 'Cakes'),
(32, 'medium', 'Spice Cake with Cinnamon Streusel', 'Spiced cake with cinnamon topping.', 'imgs/Spice Cake with Cinnamon Streusel.jpg', 'Cakes'),
(33, 'medium', 'Blueberry Banana Cake with Cream Cheese Frosting', 'Blueberry and banana with creamy frosting.', 'imgs/Blueberry Banana Cake with Cream Cheese Frosting.jpg', 'Cakes'),
(34, 'medium', 'Chocolate Chip Cake With Whipped Chocolate Buttercream', 'Chocolate chips with whipped frosting.', 'imgs/Chocolate Chip Cake With Whipped Chocolate Buttercream.jpg', 'Cakes'),
(35, 'medium', 'Peanut Butter & Jelly Cake', 'Classic PB&J sandwich in cake form.', 'imgs/Peanut Butter & Jelly Cake.jpg', 'Cakes'),
(36, 'medium', 'Chocolate Orange Cake', 'Rich chocolate with zesty orange.', 'imgs/Chocolate Orange Cake.jpg', 'Cakes'),
(37, 'medium', 'Walnut Cake With Brown Sugar Buttercream', 'Walnut cake with brown sugar frosting.', 'imgs/Walnut Cake With Brown Sugar Buttercream.jpg', 'Cakes'),
(38, 'medium', 'Apple Pie Cake', 'Apple pie filling in cake layers.', 'imgs/Apple Pie Cake.jpg', 'Cakes'),
(39, 'medium', 'Water', 'Pure and refreshing hydration.', 'imgs/water (2).jpg', 'Drinks'),
(40, 'medium', 'Iced Shaken Espresso', 'Bold espresso shaken with ice.', 'imgs/IcedShakenEspresso.jpg', 'Drinks'),
(41, 'medium', 'Iced Passion Tango Tea', 'Vibrant passion fruit tea with ice.', 'imgs/IcedPassionTangoTea.jpg', 'Drinks'),
(42, 'medium', 'Strawberry Acai Lemonade Refresher', 'Refreshing strawberry and acai blend.', 'imgs/SBX20211210_StrawberryAcaiLemonadeRefreshers.jpg', 'Drinks');

-- --------------------------------------------------------

--
-- Table structure for table `card_templates`
--

CREATE TABLE `card_templates` (
  `id` int(11) NOT NULL,
  `package_type` enum('luxury','regular','medium') NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `design_json` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `card_templates`
--

INSERT INTO `card_templates` (`id`, `package_type`, `template_name`, `preview_image`, `design_json`, `created_at`) VALUES
(1, 'regular', 'Classic Elegance', NULL, NULL, '2025-12-20 23:44:21'),
(2, 'medium', 'Modern Grace', NULL, NULL, '2025-12-20 23:44:21'),
(3, 'luxury', 'Royal Luxury', NULL, NULL, '2025-12-20 23:44:21');

-- --------------------------------------------------------

--
-- Table structure for table `ceremonies`
--

CREATE TABLE `ceremonies` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ceremony_date` date NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `layout` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ceremony_photos`
--

CREATE TABLE `ceremony_photos` (
  `id` int(11) NOT NULL,
  `ceremony_id` int(11) NOT NULL,
  `photo_url` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `food_menu`
--

CREATE TABLE `food_menu` (
  `id` int(11) NOT NULL,
  `package_type` enum('luxury','regular','medium') NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT 'Main Dishes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_menu`
--

INSERT INTO `food_menu` (`id`, `package_type`, `name`, `description`, `image_url`, `category`) VALUES
(1, 'luxury', 'Grilled Salmon with Olive Oil', 'Succulent salmon grilled with olive oil.', 'imgs/GrilledSalmon.jpg', 'Main Dishes'),
(2, 'luxury', 'Grilled Jumbo Shrimp', 'Juicy jumbo shrimp with a smoky finish.', 'imgs/JumboShrimp.jpg', 'Main Dishes'),
(3, 'luxury', 'Crab with Herbs', 'Tender crab infused with fresh herbs.', 'imgs/HerbCrab.jpg', 'Main Dishes'),
(4, 'luxury', 'Seafood Pasta', 'Pasta with a medley of seafood delights.', 'imgs/SeafoodPasta.jpg', 'Main Dishes'),
(5, 'luxury', 'Squid Risotto', 'Creamy risotto with tender squid.', 'imgs/SquidRisotto.jpg', 'Main Dishes'),
(6, 'luxury', 'Grilled Oysters', 'Oysters grilled to perfection.', 'imgs/GrilledOysters.jpg', 'Main Dishes'),
(7, 'luxury', 'Creamy Shrimp Tray', 'Shrimp in a rich creamy sauce.', 'imgs/CreamyShrimpTray.jpg', 'Main Dishes'),
(8, 'luxury', 'Shrimp Croquettes', 'Crispy croquettes filled with shrimp.', 'imgs/ShrimpCroquettes.jpg', 'Main Dishes'),
(9, 'luxury', 'Grilled Lobster with Garlic', 'Lobster grilled with aromatic garlic.', 'imgs/GarlicLobster.jpg', 'Main Dishes'),
(10, 'luxury', 'Shrimp Fajita', 'Sizzling shrimp with vibrant veggies.', 'imgs/ShrimpFajita.jpg', 'Main Dishes'),
(11, 'luxury', 'Squid Rice', 'Fragrant rice with tender squid.', 'imgs/SquidRice.jpg', 'Main Dishes'),
(12, 'luxury', 'Sea Bream with Herbs', 'Sea bream with aromatic herb seasoning.', 'imgs/HerbSeaBream.jpg', 'Main Dishes'),
(13, 'luxury', 'Premium Sushi', 'Exquisite sushi with fresh seafood.', 'imgs/PremiumSushi.jpg', 'Main Dishes'),
(14, 'luxury', 'Grilled Fish with Lemon', 'Fish grilled with zesty lemon flavor.', 'imgs/LemonGrilledFish.jpg', 'Main Dishes'),
(15, 'luxury', 'Seafood Soup', 'Rich soup with assorted seafood.', 'imgs/SeafoodSoup.jpg', 'Main Dishes'),
(16, 'luxury', 'Stuffed Fish', 'Fish stuffed with herbs and spices.', 'imgs/StuffedFish.jpg', 'Main Dishes'),
(17, 'luxury', 'Fish Kebab', 'Skewered fish with savory spices.', 'imgs/FishKebab.jpg', 'Main Dishes'),
(18, 'luxury', 'Salmon Fillet with Lemon', 'Tender salmon with lemon sauce.', 'imgs/LemonSalmonFillet.jpg', 'Main Dishes'),
(19, 'luxury', 'Seafood Tagliatelle', 'Tagliatelle with mixed seafood.', 'imgs/SeafoodTagliatelle.jpg', 'Main Dishes'),
(20, 'luxury', 'Shrimp Pie', 'Savory pie filled with shrimp.', 'imgs/ShrimpPie.jpg', 'Main Dishes'),
(21, 'luxury', 'Luxury Cheese Samosa', 'Crispy samosas with premium cheese.', 'imgs/CheeseSamosaLuxury.jpg', 'Appetizers'),
(22, 'luxury', 'Salmon Crostini', 'Toasted bread topped with salmon.', 'imgs/SalmonCrostini.jpg', 'Appetizers'),
(23, 'luxury', 'Caesar Salad with Shrimp', 'Classic Caesar with fresh shrimp.', 'imgs/CaesarShrimpSalad.jpg', 'Appetizers'),
(24, 'luxury', 'Caviar', 'Premium caviar with delicate flavor.', 'imgs/Caviar.jpg', 'Appetizers'),
(25, 'luxury', 'Smoked Seafood', 'Assorted smoked seafood delicacies.', 'imgs/SmokedSeafood.jpg', 'Appetizers'),
(26, 'luxury', 'Smoked Salmon Rolls', 'Delicate rolls with smoked salmon.', 'imgs/SmokedSalmonRolls.jpg', 'Appetizers'),
(27, 'luxury', 'Seafood Cream Soup', 'Rich creamy soup with seafood.', 'imgs/SeafoodCreamSoup.jpg', 'Appetizers'),
(28, 'luxury', 'Salmon Tart', 'Flaky tart with fresh salmon.', 'imgs/SalmonTart.jpg', 'Appetizers'),
(29, 'luxury', 'Oysters', 'Fresh oysters with a briny taste.', 'imgs/Oysters.jpg', 'Appetizers'),
(30, 'luxury', 'Avocado and Shrimp Salad', 'Creamy avocado with tender shrimp.', 'imgs/AvocadoShrimpSalad.jpg', 'Appetizers'),
(31, 'luxury', 'Mini Salmon Burger', 'Bite-sized burgers with salmon patty.', 'imgs/MiniSalmonBurger.jpg', 'Appetizers'),
(32, 'luxury', 'Black Caviar', 'Luxurious black caviar delicacy.', 'imgs/BlackCaviar.jpg', 'Appetizers'),
(33, 'luxury', 'Stuffed Mushrooms with Cheese', 'Mushrooms stuffed with creamy cheese.', 'imgs/StuffedMushrooms.jpg', 'Appetizers'),
(34, 'luxury', 'Crab Sticks', 'Flavorful crab sticks with dip.', 'imgs/CrabSticks.jpg', 'Appetizers'),
(35, 'luxury', 'Tomato Bruschetta', 'Toasted bread with fresh tomato.', 'imgs/TomatoBruschetta.jpg', 'Appetizers'),
(36, 'luxury', 'Sushi Rolls', 'Fresh sushi rolls with seafood.', 'imgs/SushiRolls.jpg', 'Appetizers'),
(37, 'luxury', 'Grilled Shrimp Skewers', 'Shrimp skewers grilled to perfection.', 'imgs/GrilledShrimpSkewers.jpg', 'Appetizers'),
(38, 'luxury', 'Crab Soup', 'Rich crab soup with spices.', 'imgs/CrabSoup.jpg', 'Appetizers'),
(39, 'luxury', 'Seafood Spring Rolls', 'Crispy rolls with seafood filling.', 'imgs/SeafoodSpringRolls.jpg', 'Appetizers'),
(40, 'luxury', 'Smoked Fish with Lemon', 'Smoked fish with lemon slices.', 'imgs/SmokedFishLemon.jpg', 'Appetizers'),
(41, 'medium', 'Creamy Chicken with Mushroom', 'Tender chicken in creamy mushroom sauce.', 'imgs/CreamyChickenMushroom.jpg', 'Main Dishes'),
(42, 'medium', 'Beef with Brown Sauce', 'Juicy beef with rich brown sauce.', 'imgs/BeefBrownSauce.jpg', 'Main Dishes'),
(43, 'medium', 'Grilled Kufta', 'Spiced meatballs grilled to perfection.', 'imgs/GrilledKufta.jpg', 'Main Dishes'),
(44, 'medium', 'Mixed Stuffed Vegetables', 'Zucchini and eggplant stuffed with rice.', 'imgs/MixedStuffedVegetables.jpg', 'Main Dishes'),
(45, 'medium', 'Saffron Rice', 'Golden rice infused with saffron.', 'imgs/SaffronRice.jpg', 'Main Dishes'),
(46, 'medium', 'Alfredo Pasta', 'Creamy pasta with rich Alfredo sauce.', 'imgs/AlfredoPasta.jpg', 'Main Dishes'),
(47, 'medium', 'Eggplant Stew with Meat', 'Hearty eggplant stew with tender meat.', 'imgs/EggplantStewMeat.jpg', 'Main Dishes'),
(48, 'medium', 'Potato Tray with Meat', 'Baked potatoes with savory meat.', 'imgs/PotatoTrayMeat.jpg', 'Main Dishes'),
(49, 'medium', 'Herb Grilled Chicken', 'Juicy chicken with aromatic herbs.', 'imgs/HerbGrilledChicken.jpg', 'Main Dishes'),
(50, 'medium', 'Chicken Kabsa', 'Spiced rice with tender chicken.', 'imgs/ChickenKabsa.jpg', 'Main Dishes'),
(51, 'medium', 'Freekeh Soup', 'Warm freekeh soup with rich flavor.', 'imgs/FreekehSoup.jpg', 'Main Dishes'),
(52, 'medium', 'Lasagna', 'Layered pasta with meat and béchamel.', 'imgs/Lasagna.jpg', 'Main Dishes'),
(53, 'medium', 'Fried Fish Fillet', 'Crispy fish fillets with marinade.', 'imgs/FriedFishFillet.jpg', 'Main Dishes'),
(54, 'medium', 'Grilled Baby Shrimp', 'Tender shrimp grilled to perfection.', 'imgs/GrilledBabyShrimp.jpg', 'Main Dishes'),
(55, 'medium', 'Shish Tawook', 'Marinated chicken skewers, grilled.', 'imgs/ShishTawook.jpg', 'Main Dishes'),
(56, 'medium', 'Chicken Fajita', 'Sizzling chicken with colorful veggies.', 'imgs/ChickenFajita.jpg', 'Main Dishes'),
(57, 'medium', 'Chicken with Tomato', 'Chicken in rich tomato sauce.', 'imgs/ChickenTomato.jpg', 'Main Dishes'),
(58, 'medium', 'Rice with Vermicelli', 'Fluffy rice with toasted vermicelli.', 'imgs/RiceVermicelli.jpg', 'Main Dishes'),
(59, 'medium', 'Pasta with White Sauce', 'Creamy pasta with white sauce.', 'imgs/PastaWhiteSauce.jpg', 'Main Dishes'),
(60, 'medium', 'Simple Indian Kebab', 'Spiced kebab with Indian flavors.', 'imgs/IndianKebab.jpg', 'Main Dishes'),
(61, 'medium', 'Fattoush Salad', 'Fresh vegetables with crispy bread.', 'imgs/FattoushSalad.jpg', 'Appetizers'),
(62, 'medium', 'Hummus with Meat', 'Creamy hummus topped with minced meat.', 'imgs/HummusMeat.jpg', 'Appetizers'),
(63, 'medium', 'Fine Tabbouleh', 'Finely chopped parsley and bulgur salad.', 'imgs/FineTabbouleh.jpg', 'Appetizers'),
(64, 'medium', 'Arugula and Pomegranate Salad', 'Zesty arugula with sweet pomegranate.', 'imgs/ArugulaPomegranate.jpg', 'Appetizers'),
(65, 'medium', 'Cheese Samosa', 'Crispy samosas filled with cheese.', 'imgs/CheeseSamosa.jpg', 'Appetizers'),
(66, 'medium', 'Fried Kibbeh', 'Crispy kibbeh stuffed with meat.', 'imgs/FriedKibbeh.jpg', 'Appetizers'),
(67, 'medium', 'Vegetable Soup', 'Light soup with fresh vegetables.', 'imgs/VegetableSoup.jpg', 'Appetizers'),
(68, 'medium', 'Greek Salad', 'Fresh veggies with feta cheese.', 'imgs/GreekSalad.jpg', 'Appetizers'),
(69, 'medium', 'Carrot Mayo Salad', 'Creamy carrot salad with mayonnaise.', 'imgs/CarrotMayoSalad.jpg', 'Appetizers'),
(70, 'medium', 'Chicken Rolls', 'Crispy rolls stuffed with chicken.', 'imgs/ChickenRolls.jpg', 'Appetizers'),
(71, 'medium', 'Fried Cheese Balls', 'Golden fried cheese balls.', 'imgs/FriedCheeseBalls.jpg', 'Appetizers'),
(72, 'medium', 'Mozzarella Sticks', 'Crispy mozzarella with dipping sauce.', 'imgs/MozzarellaSticks.jpg', 'Appetizers'),
(73, 'medium', 'Creamy Chicken Soup', 'Rich and creamy chicken soup.', 'imgs/CreamyChickenSoup.jpg', 'Appetizers'),
(74, 'medium', 'Caesar Salad', 'Classic salad with creamy dressing.', 'imgs/CaesarSalad.jpg', 'Appetizers'),
(75, 'medium', 'Lentil Soup with Cumin', 'Spiced lentil soup with cumin.', 'imgs/LentilSoupCumin.jpg', 'Appetizers'),
(76, 'medium', 'Potato Wedges', 'Crispy wedges with savory seasoning.', 'imgs/PotatoWedges.jpg', 'Appetizers'),
(77, 'medium', 'Bean Salad', 'Fresh beans with light dressing.', 'imgs/BeanSalad.jpg', 'Appetizers'),
(78, 'medium', 'Vegetable Spring Rolls', 'Crispy rolls with fresh veggies.', 'imgs/VeggieSpringRolls.jpg', 'Appetizers'),
(79, 'medium', 'Broasted Chicken', 'Crispy and juicy broasted chicken.', 'imgs/BroastedChicken.jpg', 'Appetizers'),
(80, 'medium', 'Sautéed Vegetables', 'Colorful veggies lightly sautéed.', 'imgs/SauteedVegetables.jpg', 'Appetizers'),
(81, 'regular', 'Vegetable Stew', 'A hearty mix of fresh vegetables.', 'imgs/VegetableStew.jpg', 'Main Dishes'),
(82, 'regular', 'Mulukhiyah', 'Traditional green stew with savory flavor.', 'imgs/mulukhiyah.jpg', 'Main Dishes'),
(83, 'regular', 'Shawarma', 'Tender meat wrapped in warm pita.', 'imgs/shawarma.jpg', 'Main Dishes'),
(84, 'regular', 'Pizza', 'Classic pizza with fresh toppings.', 'imgs/pizza.jpg', 'Main Dishes'),
(85, 'regular', 'Fried Fish', 'Crispy fish fillets with marinade.', 'imgs/friedFish.jpg', 'Main Dishes'),
(86, 'regular', 'Moussaka', 'Layered eggplant with spiced meat.', 'imgs/Moussaka.jpg', 'Main Dishes'),
(87, 'regular', 'Grilled Chicken', 'Juicy chicken grilled to perfection.', 'imgs/chicken.jpg', 'Main Dishes'),
(88, 'regular', 'Delicious Pasta', 'Creamy pasta with choice of sauces.', 'imgs/pasta.jpg', 'Main Dishes'),
(89, 'regular', 'Tomato Pasta', 'Pasta in vibrant tomato sauce.', 'imgs/tomatoPasta.jpg', 'Main Dishes'),
(90, 'regular', 'Kufta', 'Spiced meatballs grilled to delight.', 'imgs/kufta.jpg', 'Main Dishes'),
(91, 'regular', 'Stuffed Onions', 'Onions filled with flavorful rice.', 'imgs/Stuffedonions.jpg', 'Main Dishes'),
(92, 'regular', 'Rice with Vegetables', 'Fragrant rice with colorful veggies.', 'imgs/rice.jpg', 'Main Dishes'),
(93, 'regular', 'Pulled Chicken', 'Tender shredded chicken in sauce.', 'imgs/PulledChicken.jpg', 'Main Dishes'),
(94, 'regular', 'Roasted Potatoes', 'Crispy potatoes with herbs.', 'imgs/RoastedPotatoes.jpg', 'Main Dishes'),
(95, 'regular', 'White Rice', 'Fluffy plain rice, perfectly cooked.', 'imgs/WhiteRice.jpg', 'Main Dishes'),
(96, 'regular', 'Stuffed Zucchini', 'Zucchini filled with rice and spices.', 'imgs/StuffedZucchini.jpg', 'Main Dishes'),
(97, 'regular', 'Chicken in Yogurt', 'Chicken cooked in creamy yogurt sauce.', 'imgs/ChickenYogurt.jpg', 'Main Dishes'),
(98, 'regular', 'Hummus', 'Creamy dips served with warm pita.', 'imgs/stuff.jpg', 'Appetizers'),
(99, 'regular', 'Tabbouleh', 'Fresh parsley and bulgur salad.', 'imgs/Tabbouleh.jpg', 'Appetizers'),
(100, 'regular', 'Green Salad', 'Crisp greens with a light dressing.', 'imgs/salad1.jpg', 'Appetizers'),
(101, 'regular', 'Falafel', 'Crispy chickpea patties with tahini.', 'imgs/falafel.jpg', 'Appetizers'),
(102, 'regular', 'Stuffed Grape Leaves (Dawali)', 'Rice-filled grape leaves with herbs.', 'imgs/dawali.jpg', 'Appetizers'),
(103, 'regular', 'Labneh, Hummus & Foul', 'Creamy dips served with warm pita.', 'imgs/stuff.jpg', 'Appetizers'),
(104, 'regular', 'Lentil Soup', 'Warm, spiced lentil soup.', 'imgs/letsoup.jpg', 'Appetizers'),
(105, 'regular', 'Vegetable Soup', 'Light soup with fresh vegetables.', 'imgs/vegsoup.jpg', 'Appetizers'),
(106, 'regular', 'Cabbage Salad', 'Crunchy cabbage with a tangy dressing.', 'imgs/Cabbagesalad.jpg', 'Appetizers'),
(107, 'regular', 'Hummus with Tahini', 'Smooth hummus topped with tahini sauce.', 'imgs/HummusTahini.jpg', 'Appetizers'),
(108, 'regular', 'Mutabbal Eggplant', 'Smoky eggplant dip with garlic.', 'imgs/Mutabbal.jpg', 'Appetizers'),
(109, 'regular', 'Arugula Salad with Lemon', 'Zesty arugula with lemon dressing.', 'imgs/ArugulaSalad.jpg', 'Appetizers'),
(110, 'regular', 'Mixed Appetizers', 'Olives, pickles, and green onions.', 'imgs/MixedAppetizers.jpg', 'Appetizers'),
(111, 'regular', 'Cucumber Yogurt Salad', 'Refreshing yogurt with cucumber.', 'imgs/CucumberYogurt.jpg', 'Appetizers'),
(112, 'regular', 'Tahini Salad', 'Creamy tahini with fresh vegetables.', 'imgs/TahiniSalad.jpg', 'Appetizers'),
(113, 'regular', 'Corn Salad', 'Sweet corn with a light vinaigrette.', 'imgs/CornSalad.jpg', 'Appetizers');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `photo_url` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `status` enum('pending','attending','not_attending') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invitations`
--

CREATE TABLE `invitations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `design` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invitation_cards`
--

CREATE TABLE `invitation_cards` (
  `id` int(11) NOT NULL,
  `package_type` enum('luxury','regular','medium') NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invitation_cards`
--

INSERT INTO `invitation_cards` (`id`, `package_type`, `name`, `image_url`) VALUES
(1, 'luxury', 'Olive Elegance', 'imgs/LX1.png'),
(2, 'luxury', 'Sage Harmony', 'imgs/LX2.png'),
(3, 'luxury', 'Emerald Whisper', 'imgs/LX3.png'),
(4, 'luxury', 'Forest Bloom', 'imgs/LX4.png'),
(5, 'luxury', 'Mint Luxury', 'imgs/LX5.png'),
(6, 'luxury', 'Verdant Gold', 'imgs/LX6.png'),
(7, 'luxury', 'Green Marble', 'imgs/LX7.png'),
(8, 'luxury', 'Luxury Fern', 'imgs/LX8.png'),
(9, 'medium', 'Romantic Bloom', 'imgs/SM5.png'),
(10, 'medium', 'Golden Chic', 'imgs/SM2.png'),
(11, 'medium', 'Vintage Charm', 'imgs/SM3.png'),
(12, 'medium', 'Minimal Elegance', 'imgs/SM4.png'),
(13, 'regular', 'Classic Elegance', 'imgs/sR1.png'),
(14, 'regular', 'Modern Grace', 'imgs/sR2.png');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `from_user_id`, `to_user_id`, `content`, `guest_name`, `guest_email`, `is_read`, `sent_at`) VALUES
(1, 13, 8, 'try it', NULL, NULL, 0, '2025-12-23 08:13:32'),
(2, 13, 9, 'try it', NULL, NULL, 1, '2025-12-23 08:13:32'),
(3, 9, 13, 'huhjk', NULL, NULL, 0, '2025-12-23 08:20:32'),
(4, 13, 8, 'help me', NULL, NULL, 0, '2025-12-23 08:21:22'),
(5, 13, 9, 'help me', NULL, NULL, 0, '2025-12-23 08:21:22'),
(6, 13, 8, 'hello admin', NULL, NULL, 0, '2025-12-23 11:40:05'),
(7, 13, 9, 'hello admin', NULL, NULL, 1, '2025-12-23 11:40:05'),
(8, 9, 13, 'hello zainab', NULL, NULL, 1, '2025-12-23 11:40:45');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `created_at`) VALUES
(1, 'reg', '2025-12-20 22:52:59'),
(2, 'med', '2025-12-20 22:52:59'),
(3, 'lux', '2025-12-20 22:52:59'),
(5, 'basic', '2025-12-23 11:42:23');

-- --------------------------------------------------------

--
-- Table structure for table `photography_sessions`
--

CREATE TABLE `photography_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_date` date DEFAULT NULL,
  `session_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `photography_sessions`
--

INSERT INTO `photography_sessions` (`id`, `user_id`, `session_date`, `session_time`, `location`, `notes`, `created_at`, `updated_at`) VALUES
(1, 13, '2028-12-12', '00:12:00', 'nablus', 'hghhh', '2025-12-23 21:14:39', '2025-12-23 21:14:39');

-- --------------------------------------------------------

--
-- Table structure for table `planning_tips`
--

CREATE TABLE `planning_tips` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `planning_tips`
--

INSERT INTO `planning_tips` (`id`, `title`, `content`, `created_at`) VALUES
(1, 'Choose Your Theme Early', 'Setting a theme early helps in cohesive decision making for decor, attire, and venue.', '2025-12-22 17:23:54'),
(2, 'Budget Buffer', 'Always add a 10-15% buffer to your wedding budget for unexpected costs.', '2025-12-22 17:23:54'),
(3, 'Guest List Strategy', 'Start with your must-haves and expand from there to avoid overcrowding.', '2025-12-22 17:23:54');

-- --------------------------------------------------------

--
-- Table structure for table `rsvp_guests`
--

CREATE TABLE `rsvp_guests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `unique_code` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `status` enum('pending','attending','not-attending') DEFAULT 'pending',
  `party_size` int(11) DEFAULT 1,
  `dietary_restrictions` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rsvp_guests`
--

INSERT INTO `rsvp_guests` (`id`, `user_id`, `event_id`, `unique_code`, `name`, `email`, `phone`, `status`, `party_size`, `dietary_restrictions`, `notes`, `responded_at`, `created_at`, `updated_at`) VALUES
(1, 13, NULL, 'e945d448fba4', 'Zainab qased Samarah', 'zainab.samarah@hotmail.com', '+970594907091', 'pending', 1, NULL, 'sddd', NULL, '2025-12-23 19:33:58', '2025-12-23 19:33:58'),
(2, 13, 1, 'd646775cf4ea', 'Zainab Samarah', 'zainab.samarah@hotmail.com', '+972594907091', 'attending', 1, 'Hh', NULL, '2025-12-23 20:02:13', '2025-12-23 20:02:13', '2025-12-23 20:02:13'),
(3, 13, 1, '817a57911011', 'Zainab', 's12219989@stu.najah.edu', '+972594907091', 'attending', 4, 'Anything', NULL, '2025-12-23 20:46:55', '2025-12-23 20:46:55', '2025-12-23 20:46:55');

-- --------------------------------------------------------

--
-- Table structure for table `rsvp_messages`
--

CREATE TABLE `rsvp_messages` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rsvp_messages`
--

INSERT INTO `rsvp_messages` (`id`, `guest_id`, `user_id`, `message`, `created_at`) VALUES
(1, 2, 13, 'Ggg', '2025-12-23 20:02:14'),
(2, 3, 13, 'Coming', '2025-12-23 20:46:55');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `category` enum('food','decoration','venue','photography','entertainment','other') DEFAULT 'other',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `price`, `category`, `created_at`) VALUES
(1, '', '', 0.00, 'other', '2025-12-22 17:54:32'),
(2, '', '', 0.00, 'other', '2025-12-22 17:54:34'),
(3, '', '', 0.00, 'other', '2025-12-22 17:59:50');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'about_wede', 'WEDE is your ultimate partner in creating the wedding of your dreams. We handle everything from planning to execution with elegance.'),
(2, 'privacy_policy', 'Your privacy is important to us. We only collect data necessary for your wedding planning process.'),
(3, 'terms_of_service', 'By using WEDE, you agree to our terms of providing premium wedding planning services.'),
(4, 'contact_info', 'Email: contact@wede.com | Phone: +1 234 567 890'),
(5, 'company_desc', 'WEDE Luxury Wedding Planners - crafting unforgettable moments since 2020.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','owner','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `age`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'Zainab', 'Samarah', 55, 'b.b@hotmail.com', '$2y$10$1/QpnnRHhwZosQuHtQleXubUFi/lS50CqDPOygCh109sUBMvYw3wa', 'user', '2025-12-20 16:11:10'),
(3, 'Zainab', 'Samarah', 22, 'z.z@hotmail.com', '$2y$10$fnimMiKSDP/O8aZ6wHrKVexIXCQC.F.uCSA34UjYnmGgGrDziDpaS', 'user', '2025-12-20 16:26:49'),
(4, 'testuser', '123', 25, 'testuser123@example.com', '$2y$10$kAM7ZZ.STrflWDZveMjIK.ixeOiSNRl3n9eYbAow48KVKLAqXJwgy', 'user', '2025-12-20 21:24:45'),
(5, 'testuser', 't', 23, 'testuser3@example.com', '$2y$10$v6i5UEmZ1Nr1Ru8tYrJQJ.DR4f6etFJ/F7OUJJkxFHuT77AtmMJzS', 'user', '2025-12-20 21:44:12'),
(6, 'dana', 'thuthain', 22, 'dana.thul@hotmail.com', '$2y$10$LZXe6C9uoTQopAeCLjh1fe.TmFDgbQ2UJA93.58dbCE2J8bXBmy/i', 'user', '2025-12-20 22:39:35'),
(7, 'Zainab', 'Samarah', 21, 'h.h@hotmail.com', '$2y$10$a9kCPtniVEoaI5AJ5Gzf0.ywPSb2LV0bFGEjkCIjHjZmTdnjbl8La', 'user', '2025-12-22 16:31:37'),
(8, 'Admin', 'Root', NULL, 'admin@wede.com', '$2y$10$eImZwOBEFG1VvQrMg8zhzesJEy0VTZKr2B7W7UbnuYBD7ksdnQo3S', 'owner', '2025-12-22 17:23:54'),
(9, 'joe', 'hh', 52, 'joe.h@hotmail.com', '$2y$10$hntFwExjDJgwRyLuU9z4newH6pBoeAhaFknUqcgdi9bhuBx7YQYT2', 'owner', '2025-12-22 17:30:09'),
(10, '', '', NULL, '', '$2y$10$/4riHCsY6zIULWDE8bpJ8eeyI87SgbfAmpbqnesLa6x20mFIJeVwa', 'user', '2025-12-22 17:59:47'),
(12, 'jana', 'masoud', 22, 'jana.masoud@hotmail.com', '$2y$10$xtVq.meX84Vmlfg4XfrhcuxVZ7QHK.weXDQHvvcyUty6FYqaGYMei', 'user', '2025-12-22 19:04:04'),
(13, 'Zainab', 'Samarah', 21, 's.s@hotmail.com', '$2y$10$DL4OgMFrcQpoRuq6AaZWWe7pCT5cyRMsCoTmWbXGBwLmsU1O/.2qK', 'user', '2025-12-22 20:32:51');

-- --------------------------------------------------------

--
-- Table structure for table `user_budget_settings`
--

CREATE TABLE `user_budget_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_budget` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'USD',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_cake_selections`
--

CREATE TABLE `user_cake_selections` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cake_id` int(11) NOT NULL,
  `selected_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_cake_selections`
--

INSERT INTO `user_cake_selections` (`id`, `user_id`, `cake_id`, `selected_at`) VALUES
(1, 6, 303, '2025-12-20 23:25:27'),
(2, 6, 315, '2025-12-20 23:25:27'),
(3, 10, 1, '2025-12-22 18:10:06');

-- --------------------------------------------------------

--
-- Table structure for table `user_card_customizations`
--

CREATE TABLE `user_card_customizations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_template_id` int(11) NOT NULL,
  `bride_name` varchar(255) NOT NULL,
  `groom_name` varchar(255) NOT NULL,
  `wedding_date` date DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_card_customizations`
--

INSERT INTO `user_card_customizations` (`id`, `user_id`, `card_template_id`, `bride_name`, `groom_name`, `wedding_date`, `location`, `created_at`) VALUES
(1, 12, 1, 'jana', 'jana', '2028-09-27', 'San Francisco', '2025-12-22 19:07:19'),
(2, 13, 1, 'Zainab', 'Benjamin', '2028-09-27', 'San Francisco', '2025-12-23 11:38:56');

-- --------------------------------------------------------

--
-- Table structure for table `user_card_selection`
--

CREATE TABLE `user_card_selection` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_template_id` int(11) NOT NULL,
  `selected_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bride_name` varchar(200) DEFAULT NULL,
  `groom_name` varchar(200) DEFAULT NULL,
  `wedding_date` date DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `custom_text` text DEFAULT NULL,
  `card_design_json` text DEFAULT NULL COMMENT 'Stores full card customization as JSON'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_card_selection`
--

INSERT INTO `user_card_selection` (`id`, `user_id`, `card_template_id`, `selected_at`, `bride_name`, `groom_name`, `wedding_date`, `location`, `custom_text`, `card_design_json`) VALUES
(1, 7, 1, '2025-12-22 17:28:23', 'zainab', 'stafg', '2028-09-27', 'nablus', 'SR1.png', '{\"template\":\"SR1.png\",\"elements\":[{\"text\":\"Save the Date\",\"top\":\"25%\",\"left\":\"\",\"fontSize\":\"12px\",\"fontFamily\":\"\",\"color\":\"\"},{\"text\":\"FOR THE WEDDING OF\",\"top\":\"33%\",\"left\":\"\",\"fontSize\":\"13px\",\"fontFamily\":\"\",\"color\":\"\"},{\"text\":\"CALEB\\n                    GOLDEN\",\"top\":\"45%\",\"left\":\"\",\"fontSize\":\"20px\",\"fontFamily\":\"\\\"Playfair Display\\\", serif\",\"color\":\"\"},{\"text\":\"and\\n                \",\"top\":\"50%\",\"left\":\"\",\"fontSize\":\"16px\",\"fontFamily\":\"\\\"Dancing Script\\\", cursive\",\"color\":\"\"},{\"text\":\"BENJAMIN\\n                    JAFFE\",\"top\":\"55%\",\"left\":\"\",\"fontSize\":\"20px\",\"fontFamily\":\"\\\"Playfair Display\\\", serif\",\"color\":\"\"},{\"text\":\"Sunday, the 27 of September, 2028\",\"top\":\"65%\",\"left\":\"\",\"fontSize\":\"12px\",\"fontFamily\":\"\",\"color\":\"\"},{\"text\":\"San Francisco, California\",\"top\":\"75%\",\"left\":\"\",\"fontSize\":\"12px\",\"fontFamily\":\"\",\"color\":\"\"},{\"text\":\"Invitation to follow\",\"top\":\"80%\",\"left\":\"\",\"fontSize\":\"11px\",\"fontFamily\":\"\",\"color\":\"\"}]}'),
(2, 12, 1, '2025-12-22 19:07:19', 'jana', 'jana', '2028-09-27', 'San Francisco', 'SR1.png', '{\"template\":\"SR1.png\",\"elements\":[{\"text\":\"Save the Date\",\"top\":\"25%\",\"left\":\"\",\"fontSize\":\"12px\",\"fontFamily\":\"\",\"color\":\"\"},{\"text\":\"FOR THE WEDDING OF\",\"top\":\"33%\",\"left\":\"\",\"fontSize\":\"13px\",\"fontFamily\":\"\",\"color\":\"\"},{\"text\":\"CALEB\\n                    GOLDEN\",\"top\":\"45%\",\"left\":\"\",\"fontSize\":\"20px\",\"fontFamily\":\"\\\"Playfair Display\\\", serif\",\"color\":\"\"},{\"text\":\"and\\n                \",\"top\":\"50%\",\"left\":\"\",\"fontSize\":\"16px\",\"fontFamily\":\"\\\"Dancing Script\\\", cursive\",\"color\":\"\"},{\"text\":\"BENJAMIN\\n                    JAFFE\",\"top\":\"55%\",\"left\":\"\",\"fontSize\":\"20px\",\"fontFamily\":\"\\\"Playfair Display\\\", serif\",\"color\":\"\"},{\"text\":\"Sunday, the 27 of September, 2028\",\"top\":\"65%\",\"left\":\"\",\"fontSize\":\"12px\",\"fontFamily\":\"\",\"color\":\"\"},{\"text\":\"San Francisco, California\",\"top\":\"75%\",\"left\":\"\",\"fontSize\":\"12px\",\"fontFamily\":\"\",\"color\":\"\"},{\"text\":\"Invitation to follow\",\"top\":\"80%\",\"left\":\"\",\"fontSize\":\"11px\",\"fontFamily\":\"\",\"color\":\"\"}]}'),
(3, 13, 1, '2025-12-23 20:01:03', 'zainab', 'Benjamin', '2028-09-27', 'San Francisco', 'SR1.png', '{\"template\":\"SR1.png\",\"elements\":[{\"text\":\"Save the Date\",\"top\":\"25%\",\"left\":\"\",\"fontSize\":\"12px\",\"fontFamily\":\"\",\"color\":\"\"},{\"text\":\"FOR THE WEDDING OF\",\"top\":\"33%\",\"left\":\"\",\"fontSize\":\"13px\",\"fontFamily\":\"\",\"color\":\"\"},{\"text\":\"CALEB\\n                    GOLDEN\",\"top\":\"45%\",\"left\":\"\",\"fontSize\":\"20px\",\"fontFamily\":\"\\\"Playfair Display\\\", serif\",\"color\":\"\"},{\"text\":\"and\\n                \",\"top\":\"50%\",\"left\":\"\",\"fontSize\":\"16px\",\"fontFamily\":\"\\\"Dancing Script\\\", cursive\",\"color\":\"\"},{\"text\":\"BENJAMIN\\n                    JAFFE\",\"top\":\"55%\",\"left\":\"\",\"fontSize\":\"20px\",\"fontFamily\":\"\\\"Playfair Display\\\", serif\",\"color\":\"\"},{\"text\":\"Sunday, the 27 of September, 2028\",\"top\":\"65%\",\"left\":\"\",\"fontSize\":\"12px\",\"fontFamily\":\"\",\"color\":\"\"},{\"text\":\"San Francisco, California\",\"top\":\"75%\",\"left\":\"\",\"fontSize\":\"12px\",\"fontFamily\":\"\",\"color\":\"\"},{\"text\":\"Invitation to follow\",\"top\":\"80%\",\"left\":\"\",\"fontSize\":\"11px\",\"fontFamily\":\"\",\"color\":\"\"}]}');

-- --------------------------------------------------------

--
-- Table structure for table `user_decorations`
--

CREATE TABLE `user_decorations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `theme` varchar(100) DEFAULT NULL,
  `flowers` varchar(100) DEFAULT NULL,
  `lighting` varchar(100) DEFAULT NULL,
  `centerpieces` varchar(100) DEFAULT NULL,
  `custom_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_decorations`
--

INSERT INTO `user_decorations` (`id`, `user_id`, `theme`, `flowers`, `lighting`, `centerpieces`, `custom_notes`, `created_at`, `updated_at`) VALUES
(1, 13, 'Golden Royal', 'Roses', NULL, NULL, NULL, '2025-12-23 20:22:00', '2025-12-23 20:22:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_food_selections`
--

CREATE TABLE `user_food_selections` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `food_menu_id` int(11) NOT NULL,
  `selected_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_food_selections`
--

INSERT INTO `user_food_selections` (`id`, `user_id`, `food_menu_id`, `selected_at`) VALUES
(1, 3, 112, '2025-12-20 18:43:07'),
(2, 3, 105, '2025-12-20 18:43:07'),
(3, 6, 85, '2025-12-20 23:11:05'),
(4, 6, 82, '2025-12-20 23:11:05'),
(5, 6, 94, '2025-12-20 23:11:05'),
(6, 7, 88, '2025-12-22 18:23:21'),
(7, 7, 85, '2025-12-22 18:23:21'),
(8, 7, 87, '2025-12-22 18:23:21'),
(9, 7, 95, '2025-12-22 18:23:21'),
(10, 12, 97, '2025-12-22 19:06:01'),
(11, 12, 85, '2025-12-22 19:06:01'),
(12, 12, 82, '2025-12-22 19:06:01'),
(13, 12, 89, '2025-12-22 19:06:02'),
(14, 12, 81, '2025-12-22 19:06:02'),
(15, 12, 101, '2025-12-22 19:06:02'),
(16, 12, 100, '2025-12-22 19:06:02'),
(17, 12, 102, '2025-12-22 19:06:02'),
(18, 12, 112, '2025-12-22 19:06:02'),
(19, 13, 88, '2025-12-22 20:40:36'),
(20, 13, 85, '2025-12-22 20:40:36'),
(21, 13, 87, '2025-12-22 20:40:36'),
(22, 13, 94, '2025-12-22 20:40:36'),
(23, 13, 96, '2025-12-22 20:40:36'),
(24, 13, 89, '2025-12-22 20:40:36'),
(25, 13, 104, '2025-12-22 20:40:36'),
(26, 13, 110, '2025-12-22 20:40:36'),
(27, 13, 97, '2025-12-23 11:37:10');

-- --------------------------------------------------------

--
-- Table structure for table `user_music_preferences`
--

CREATE TABLE `user_music_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_time` varchar(100) DEFAULT NULL,
  `vibe` varchar(100) DEFAULT NULL,
  `duration` int(11) DEFAULT 0,
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_music_preferences`
--

INSERT INTO `user_music_preferences` (`id`, `user_id`, `event_time`, `vibe`, `duration`, `special_requests`, `created_at`, `updated_at`) VALUES
(1, 13, 'Ceremony', 'Romantic & Elegant', 3, 'ghhhhhhhhhh ghfgh hgghbnhgg', '2025-12-23 18:54:02', '2025-12-23 18:55:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_music_selections`
--

CREATE TABLE `user_music_selections` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_type` varchar(100) NOT NULL,
  `price_range` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_music_selections`
--

INSERT INTO `user_music_selections` (`id`, `user_id`, `item_name`, `item_type`, `price_range`, `created_at`) VALUES
(5, 13, 'Jazz & Soul', 'Music Genre', 'Included', '2025-12-23 18:55:59'),
(6, 13, 'Live Vocalist', 'Entertainment', '$350 - $700', '2025-12-23 18:55:59'),
(7, 13, 'Acoustic', 'Music Genre', 'Included', '2025-12-23 18:55:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_packages`
--

CREATE TABLE `user_packages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `package_type` enum('luxury','regular','medium') NOT NULL DEFAULT 'regular',
  `full_name` varchar(200) NOT NULL,
  `email` varchar(150) NOT NULL,
  `wedding_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_packages`
--

INSERT INTO `user_packages` (`id`, `user_id`, `package_id`, `package_type`, `full_name`, `email`, `wedding_date`, `notes`, `created_at`) VALUES
(1, 6, 2, 'regular', 'dana', 'dana.thul@hotmail.com', '2027-12-12', 'hg', '2025-12-20 22:58:42'),
(2, 7, 2, 'regular', 'dana', 'h.h@hotmail.com', '2026-12-12', 'helloooooooooooo', '2025-12-22 17:26:53'),
(3, 12, 2, 'regular', 'jana', 'jana.masoud@hotmail.com', '2026-12-12', 'this is my package', '2025-12-22 19:05:16'),
(4, 13, 1, 'regular', 'hala', 's.s@hotmail.com', '2026-12-12', '.....', '2025-12-22 20:36:58');

-- --------------------------------------------------------

--
-- Table structure for table `user_photography`
--

CREATE TABLE `user_photography` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_date` date DEFAULT NULL,
  `session_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `name`, `category`, `contact_info`, `description`, `created_at`) VALUES
(1, 'Gourmet Delights', 'Food', 'contact@gourmet.com', 'Premium catering service with international cuisines.', '2025-12-22 17:23:54'),
(2, 'Floral Dreams', 'Decoration', 'info@floraldreams.com', 'Bespoke floral arrangements for luxury weddings.', '2025-12-22 17:23:54'),
(3, 'Shot with Love', 'Photography', 'hello@shotwithlove.com', 'Candid wedding photography and cinematography.', '2025-12-22 17:23:54');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT 'My Wedding Video',
  `video_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wedding_events`
--

CREATE TABLE `wedding_events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL DEFAULT 'My Wedding',
  `event_date` date DEFAULT NULL,
  `event_location` text DEFAULT NULL,
  `rsvp_code` varchar(32) NOT NULL,
  `rsvp_deadline` date DEFAULT NULL,
  `max_guests` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wedding_events`
--

INSERT INTO `wedding_events` (`id`, `user_id`, `event_name`, `event_date`, `event_location`, `rsvp_code`, `rsvp_deadline`, `max_guests`, `created_at`, `updated_at`) VALUES
(1, 13, 'Wedding of zainab & Benjamin', '2028-09-27', 'San Francisco', '5591ca5b436d9b56', NULL, 0, '2025-12-23 19:33:09', '2025-12-23 19:35:32'),
(2, 13, 'Wedding of zainab & Benjamin', '2028-09-27', 'San Francisco', 'd48aa3ab991f863e', NULL, 0, '2025-12-23 19:35:39', '2025-12-23 19:35:39'),
(3, 13, 'Wedding of zainab & Benjamin', '2028-09-27', 'San Francisco', '7f02ac2a15c9f112', NULL, 0, '2025-12-23 19:39:41', '2025-12-23 19:39:41'),
(4, 13, 'Wedding of zainab & Benjamin', '2028-09-27', 'San Francisco', '868839559b3414e7', NULL, 0, '2025-12-23 19:43:30', '2025-12-23 19:43:30'),
(5, 13, 'Wedding of zainab & Benjamin', '2028-09-27', 'San Francisco', '903ed429a542fd1b', NULL, 0, '2025-12-23 19:43:35', '2025-12-23 19:43:35'),
(6, 13, 'Wedding of zainab & Benjamin', '2028-09-27', 'San Francisco', '576771c0605fcea5', NULL, 0, '2025-12-23 20:01:03', '2025-12-23 20:01:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `budget_categories`
--
ALTER TABLE `budget_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `budget_expenses`
--
ALTER TABLE `budget_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `budget_items`
--
ALTER TABLE `budget_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cakes`
--
ALTER TABLE `cakes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cake_menu`
--
ALTER TABLE `cake_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_package` (`package_type`);

--
-- Indexes for table `card_templates`
--
ALTER TABLE `card_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_package` (`package_type`);

--
-- Indexes for table `ceremonies`
--
ALTER TABLE `ceremonies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ceremony_photos`
--
ALTER TABLE `ceremony_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ceremony_id` (`ceremony_id`);

--
-- Indexes for table `food_menu`
--
ALTER TABLE `food_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_package` (`package_type`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `invitations`
--
ALTER TABLE `invitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `invitation_cards`
--
ALTER TABLE `invitation_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_user_id` (`from_user_id`),
  ADD KEY `to_user_id` (`to_user_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `photography_sessions`
--
ALTER TABLE `photography_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `planning_tips`
--
ALTER TABLE `planning_tips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rsvp_guests`
--
ALTER TABLE `rsvp_guests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_code` (`unique_code`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_unique_code` (`unique_code`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `rsvp_messages`
--
ALTER TABLE `rsvp_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_guest_id` (`guest_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_budget_settings`
--
ALTER TABLE `user_budget_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_budget` (`user_id`);

--
-- Indexes for table `user_cake_selections`
--
ALTER TABLE `user_cake_selections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_cake` (`user_id`,`cake_id`),
  ADD KEY `cake_menu_id` (`cake_id`);

--
-- Indexes for table `user_card_customizations`
--
ALTER TABLE `user_card_customizations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `card_template_id` (`card_template_id`);

--
-- Indexes for table `user_card_selection`
--
ALTER TABLE `user_card_selection`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_card` (`user_id`),
  ADD KEY `card_template_id` (`card_template_id`);

--
-- Indexes for table `user_decorations`
--
ALTER TABLE `user_decorations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_food_selections`
--
ALTER TABLE `user_food_selections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_food` (`user_id`,`food_menu_id`),
  ADD KEY `food_menu_id` (`food_menu_id`);

--
-- Indexes for table `user_music_preferences`
--
ALTER TABLE `user_music_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_music_selections`
--
ALTER TABLE `user_music_selections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_packages`
--
ALTER TABLE `user_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `user_photography`
--
ALTER TABLE `user_photography`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_photo` (`user_id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `wedding_events`
--
ALTER TABLE `wedding_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rsvp_code` (`rsvp_code`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_rsvp_code` (`rsvp_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `budget_categories`
--
ALTER TABLE `budget_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `budget_expenses`
--
ALTER TABLE `budget_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `budget_items`
--
ALTER TABLE `budget_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cakes`
--
ALTER TABLE `cakes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `cake_menu`
--
ALTER TABLE `cake_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `card_templates`
--
ALTER TABLE `card_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ceremonies`
--
ALTER TABLE `ceremonies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ceremony_photos`
--
ALTER TABLE `ceremony_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `food_menu`
--
ALTER TABLE `food_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invitations`
--
ALTER TABLE `invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invitation_cards`
--
ALTER TABLE `invitation_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `photography_sessions`
--
ALTER TABLE `photography_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `planning_tips`
--
ALTER TABLE `planning_tips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rsvp_guests`
--
ALTER TABLE `rsvp_guests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rsvp_messages`
--
ALTER TABLE `rsvp_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_budget_settings`
--
ALTER TABLE `user_budget_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_cake_selections`
--
ALTER TABLE `user_cake_selections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_card_customizations`
--
ALTER TABLE `user_card_customizations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_card_selection`
--
ALTER TABLE `user_card_selection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_decorations`
--
ALTER TABLE `user_decorations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_food_selections`
--
ALTER TABLE `user_food_selections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `user_music_preferences`
--
ALTER TABLE `user_music_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_music_selections`
--
ALTER TABLE `user_music_selections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_packages`
--
ALTER TABLE `user_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_photography`
--
ALTER TABLE `user_photography`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wedding_events`
--
ALTER TABLE `wedding_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budget_categories`
--
ALTER TABLE `budget_categories`
  ADD CONSTRAINT `budget_categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budget_expenses`
--
ALTER TABLE `budget_expenses`
  ADD CONSTRAINT `budget_expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budget_expenses_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `budget_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ceremonies`
--
ALTER TABLE `ceremonies`
  ADD CONSTRAINT `ceremonies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ceremony_photos`
--
ALTER TABLE `ceremony_photos`
  ADD CONSTRAINT `ceremony_photos_ibfk_1` FOREIGN KEY (`ceremony_id`) REFERENCES `ceremonies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gallery`
--
ALTER TABLE `gallery`
  ADD CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guests`
--
ALTER TABLE `guests`
  ADD CONSTRAINT `guests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invitations`
--
ALTER TABLE `invitations`
  ADD CONSTRAINT `invitations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `photography_sessions`
--
ALTER TABLE `photography_sessions`
  ADD CONSTRAINT `photography_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rsvp_guests`
--
ALTER TABLE `rsvp_guests`
  ADD CONSTRAINT `rsvp_guests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rsvp_guests_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `wedding_events` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rsvp_messages`
--
ALTER TABLE `rsvp_messages`
  ADD CONSTRAINT `rsvp_messages_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `rsvp_guests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rsvp_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_budget_settings`
--
ALTER TABLE `user_budget_settings`
  ADD CONSTRAINT `user_budget_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_cake_selections`
--
ALTER TABLE `user_cake_selections`
  ADD CONSTRAINT `user_cake_selections_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_card_customizations`
--
ALTER TABLE `user_card_customizations`
  ADD CONSTRAINT `fk_card_template` FOREIGN KEY (`card_template_id`) REFERENCES `invitation_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_card` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_card_selection`
--
ALTER TABLE `user_card_selection`
  ADD CONSTRAINT `user_card_selection_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_card_selection_ibfk_2` FOREIGN KEY (`card_template_id`) REFERENCES `card_templates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_decorations`
--
ALTER TABLE `user_decorations`
  ADD CONSTRAINT `user_decorations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_food_selections`
--
ALTER TABLE `user_food_selections`
  ADD CONSTRAINT `user_food_selections_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_food_selections_ibfk_2` FOREIGN KEY (`food_menu_id`) REFERENCES `food_menu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_music_preferences`
--
ALTER TABLE `user_music_preferences`
  ADD CONSTRAINT `user_music_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_music_selections`
--
ALTER TABLE `user_music_selections`
  ADD CONSTRAINT `user_music_selections_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_packages`
--
ALTER TABLE `user_packages`
  ADD CONSTRAINT `user_packages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_packages_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_photography`
--
ALTER TABLE `user_photography`
  ADD CONSTRAINT `user_photography_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wedding_events`
--
ALTER TABLE `wedding_events`
  ADD CONSTRAINT `wedding_events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
