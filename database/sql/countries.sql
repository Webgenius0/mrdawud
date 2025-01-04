-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2024 at 08:14 AM
-- Server version: 8.4.2
-- PHP Version: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `boiler_plate`
--

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint UNSIGNED NOT NULL,
  `iso` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nicename` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iso3` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numcode` smallint DEFAULT NULL,
  `phonecode` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `iso`, `name`, `nicename`, `iso3`, `numcode`, `phonecode`, `created_at`, `updated_at`) VALUES
(1, 'AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 4, 93, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(2, 'AL', 'ALBANIA', 'Albania', 'ALB', 8, 355, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(3, 'DZ', 'ALGERIA', 'Algeria', 'DZA', 12, 213, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(4, 'AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 16, 1684, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(5, 'AD', 'ANDORRA', 'Andorra', 'AND', 20, 376, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(6, 'AO', 'ANGOLA', 'Angola', 'AGO', 24, 244, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(7, 'AI', 'ANGUILLA', 'Anguilla', 'AIA', 660, 1264, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(8, 'AQ', 'ANTARCTICA', 'Antarctica', NULL, NULL, 0, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(9, 'AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', 28, 1268, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(10, 'AR', 'ARGENTINA', 'Argentina', 'ARG', 32, 54, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(11, 'AM', 'ARMENIA', 'Armenia', 'ARM', 51, 374, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(12, 'AW', 'ARUBA', 'Aruba', 'ABW', 533, 297, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(13, 'AU', 'AUSTRALIA', 'Australia', 'AUS', 36, 61, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(14, 'AT', 'AUSTRIA', 'Austria', 'AUT', 40, 43, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(15, 'AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 31, 994, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(16, 'BS', 'BAHAMAS', 'Bahamas', 'BHS', 44, 1242, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(17, 'BH', 'BAHRAIN', 'Bahrain', 'BHR', 48, 973, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(18, 'BD', 'BANGLADESH', 'Bangladesh', 'BGD', 50, 880, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(19, 'BB', 'BARBADOS', 'Barbados', 'BRB', 52, 1246, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(20, 'BY', 'BELARUS', 'Belarus', 'BLR', 112, 375, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(21, 'BE', 'BELGIUM', 'Belgium', 'BEL', 56, 32, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(22, 'BZ', 'BELIZE', 'Belize', 'BLZ', 84, 501, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(23, 'BJ', 'BENIN', 'Benin', 'BEN', 204, 229, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(24, 'BM', 'BERMUDA', 'Bermuda', 'BMU', 60, 1441, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(25, 'BT', 'BHUTAN', 'Bhutan', 'BTN', 64, 975, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(26, 'BO', 'BOLIVIA', 'Bolivia', 'BOL', 68, 591, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(27, 'BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', 70, 387, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(28, 'BW', 'BOTSWANA', 'Botswana', 'BWA', 72, 267, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(29, 'BV', 'BOUVET ISLAND', 'Bouvet Island', NULL, NULL, 0, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(30, 'BR', 'BRAZIL', 'Brazil', 'BRA', 76, 55, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(31, 'IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', NULL, NULL, 246, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(32, 'BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', 96, 673, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(33, 'BG', 'BULGARIA', 'Bulgaria', 'BGR', 100, 359, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(34, 'BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', 854, 226, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(35, 'BI', 'BURUNDI', 'Burundi', 'BDI', 108, 257, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(36, 'KH', 'CAMBODIA', 'Cambodia', 'KHM', 116, 855, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(37, 'CM', 'CAMEROON', 'Cameroon', 'CMR', 120, 237, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(38, 'CA', 'CANADA', 'Canada', 'CAN', 124, 1, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(39, 'CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 132, 238, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(40, 'KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 136, 1345, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(41, 'CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 140, 236, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(42, 'TD', 'CHAD', 'Chad', 'TCD', 148, 235, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(43, 'CL', 'CHILE', 'Chile', 'CHL', 152, 56, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(44, 'CN', 'CHINA', 'China', 'CHN', 156, 86, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(45, 'CX', 'CHRISTMAS ISLAND', 'Christmas Island', NULL, NULL, 61, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(46, 'CC', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', NULL, NULL, 672, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(47, 'CO', 'COLOMBIA', 'Colombia', 'COL', 170, 57, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(48, 'KM', 'COMOROS', 'Comoros', 'COM', 174, 269, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(49, 'CG', 'CONGO', 'Congo', 'COG', 178, 242, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(50, 'CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', 180, 242, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(51, 'CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 184, 682, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(52, 'CR', 'COSTA RICA', 'Costa Rica', 'CRI', 188, 506, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(53, 'CI', 'COTE D\'IVOIRE', 'Cote D\'Ivoire', 'CIV', 384, 225, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(54, 'HR', 'CROATIA', 'Croatia', 'HRV', 191, 385, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(55, 'CU', 'CUBA', 'Cuba', 'CUB', 192, 53, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(56, 'CY', 'CYPRUS', 'Cyprus', 'CYP', 196, 357, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(57, 'CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 203, 420, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(58, 'DK', 'DENMARK', 'Denmark', 'DNK', 208, 45, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(59, 'DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 262, 253, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(60, 'DM', 'DOMINICA', 'Dominica', 'DMA', 212, 1767, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(61, 'DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', 214, 1809, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(62, 'EC', 'ECUADOR', 'Ecuador', 'ECU', 218, 593, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(63, 'EG', 'EGYPT', 'Egypt', 'EGY', 818, 20, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(64, 'SV', 'EL SALVADOR', 'El Salvador', 'SLV', 222, 503, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(65, 'GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 226, 240, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(66, 'ER', 'ERITREA', 'Eritrea', 'ERI', 232, 291, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(67, 'EE', 'ESTONIA', 'Estonia', 'EST', 233, 372, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(68, 'ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 231, 251, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(69, 'FK', 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', 'FLK', 238, 500, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(70, 'FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', 234, 298, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(71, 'FJ', 'FIJI', 'Fiji', 'FJI', 242, 679, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(72, 'FI', 'FINLAND', 'Finland', 'FIN', 246, 358, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(73, 'FR', 'FRANCE', 'France', 'FRA', 250, 33, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(74, 'GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 254, 594, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(75, 'PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 258, 689, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(76, 'TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', NULL, NULL, 0, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(77, 'GA', 'GABON', 'Gabon', 'GAB', 266, 241, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(78, 'GM', 'GAMBIA', 'Gambia', 'GMB', 270, 220, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(79, 'GE', 'GEORGIA', 'Georgia', 'GEO', 268, 995, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(80, 'DE', 'GERMANY', 'Germany', 'DEU', 276, 49, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(81, 'GH', 'GHANA', 'Ghana', 'GHA', 288, 233, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(82, 'GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 292, 350, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(83, 'GR', 'GREECE', 'Greece', 'GRC', 300, 30, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(84, 'GL', 'GREENLAND', 'Greenland', 'GRL', 304, 299, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(85, 'GD', 'GRENADA', 'Grenada', 'GRD', 308, 1473, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(86, 'GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 312, 590, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(87, 'GU', 'GUAM', 'Guam', 'GUM', 316, 1671, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(88, 'GT', 'GUATEMALA', 'Guatemala', 'GTM', 320, 502, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(89, 'GN', 'GUINEA', 'Guinea', 'GIN', 324, 224, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(90, 'GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', 624, 245, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(91, 'GY', 'GUYANA', 'Guyana', 'GUY', 328, 592, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(92, 'HT', 'HAITI', 'Haiti', 'HTI', 332, 509, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(93, 'HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', NULL, NULL, 0, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(94, 'VA', 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', 'VAT', 336, 39, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(95, 'HN', 'HONDURAS', 'Honduras', 'HND', 340, 504, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(96, 'HK', 'HONG KONG', 'Hong Kong', 'HKG', 344, 852, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(97, 'HU', 'HUNGARY', 'Hungary', 'HUN', 348, 36, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(98, 'IS', 'ICELAND', 'Iceland', 'ISL', 352, 354, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(99, 'IN', 'INDIA', 'India', 'IND', 356, 91, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(100, 'ID', 'INDONESIA', 'Indonesia', 'IDN', 360, 62, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(101, 'IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', 364, 98, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(102, 'IQ', 'IRAQ', 'Iraq', 'IRQ', 368, 964, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(103, 'IE', 'IRELAND', 'Ireland', 'IRL', 372, 353, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(104, 'IL', 'ISRAEL', 'Israel', 'ISR', 376, 972, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(105, 'IT', 'ITALY', 'Italy', 'ITA', 380, 39, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(106, 'JM', 'JAMAICA', 'Jamaica', 'JAM', 388, 1876, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(107, 'JP', 'JAPAN', 'Japan', 'JPN', 392, 81, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(108, 'JO', 'JORDAN', 'Jordan', 'JOR', 400, 962, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(109, 'KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 398, 7, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(110, 'KE', 'KENYA', 'Kenya', 'KEN', 404, 254, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(111, 'KI', 'KIRIBATI', 'Kiribati', 'KIR', 296, 686, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(112, 'KP', 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'Korea, Democratic People\'s Republic of', 'PRK', 408, 850, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(113, 'KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', 410, 82, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(114, 'KW', 'KUWAIT', 'Kuwait', 'KWT', 414, 965, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(115, 'KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 417, 996, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(116, 'LA', 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'Lao People\'s Democratic Republic', 'LAO', 418, 856, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(117, 'LV', 'LATVIA', 'Latvia', 'LVA', 428, 371, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(118, 'LB', 'LEBANON', 'Lebanon', 'LBN', 422, 961, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(119, 'LS', 'LESOTHO', 'Lesotho', 'LSO', 426, 266, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(120, 'LR', 'LIBERIA', 'Liberia', 'LBR', 430, 231, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(121, 'LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', 434, 218, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(122, 'LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 438, 423, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(123, 'LT', 'LITHUANIA', 'Lithuania', 'LTU', 440, 370, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(124, 'LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 442, 352, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(125, 'MO', 'MACAO', 'Macao', 'MAC', 446, 853, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(126, 'MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807, 389, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(127, 'MG', 'MADAGASCAR', 'Madagascar', 'MDG', 450, 261, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(128, 'MW', 'MALAWI', 'Malawi', 'MWI', 454, 265, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(129, 'MY', 'MALAYSIA', 'Malaysia', 'MYS', 458, 60, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(130, 'MV', 'MALDIVES', 'Maldives', 'MDV', 462, 960, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(131, 'ML', 'MALI', 'Mali', 'MLI', 466, 223, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(132, 'MT', 'MALTA', 'Malta', 'MLT', 470, 356, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(133, 'MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 584, 692, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(134, 'MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 474, 596, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(135, 'MR', 'MAURITANIA', 'Mauritania', 'MRT', 478, 222, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(136, 'MU', 'MAURITIUS', 'Mauritius', 'MUS', 480, 230, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(137, 'YT', 'MAYOTTE', 'Mayotte', NULL, NULL, 269, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(138, 'MX', 'MEXICO', 'Mexico', 'MEX', 484, 52, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(139, 'FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', 583, 691, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(140, 'MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', 498, 373, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(141, 'MC', 'MONACO', 'Monaco', 'MCO', 492, 377, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(142, 'MN', 'MONGOLIA', 'Mongolia', 'MNG', 496, 976, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(143, 'MS', 'MONTSERRAT', 'Montserrat', 'MSR', 500, 1664, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(144, 'MA', 'MOROCCO', 'Morocco', 'MAR', 504, 212, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(145, 'MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 508, 258, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(146, 'MM', 'MYANMAR', 'Myanmar', 'MMR', 104, 95, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(147, 'NA', 'NAMIBIA', 'Namibia', 'NAM', 516, 264, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(148, 'NR', 'NAURU', 'Nauru', 'NRU', 520, 674, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(149, 'NP', 'NEPAL', 'Nepal', 'NPL', 524, 977, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(150, 'NL', 'NETHERLANDS', 'Netherlands', 'NLD', 528, 31, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(151, 'AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', 530, 599, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(152, 'NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 540, 687, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(153, 'NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 554, 64, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(154, 'NI', 'NICARAGUA', 'Nicaragua', 'NIC', 558, 505, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(155, 'NE', 'NIGER', 'Niger', 'NER', 562, 227, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(156, 'NG', 'NIGERIA', 'Nigeria', 'NGA', 566, 234, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(157, 'NU', 'NIUE', 'Niue', 'NIU', 570, 683, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(158, 'NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', 574, 672, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(159, 'MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', 580, 1670, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(160, 'NO', 'NORWAY', 'Norway', 'NOR', 578, 47, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(161, 'OM', 'OMAN', 'Oman', 'OMN', 512, 968, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(162, 'PK', 'PAKISTAN', 'Pakistan', 'PAK', 586, 92, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(163, 'PW', 'PALAU', 'Palau', 'PLW', 585, 680, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(164, 'PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', NULL, NULL, 970, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(165, 'PA', 'PANAMA', 'Panama', 'PAN', 591, 507, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(166, 'PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', 598, 675, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(167, 'PY', 'PARAGUAY', 'Paraguay', 'PRY', 600, 595, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(168, 'PE', 'PERU', 'Peru', 'PER', 604, 51, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(169, 'PH', 'PHILIPPINES', 'Philippines', 'PHL', 608, 63, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(170, 'PN', 'PITCAIRN', 'Pitcairn', 'PCN', 612, 0, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(171, 'PL', 'POLAND', 'Poland', 'POL', 616, 48, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(172, 'PT', 'PORTUGAL', 'Portugal', 'PRT', 620, 351, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(173, 'PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 630, 1787, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(174, 'QA', 'QATAR', 'Qatar', 'QAT', 634, 974, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(175, 'RE', 'REUNION', 'Reunion', 'REU', 638, 262, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(176, 'RO', 'ROMANIA', 'Romania', 'ROM', 642, 40, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(177, 'RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 643, 70, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(178, 'RW', 'RWANDA', 'Rwanda', 'RWA', 646, 250, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(179, 'SH', 'SAINT HELENA', 'Saint Helena', 'SHN', 654, 290, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(180, 'KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', 659, 1869, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(181, 'LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', 662, 1758, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(182, 'PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', 666, 508, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(183, 'VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', 670, 1784, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(184, 'WS', 'SAMOA', 'Samoa', 'WSM', 882, 684, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(185, 'SM', 'SAN MARINO', 'San Marino', 'SMR', 674, 378, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(186, 'ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', 678, 239, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(187, 'SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 682, 966, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(188, 'SN', 'SENEGAL', 'Senegal', 'SEN', 686, 221, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(189, 'CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', NULL, NULL, 381, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(190, 'SC', 'SEYCHELLES', 'Seychelles', 'SYC', 690, 248, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(191, 'SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 694, 232, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(192, 'SG', 'SINGAPORE', 'Singapore', 'SGP', 702, 65, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(193, 'SK', 'SLOVAKIA', 'Slovakia', 'SVK', 703, 421, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(194, 'SI', 'SLOVENIA', 'Slovenia', 'SVN', 705, 386, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(195, 'SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', 90, 677, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(196, 'SO', 'SOMALIA', 'Somalia', 'SOM', 706, 252, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(197, 'ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 710, 27, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(198, 'GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', NULL, NULL, 0, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(199, 'ES', 'SPAIN', 'Spain', 'ESP', 724, 34, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(200, 'LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 144, 94, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(201, 'SD', 'SUDAN', 'Sudan', 'SDN', 736, 249, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(202, 'SR', 'SURINAME', 'Suriname', 'SUR', 740, 597, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(203, 'SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', 744, 47, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(204, 'SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 748, 268, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(205, 'SE', 'SWEDEN', 'Sweden', 'SWE', 752, 46, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(206, 'CH', 'SWITZERLAND', 'Switzerland', 'CHE', 756, 41, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(207, 'SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', 760, 963, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(208, 'TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', 'TWN', 158, 886, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(209, 'TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 762, 992, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(210, 'TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', 834, 255, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(211, 'TH', 'THAILAND', 'Thailand', 'THA', 764, 66, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(212, 'TL', 'TIMOR-LESTE', 'Timor-Leste', NULL, NULL, 670, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(213, 'TG', 'TOGO', 'Togo', 'TGO', 768, 228, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(214, 'TK', 'TOKELAU', 'Tokelau', 'TKL', 772, 690, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(215, 'TO', 'TONGA', 'Tonga', 'TON', 776, 676, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(216, 'TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', 780, 1868, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(217, 'TN', 'TUNISIA', 'Tunisia', 'TUN', 788, 216, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(218, 'TR', 'TURKEY', 'Turkey', 'TUR', 792, 90, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(219, 'TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 795, 7370, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(220, 'TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', 796, 1649, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(221, 'TV', 'TUVALU', 'Tuvalu', 'TUV', 798, 688, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(222, 'UG', 'UGANDA', 'Uganda', 'UGA', 800, 256, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(223, 'UA', 'UKRAINE', 'Ukraine', 'UKR', 804, 380, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(224, 'AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 784, 971, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(225, 'GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 826, 44, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(226, 'US', 'UNITED STATES', 'United States', 'USA', 840, 1, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(227, 'UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', NULL, NULL, 1, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(228, 'UY', 'URUGUAY', 'Uruguay', 'URY', 858, 598, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(229, 'UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 860, 998, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(230, 'VU', 'VANUATU', 'Vanuatu', 'VUT', 548, 678, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(231, 'VE', 'VENEZUELA', 'Venezuela', 'VEN', 862, 58, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(232, 'VN', 'VIET NAM', 'Viet Nam', 'VNM', 704, 84, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(233, 'VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', 92, 1284, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(234, 'VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', 850, 1340, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(235, 'WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', 876, 681, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(236, 'EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', 732, 212, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(237, 'YE', 'YEMEN', 'Yemen', 'YEM', 887, 967, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(238, 'ZM', 'ZAMBIA', 'Zambia', 'ZMB', 894, 260, '2024-11-26 00:13:17', '2024-11-26 00:13:17'),
(239, 'ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 716, 263, '2024-11-26 00:13:17', '2024-11-26 00:13:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
