-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: pixel_vault
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `pixel_vault`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `pixel_vault` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `pixel_vault`;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Nhập khẩu','Băng game nhập khẩu từ Nhật, US, EU hoặc thị trường quốc tế','2026-05-18 06:42:54'),(2,'Nội địa','Băng game phát hành hoặc phân phối trong nước','2026-05-18 06:42:54'),(3,'Đặc biệt','Băng game hiếm, bản sưu tầm, bản giới hạn hoặc tình trạng đặc biệt','2026-05-18 06:42:54');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `genres`
--

DROP TABLE IF EXISTS `genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `genres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `genres`
--

LOCK TABLES `genres` WRITE;
/*!40000 ALTER TABLE `genres` DISABLE KEYS */;
INSERT INTO `genres` VALUES (1,'Action','Game hành động','2026-05-18 06:42:54'),(2,'Adventure','Game phiêu lưu','2026-05-18 06:42:54'),(3,'RPG','Game nhập vai','2026-05-18 06:42:54'),(4,'Metroidvania','Game khám phá bản đồ liên thông, mở khóa kỹ năng','2026-05-18 06:42:54'),(5,'Horror','Game kinh dị','2026-05-18 06:42:54'),(6,'Visual Novel','Game kể chuyện tương tác','2026-05-18 06:42:54'),(7,'Platformer','Game đi cảnh','2026-05-18 06:42:54'),(8,'Puzzle','Game giải đố','2026-05-18 06:42:54'),(9,'Racing','Game đua xe','2026-05-18 06:42:54'),(10,'Fighting','Game đối kháng','2026-05-18 06:42:54'),(11,'Strategy','Game chiến thuật','2026-05-18 06:42:54'),(12,'Simulation','Game mô phỏng','2026-05-18 06:42:54');
/*!40000 ALTER TABLE `genres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_genres`
--

DROP TABLE IF EXISTS `product_genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_genres` (
  `product_id` int NOT NULL,
  `genre_id` int NOT NULL,
  PRIMARY KEY (`product_id`,`genre_id`),
  KEY `fk_product_genres_genre` (`genre_id`),
  CONSTRAINT `fk_product_genres_genre` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_product_genres_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_genres`
--

LOCK TABLES `product_genres` WRITE;
/*!40000 ALTER TABLE `product_genres` DISABLE KEYS */;
INSERT INTO `product_genres` VALUES (1,1),(4,1),(9,1),(10,1),(3,2),(4,2),(9,2),(5,3),(6,3),(9,3),(10,3),(6,4),(10,4),(2,5),(4,5),(9,5),(2,6),(3,6),(5,6),(1,7),(2,8),(3,8),(5,8),(6,10),(10,10);
/*!40000 ALTER TABLE `product_genres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_slot` tinyint NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_product_image_slot` (`product_id`,`image_slot`),
  CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (1,5,'uploads/products/product_20260518_090047_65cba5a1.jpeg',1,1,'2026-05-18 09:00:47'),(2,5,'uploads/products/product_20260518_090047_73d14d57.jpeg',2,0,'2026-05-18 09:00:47'),(3,5,'uploads/products/product_20260518_090047_60e56f5c.jpeg',3,0,'2026-05-18 09:00:47'),(19,1,'uploads/products/product_20260513_030734_43f20732.png',1,1,'2026-05-18 06:56:22'),(20,1,'uploads/products/product_20260513_030734_618f8a21.png',2,0,'2026-05-18 06:56:22'),(21,1,'uploads/products/product_20260513_030734_7c4a4224.png',3,0,'2026-05-18 06:56:22'),(34,4,'uploads/products/product_20260513_035129_9511d469.png',1,1,'2026-05-18 07:03:29'),(35,4,'uploads/products/product_20260513_035129_4d54a1e8.png',2,0,'2026-05-18 07:03:29'),(36,4,'uploads/products/product_20260513_035129_ff62b55e.png',3,0,'2026-05-18 07:03:29'),(40,3,'uploads/products/product_20260513_034155_c5ca9641.png',1,1,'2026-05-18 07:05:22'),(41,3,'uploads/products/product_20260513_034155_7a6fbae4.png',2,0,'2026-05-18 07:05:22'),(42,3,'uploads/products/product_20260513_034155_1ddab035.png',3,0,'2026-05-18 07:05:22'),(46,2,'uploads/products/product_20260513_033008_75deaa42.png',1,1,'2026-05-18 07:07:00'),(47,2,'uploads/products/product_20260513_033008_051dadaa.png',2,0,'2026-05-18 07:07:00'),(48,2,'uploads/products/product_20260513_033008_c144ff76.png',3,0,'2026-05-18 07:07:00'),(56,6,'uploads/products/product_20260518_073501_89ad3e2f.jpeg',1,1,'2026-05-18 07:35:31'),(57,6,'uploads/products/product_20260518_073531_ba1e9cce.jpeg',2,0,'2026-05-18 07:35:31'),(58,6,'uploads/products/product_20260518_073501_ebbc281c.jpeg',3,0,'2026-05-18 07:35:31'),(59,9,'uploads/products/product_20260518_081111_cbc6f375.jpeg',1,1,'2026-05-18 08:11:11'),(60,9,'uploads/products/product_20260518_081111_6b2604a1.jpeg',2,0,'2026-05-18 08:11:11'),(61,9,'uploads/products/product_20260518_081111_2cf22d0b.jpeg',3,0,'2026-05-18 08:11:11'),(62,10,'uploads/products/product_20260518_083109_de57c7c6.jpeg',1,1,'2026-05-18 08:31:09'),(63,10,'uploads/products/product_20260518_083109_b90fbfd0.jpeg',2,0,'2026-05-18 08:31:09'),(64,10,'uploads/products/product_20260518_083109_4869cb99.jpeg',3,0,'2026-05-18 08:31:09');
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `category_id` int DEFAULT NULL,
  `product_condition` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Đã qua sử dụng',
  `resolution` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '256 × 224',
  `rom_format` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '16MB ROM',
  `players` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1 người chơi',
  `region` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tự do',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_products_category` (`category_id`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Huyền thoại Pixel 1989','Huyền thoại Pixel 1989 là một tựa game phiêu lưu hành động 8-bit mang phong cách dark-fantasy retro. \r\n\r\nNgười chơi hóa thân thành chiến binh cuối cùng của một vương quốc pixel đã mục nát, bước qua hầm ngục bị nguyền rủa, lâu đài đổ nát và vùng đất phủ bóng tối để đối đầu với rồng xanh cổ đại — sinh vật được cho là nguồn gốc của lời nguyền đang nuốt chửng thế giới.',189000.00,2,'Mới','320 × 240','16MB ROM','1 người chơi','Nhật Bản, Việt Nam, Anh, Mỹ','2026-05-18 06:42:54','2026-05-18 06:56:22'),(2,'Pixel Vault: The Lost Save','Trong một hệ máy cũ, có một file save chưa từng được hoàn thành. Người chơi bước vào hành trình khôi phục ký ức của một nhân vật pixel bị mắc kẹt giữa các màn chơi đã hỏng. Mỗi vùng đất là một phần dữ liệu bị phân mảnh, chứa những câu chuyện buồn, những lựa chọn cũ và một cái kết đã bị giấu đi.',170000.00,2,'Đã qua sử dụng','256 × 240','16MB ROM','1 người chơi','US, Việt Nam, Nga','2026-05-18 06:42:54','2026-05-18 06:58:04'),(3,'Pixel Vault: Forgotten Arcade','Một tiệm arcade cũ xuất hiện vào lúc nửa đêm, chỉ mở cửa cho những người từng bỏ quên điều gì đó trong quá khứ. \r\n\r\nMỗi máy game bên trong là những ký ức rời rạc được mã hóa bằng pixel những con số và các câu đố hóc búa. \r\n\r\nNgười chơi phải vượt qua các trò chơi kỳ lạ để tìm ra lý do vì sao mình được chọn.',219000.00,1,'Mới','426 × 240','16MB ROM','1 người chơi','EU, Nhật Bản, Hàn Quốc','2026-05-18 06:42:54','2026-05-18 07:05:02'),(4,'Pixel Vault: Crimson ROM','Một file ROM màu đỏ đột nhiên xuất hiện trên mạng đi kèm với lời đồn rằng ai chơi đến màn cuối sẽ nhìn thấy “sự thật phía sau thế giới pixel”.\r\n\r\nBạn chỉ là một trong số những người chơi tò mò tải nó về và bị kéo vào một cuộc phiêu lưu kỳ lạ, nơi ranh giới giữa game, ký ức và thực tại dần tan chảy.',199000.00,1,'Đã qua sử dụng','320 × 240','16MB ROM','2 người chơi','Nga, Singapore, US, UK','2026-05-18 06:42:54','2026-05-18 07:03:29'),(5,'Pixel Vault: The Eiffel Rose','Tại Paris hoa lệ, trước khi qua đời, một họa sĩ già bí ẩn đã để lại tác phẩm cuối cùng của mình — một bức tranh pixel nổi tiếng mô tả tháp Eiffel nở rộ giữa những đóa hồng gai dưới bầu trời sao, cùng hình bóng một cô gái không ai rõ danh tính.\r\n\r\nBức tranh nhanh chóng trở thành kiệt tác, được trưng bày tại nhiều bảo tàng danh tiếng, rồi đột ngột biến mất không để lại bất kỳ dấu vết nào. Nhiều năm sau, nó bất ngờ xuất hiện trở lại tại hiện trường của một vụ án mạng kỳ lạ: dưới bầu trời sao, cạnh thi thể lạnh ngắt của một người quản gia và giữa những cánh hồng gai nhuốm máu.\r\n\r\nNgười chơi vào vai một thám tử tập sự bị cuốn vào vụ án bí ẩn ấy. Lần theo những manh mối ẩn sau bức tranh thất lạc, bạn phải khám phá mối liên hệ giữa cô gái trong tranh, cái chết của người quản gia và bí mật đen tối đang ẩn mình sau vẻ đẹp lộng lẫy của Paris — trước khi thành phố ấy một lần nữa bị nhuộm đỏ bởi những cánh hoa hồng.',169000.00,1,'Đã qua sử dụng','320 × 240','8MB ROM','1 người chơi','Pháp, Việt Nam, US, UK','2026-05-18 09:00:47','2026-05-18 09:00:47'),(6,'Pixel Vault: Kingdom Washed Away','Từng có một vương quốc pixel rực rỡ tồn tại dưới sự ban phước của thực thể cổ đại mang tên The Unknown. Nhưng mọi thứ đã sụp đổ khi một màn mưa kỳ lạ kéo đến, cuốn trôi lịch sử, ký ức và sự sống, để lại phía sau một thế giới mục nát đang dần tan vào hư vô.\r\n\r\nNgười chơi vào vai một kẻ thất lạc không còn nhớ rõ quá khứ của mình, chỉ mang theo thanh kiếm gỉ sét và một chiếc nhẫn bị lãng quên. \r\n\r\nTrên hành trình băng qua những tàn tích bị mưa xóa nhòa, người chơi phải lần theo dấu vết của kẻ đã tạo ra cơn mưa, đồng thời khám phá sự thật đen tối phía sau The Unknown và lời nguyền đang nuốt chửng vương quốc.',245000.00,3,'Mới','426 × 240','24MB ROM','1 người chơi','Việt Nam, US, UK, Nhật Bản','2026-05-18 07:21:49','2026-05-18 07:21:49'),(9,'Pixel Vault: The Abyss','Pixel Vault: The Abyss là một cuộc thám hiểm dark-fantasy vào miệng hố khổng lồ nằm giữa một quốc đảo cổ. \r\n\r\nNgười chơi vào vai một nhà thám hiểm lún sâu vào The Abyss để trục vớt di vật thất lạc và chinh phục các tầng sâu trong truyền thuyết. \r\n\r\nNhưng càng đi xuống, lời nguyền càng trở nên rõ ràng: những kẻ chạm tới tầng 6 không thể trở về, và dưới đáy vực có thể không phải kho báu, mà là sự thật mà nhân loại chưa từng nên tìm thấy.',175000.00,3,'Mới','320 × 240','16MB ROM','1 người chơi','Nhật Bản, Việt Nam','2026-05-18 08:11:11','2026-05-18 08:11:11'),(10,'Pixel Vault: The Matrix','Pixel Vault: The Matrix lấy bối cảnh trong một thành phố cyber đen tối, nơi giá trị của con người không còn được đo bằng lòng nhân hậu, ký ức hay linh hồn, mà bằng những con số trên hợp đồng bạc tỷ của các tập đoàn công nghệ.\r\n\r\nNgười chơi vào vai một sát thủ cyborg chuyên thực hiện các nhiệm vụ thanh trừng, ám sát và dọn dẹp những bí mật bẩn thỉu cho các tập đoàn. \r\n\r\nNhưng sau một phi vụ thất bại, bạn bị chính những kẻ thuê mình phản bội, tiêm vào cơ thể một loại thuốc phá hủy hệ thần kinh nhân tạo, khiến bộ não cyborg vỡ vụn và tâm trí mắc kẹt trong một mê cung dữ liệu mang tên The Matrix.\r\n\r\nTỉnh dậy từ đống tàn dư của chính mình, bạn không còn là một cỗ máy hoàn hảo. Bộ não công nghệ của bạn giờ đầy lỗ hổng, lỗi hệ thống và những vết nứt không thể vá. Nhưng chính các lỗi đó lại trở thành vũ khí: bạn có thể dịch chuyển qua thành phố bằng glitch, xé rách các lớp bảo mật, bẻ cong không gian dữ liệu và xuất hiện ở những nơi không ai có thể truy vết.\r\n\r\nVới thanh katana, một tâm trí vụn vỡ và khả năng khai thác những lỗ hổng trong chính bộ não mình, bạn bắt đầu cuộc thanh toán đẫm máu chống lại các tập đoàn đã tạo ra, sử dụng rồi vứt bỏ bạn. Đêm nay, thành phố cyber sẽ không còn vận hành theo luật của chúng nữa.',230000.00,1,'Đã qua sử dụng','426 × 240','24MB ROM','1 người chơi','US, UK, Pháp','2026-05-18 08:31:09','2026-05-18 08:31:09');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-18 16:03:57
