-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.34 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for bookstoredb
CREATE DATABASE IF NOT EXISTS `bookstoredb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `bookstoredb`;

-- Dumping structure for table bookstoredb.address
CREATE TABLE IF NOT EXISTS `address` (
  `AddressID` int NOT NULL AUTO_INCREMENT,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `District` varchar(100) DEFAULT NULL,
  `Ward` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`AddressID`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bookstoredb.address: ~17 rows (approximately)
REPLACE INTO `address` (`AddressID`, `Address`, `City`, `District`, `Ward`) VALUES
	(1, '123 Đường ABC', 'Hà Nội', 'Ba Đình', 'Phúc Xá'),
	(2, '456 Đường XYZ', 'TP. Hồ Chí Minh', 'Quận 1', 'Bến Nghé'),
	(3, '789 Đường DEF', 'Đà Nẵng', 'Hải Châu', 'Hòa Thuận Tây'),
	(4, '101 Đường GHI', 'Cần Thơ', 'Ninh Kiều', 'Tân An'),
	(5, '202 Đường JKL', 'Hải Phòng', 'Lê Chân', 'An Biên'),
	(7, '273 An Dương Vương', 'TP. Hồ Chí Minh', 'Quận 5', 'Phường 2'),
	(8, 'An Duong Vuong', 'TPHCM', 'Q5', 'P2'),
	(9, '789 Đường DEC', 'Cần Thơ', 'Ninh Kiều', 'Tân An'),
	(10, '101 Đường GHA', 'Cần Thơ', 'Ninh Kiều', 'Tân An'),
	(11, '789 Đường DEFH', 'Đà Nẵng', 'Hải Châu', 'Hòa Thuận Tây'),
	(15, '789 Đường DEFG', 'Đà Nẵng', 'Hải Châu', 'Hòa Thuận Tây'),
	(16, '7897 Đường DEC', 'Cần Thơ', 'Ninh Kiều', 'Tân An'),
	(17, 'xin chao', 'viet nam', ' viet nam', 'vietnam'),
	(18, '202 Đường JKLA', 'Hải Phòng', 'Lê Chân', 'Phường 2'),
	(19, 'Viet Nam', 'Viet Nam', 'Viet Nam', 'Viet Nam'),
	(20, '202 Đường JKLJ', 'Hải Phòng', 'Lê Chân', 'Phường 2'),
	(21, '202 Đường JKLG', 'Hải Phòng', 'Lê Chân', 'Phường 2'),
	(22, 'ahihi', 'hiihi', 'hehe', 'hihi');

-- Dumping structure for table bookstoredb.book
CREATE TABLE IF NOT EXISTS `book` (
  `BookID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Description` text,
  `Price` decimal(10,2) NOT NULL,
  `Stock` int NOT NULL,
  `ImageURL` varchar(255) DEFAULT NULL,
  `CategoryID` int NOT NULL,
  `Length` float DEFAULT NULL,
  `Weight` float DEFAULT NULL,
  `Dimensions` varchar(100) DEFAULT NULL,
  `Language` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Format` varchar(50) DEFAULT NULL,
  `Author` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Publisher` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ReleaseDate` date NOT NULL,
  `Status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`BookID`),
  KEY `CategoryID` (`CategoryID`),
  CONSTRAINT `book_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `category` (`CategoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bookstoredb.book: ~20 rows (approximately)
REPLACE INTO `book` (`BookID`, `Name`, `Description`, `Price`, `Stock`, `ImageURL`, `CategoryID`, `Length`, `Weight`, `Dimensions`, `Language`, `Format`, `Author`, `Publisher`, `ReleaseDate`, `Status`) VALUES
	(1, 'They Both Die At The End', '"They Both Die at the End" là một tiểu thuyết dành cho thanh thiếu niên của Adam Silvera, xoay quanh chủ đề cuộc sống, cái chết và những kết nối ý nghĩa. Câu chuyện diễn ra trong một thế giới nơi một dịch vụ tên là Death-Cast thông báo cho mọi người biết ngày họ sẽ chết. Vào cùng một ngày, hai người xa lạ—Mateo và Rufus—nhận được cuộc gọi định mệnh và kết nối với nhau qua một ứng dụng có tên Last Friend. Trong ngày cuối cùng của cuộc đời, họ dần trở nên thân thiết và học cách sống trọn vẹn từng khoảnh khắc còn lại.', 100000.00, 50, '../../img/They Both Die At The End.jpg', 1, 20, 0.5, '19.8 x 13.2 x 2.5 cm', 'tiếng Anh', 'Bìa mềm', 'Adam Silvera', 'Quill Tree Books', '2018-12-18', 1),
	(2, 'The Song Of Achilles', 'The Song of Achilles (Khúc Ca của Achilles) của Madeline Miller là một phiên bản kể lại câu chuyện về Achilles và Patroclus trong thần thoại Hy Lạp. Bối cảnh diễn ra trong cuộc chiến thành Troy, tiểu thuyết khắc họa mối quan hệ ngày càng sâu sắc giữa hai nhân vật từ khi còn nhỏ cho đến lúc trưởng thành. Với văn phong giàu cảm xúc và lối viết trữ tình, cuốn sách tái hiện một cách sống động câu chuyện vượt thời gian về tình yêu, danh dự, định mệnh và sự hy sinh.', 150000.00, 30, '../../img/The Song of Achilles.jpg', 1, 22, 0.3, '198 x 129 mm', 'Tiếng Anh', 'Bìa mềm', 'Madeline Miller', 'Bloomsbury Publishing', '2017-09-21', 1),
	(3, 'The Desolations of Devils Acre', 'The Desolations of Devils Acre là cuốn sách thứ sáu và cũng là phần kết của series Miss Peregrines Peculiar Children. Trong phần này, Jacob Portman và những người bạn đặc biệt của mình phải đối mặt với kẻ thù nguy hiểm và chạy đua qua những vòng lặp thời gian nguy hiểm nhất trong lịch sử.', 200000.00, 20, '../../img/TDOTDA.jpg', 1, 25, 0.7, '13.97 x 4.29 x 21.59 cm', 'Tiếng Anh', 'Bìa cứng', 'Ransom Riggs', 'Dutton Books for Young Readers', '2021-02-23', 1),
	(4, 'That Day the Rabbi Left Town', 'That Day the Rabbi Left Town là tập thứ mười hai và cũng là phần kết thúc của loạt truyện trinh thám nổi tiếng Rabbi Small Mysteries. Sau khi từ chức tại giáo đường Do Thái ở Barnard’s Crossing, Rabbi David Small nhận lời mời làm Giáo sư Nghiên cứu Do Thái tại Đại học Windermere ở Boston. Trong kỳ nghỉ Lễ Tạ Ơn đầy tuyết, một vị giáo sư lớn tuổi người Anh đột nhiên mất tích và sau đó được phát hiện đã qua đời. Cái chết ban đầu được cho là do đau tim, nhưng Rabbi Small nghi ngờ có điều mờ ám và bắt đầu dấn thân vào cuộc điều tra — một lần nữa sử dụng trí tuệ sắc bén và sự hiểu biết sâu sắc về con người để làm sáng tỏ sự thật.', 120000.00, 40, '../../img/TDTRLT.jpg', 1, 18, 0.1, '11.43 x 2.54 x 17.78 cm', 'Tiếng Anh', 'Bìa mềm', 'Harry Kemelman', 'Fawcett Books', '1997-01-01', 1),
	(5, 'Eight Perfect Murders', 'Eight Perfect Murders là một tiểu thuyết trinh thám kể về Malcolm Kershaw, chủ hiệu sách và người đam mê tiểu thuyết trinh thám. Nhiều năm trước, Malcolm đã viết một bài blog liệt kê tám vụ giết người hoàn hảo trong văn học, bao gồm các tác phẩm của Agatha Christie và Patricia Highsmith. Bất ngờ, FBI tiếp cận Malcolm khi một kẻ giết người hàng loạt bắt đầu thực hiện các vụ án dựa trên danh sách của anh. Cuốn sách là một sự tôn vinh thể loại trinh thám cổ điển và mang đến nhiều khúc ngoặt bất ngờ.', 180000.00, 25, '../../img/Eight Perfect Murders.jpg', 1, 21, 0.2, '13.49 x 1.73 x 20.32 cm', 'Tiếng Anh', 'Bìa mềm', 'Peter Swanson', 'William Morrow Paperbacks', '2021-02-02', 1),
	(6, 'Tokyo Ghoul Monster Edition, Volume 3', 'Tokyo Ghoul Monster Edition, Volume 3 là phiên bản đặc biệt kết hợp ba tập 7, 8 và 9 của loạt manga nổi tiếng Tokyo Ghoul. Phiên bản này được in trên giấy khổ lớn, giúp người đọc thưởng thức chi tiết nghệ thuật của Sui Ishida một cách rõ nét hơn. Câu chuyện tiếp tục theo chân Ken Kaneki, một sinh viên đại học bình thường bị biến thành nửa người nửa Ghoul sau một tai nạn kinh hoàng. Anh phải đấu tranh để tồn tại giữa hai thế giới và khám phá bản chất thực sự của mình.', 100000.00, 50, '../../img/tokyoGhoulMonsterEdition.jpg', 1, 20, 0.3, '25x17 cm', 'Tiếng Anh', 'Bìa cứng', 'Sui Ishida', 'VIZ Media', '2019-10-15', 1),
	(7, 'The Other Story', 'The Overstory là một tác phẩm đồ sộ, đầy cảm xúc về chủ nghĩa hoạt động và sự kháng cự, đồng thời là bản ngợi ca đầy mê hoặc dành cho thế giới tự nhiên. Từ rễ cây đến tán lá, rồi quay lại hạt giống, cuốn tiểu thuyết thứ mười hai của Richard Powers mở ra qua những vòng tròn đồng tâm với các câu chuyện ngụ ngôn đan xen — kéo dài từ thời kỳ tiền nội chiến ở New York đến các cuộc "Chiến tranh gỗ" cuối thế kỷ 20 tại vùng Tây Bắc Thái Bình Dương, và còn xa hơn nữa. Cuốn sách khám phá một thế giới tồn tại song song với thế giới con người — rộng lớn, chậm rãi, kết nối sâu sắc, đầy sáng tạo và gần như vô hình đối với chúng ta. Đây là câu chuyện về một nhóm người học cách nhìn thấy thế giới đó và bị cuốn vào thảm họa đang diễn ra trong lòng tự nhiên.', 150000.00, 30, '../../img/The Over Story.jpg', 4, 22, 0.4, '198 x 130 x 40 mm', 'Tiếng Anh', 'Bìa mềm', 'Richard Powers', 'Penguin', '2024-08-29', 1),
	(8, 'The Four Winds', 'The Four Winds là một tiểu thuyết lịch sử của Kristin Hannah, lấy bối cảnh thời kỳ Đại Suy Thoái và hiện tượng Dust Bowl ở Mỹ. Câu chuyện xoay quanh Elsa Martinelli, một phụ nữ sống ở Texas năm 1934, khi hạn hán và bão bụi tàn phá vùng Great Plains. Elsa phải đối mặt với quyết định khó khăn: ở lại và chiến đấu cho mảnh đất của mình hoặc di cư về phía tây đến California để tìm kiếm một cuộc sống tốt đẹp hơn. Cuốn tiểu thuyết khắc họa sâu sắc sự kiên cường của con người trong những thời kỳ khó khăn nhất và là một bức chân dung cảm động về Giấc mơ Mỹ.', 200000.00, 20, '../../img/The Four Winds.jpg', 1, 25, 0.6, '24.13 x 16.26 x 3.81 cm', 'Tiếng Anh', 'Bìa cứng', 'Kristin Hannah', 'St.Martins Press', '2021-02-02', 1),
	(9, 'Kinh tế Việt Nam: Thăng trầm và đột phá', 'Cuốn sách này phân tích những giai đoạn thăng trầm của nền kinh tế Việt Nam từ sau Đổi Mới đến nay, đồng thời đề xuất những giải pháp đột phá nhằm thúc đẩy tăng trưởng kinh tế bền vững trong tương lai.', 120000.00, 40, '../../img/kinh-te-vn-thang-tram-va-dot-pha.jpg', 3, 18, 0.5, '16 x 24 cm', 'Tiếng Việt', 'Bìa mềm', 'Nguyễn Văn Hiền', 'Nhà Xuất Bản Chính Trị Quốc Gia Sự Thật', '2009-05-29', 1),
	(10, 'phương pháp dạy học tích cực', 'Cuốn sách này giới thiệu các phương pháp dạy học tích cực nhằm nâng cao hiệu quả giảng dạy và phát huy tính chủ động của học sinh trong quá trình học tập.', 180000.00, 25, '../../img/phuong-phap-day-hoc-tich-cuc.jpg', 4, 21, 0.5, '16 x 24 cm', 'Tiếng Việt', 'Bìa mềm', 'Nguyễn Lang Bình', 'Nhà xuất bản Đại Học Sư Phạm Hà Nội', '2022-05-20', 1),
	(11, 'Hoàng Tử bé', '"Hoàng Tử Bé" (Le Petit Prince) là một tác phẩm kinh điển của nhà văn người Pháp Antoine de Saint-Exupéry, được viết vào năm 1943. Cuốn sách kể về cuộc gặp gỡ giữa một phi công bị mắc kẹt trong sa mạc Sahara và một cậu hoàng tử đến từ tiểu hành tinh B612. Qua những cuộc trò chuyện giữa họ, tác phẩm truyền tải những triết lý sâu sắc về tình bạn, tình yêu và ý nghĩa của cuộc sống.', 100000.00, 50, '../../img/HoangTuBe.jpg', 5, 20, 0.2, '14 x 20.5 cm', 'Tiếng Việt', 'Bìa cứng', 'Antoine de Saint-Exupéry', 'Nhà xuất bản Thanh niên', '2022-01-01', 1),
	(12, 'Có một con mọt sách', 'Cuốn sách này chứa đựng những câu chuyện cổ tích được bác sĩ Đỗ Hồng Ngọc viết lại một cách sáng tạo và độc đáo. Lồng vào những câu chuyện ngộ nghĩnh là lời khuyên vui nhộn, nhẹ nhàng nhắc nhở các bé về việc giữ mắt khỏe để không cận thị, giữ gìn vệ sinh và sức khỏe cơ thể, tránh nhiễm giun sán và phòng tránh những tai nạn thường gặp.', 150000.00, 30, '../../img/co-mot-con-mot-sach.jpg', 5, 22, 0.1, '18.5 x 18.5 x 0.4 cm', 'Tiếng Việt', 'Bìa mềm', 'Đỗ Hồng Ngọc', 'NXB Tổng hợp thành phố Hồ Chí Minh', '2021-02-15', 1),
	(13, 'Truyện hay cho bé tập 3', 'Cuốn sách tập hợp những câu chuyện được lựa chọn kỹ lưỡng, phù hợp với trẻ em từ 0 đến 6 tuổi, giúp các bé phát triển khả năng ngôn ngữ, tư duy và cảm xúc. Những câu chuyện trong sách không chỉ mang tính giải trí mà còn chứa đựng những bài học giáo dục nhẹ nhàng, giúp trẻ hiểu hơn về thế giới xung quanh và các giá trị đạo đức cơ bản.', 200000.00, 20, '../../img/Truyen-hay-cho-be-tap-3.jpg', 5, 25, 0.1, '20 x 20 cm', 'Tiếng Việt', 'Bìa mềm', 'Hà Nhi', 'NXB Phụ nữ Việt Nam', '2020-04-01', 1),
	(14, 'Bản thiết kế vĩ đại (the Grand Design)', 'Cuốn sách khám phá lịch sử tri thức khoa học về vũ trụ, bắt đầu từ các triết gia Hy Lạp cổ đại đến các nhà khoa học hiện đại. Hawking và Mlodinow trình bày lý thuyết M-đa chiều, một nỗ lực nhằm thống nhất các lý thuyết vật lý hiện có để giải thích sự hình thành và bản chất của vũ trụ.', 120000.00, 40, '../../img/ban-thiet-ke-vi-dai.jpg', 2, 18, 0.3, '14 x 20 cm', 'Tiếng Việt', 'Bìa mềm', 'Stephen Hawking, Leonard Mlodinow', 'NXB Trẻ', '2010-04-05', 1),
	(15, 'Dẫn nhập ngắn về khoa học - Trí tuệ nhân tạo', 'Dẫn nhập ngắn về khoa học là lựa chọn sáng suốt nhất cho bất kỳ ai muốn truy cập nhanh và hiệu quả vào kho kiến thức khổng lồ. Khai thác thông tin khoa học khách quan từ những nguồn xác tín và sử dụng phong cách truyện tranh hài hước, bộ sách độc đáo này sẽ mở ra con đường sáng rõ nhất để tìm đến những ý tưởng khoa học đột phá của nhân loại.', 180000.00, 25, '../../img/Tri-tue-nhan-tao.jpg', 2, 21, 0.6, '22x15 cm', 'Tiếng Việt', 'Bìa mềm', 'Henry Brighton, Howard Selina', 'NXB Dân Trí', '2020-04-04', 1),
	(16, 'Triết học khoa học', 'Cuốn sách này tập trung vào việc trình bày các quan điểm khác nhau trong triết học khoa học, từ đó giúp độc giả hiểu rõ hơn về phương pháp luận nghiên cứu khoa học, sự phát triển của khoa học và mối quan hệ giữa khoa học và triết học.', 100000.00, 50, '../../img/triet-hoc-khoa-hoc.jpg', 2, 20, 0.4, '13.5 x 21 cm', 'Tiếng Việt', 'Bìa mềm', 'Đỗ Anh Thơ', 'NXB Tri Thức', '2019-01-01', 1),
	(17, 'The Psychopath: a true story', 'Cuốn sách kể về trải nghiệm cá nhân của Mary Turner Thomson khi phát hiện ra rằng chồng mình, Will Jordan, là một kẻ lừa đảo chuyên nghiệp, đã kết hôn với nhiều phụ nữ và có nhiều con với họ mà không ai hay biết về nhau. Sau khi phát hiện sự thật đau lòng này, Mary quyết định chia sẻ câu chuyện của mình để cảnh báo và bảo vệ những phụ nữ khác khỏi những kẻ lừa đảo tương tự.', 150000.00, 30, '../../img/The-psychopath-a-true-story.jpg', 1, 22, 0, '13.97 x 2.54 x 20.96 cm', 'Tiếng Anh', 'Bìa mềm', 'Mary Turner Thomson', 'Little A', '2021-02-15', 1),
	(18, 'The midnight library', 'Cuốn tiểu thuyết kể về Nora Seed, một phụ nữ cảm thấy cuộc sống của mình đầy những hối tiếc và thất bại. Trong khoảnh khắc tuyệt vọng, cô phát hiện ra "Thư viện Nửa đêm" - một nơi kỳ diệu chứa vô số cuốn sách, mỗi cuốn mô tả một cuộc đời khác mà Nora có thể đã sống nếu đưa ra những lựa chọn khác nhau. Qua việc trải nghiệm những cuộc đời này, Nora khám phá ý nghĩa thực sự của hạnh phúc và sự hài lòng.', 200000.00, 20, '../../img/the-midnight-library.jpg', 1, 25, 0.2, '12.9 x 1.11 x 19.7 cm', 'Tiếng Anh', 'Bìa mềm', 'Matt Haig', 'Canongate Books', '2020-08-13', 1),
	(19, 'The Sanatorium', 'Câu chuyện diễn ra tại Le Sommet, một khách sạn sang trọng nằm biệt lập trên dãy núi Alps của Thụy Sĩ, từng là một trại điều dưỡng cho bệnh nhân lao. Nhân vật chính, Elin Warner, một thám tử đang trong kỳ nghỉ phép, đến đây để dự lễ đính hôn của anh trai mình. Tuy nhiên, khi vị hôn thê của anh trai cô mất tích và một cơn bão tuyết cô lập khách sạn, Elin buộc phải điều tra và đối mặt với những bí ẩn đen tối ẩn giấu trong quá khứ của Le Sommet.', 120000.00, 40, '../../img/The-Sanatorium.jpg', 1, 18, 0.5, '13.72 x 20.83 x 2.54 cm', 'Tiếng Anh', 'Bìa cứng', 'Sarah Pearse', 'Viking', '2020-01-01', 1),
	(20, 'Harry Potter and the Philosophers stone', 'Cuốn sách mở đầu cho loạt truyện về cậu bé phù thủy Harry Potter, theo chân Harry khi cậu khám phá ra thân phận thực sự của mình và bắt đầu học tại Trường Phù thủy và Pháp sư Hogwarts.', 180000.00, 500000, '../../img/harry-potter-and-the-philosopher-stone.jpg', 1, 21, 0.5, '13.2 x 3.6 x 20.1 cm', 'Tiếng Anh', 'Bìa cứng', 'J.K. Rowling', 'Bloomsbury Children', '2014-09-01', 1);

-- Dumping structure for table bookstoredb.category
CREATE TABLE IF NOT EXISTS `category` (
  `CategoryID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Description` text,
  PRIMARY KEY (`CategoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bookstoredb.category: ~5 rows (approximately)
REPLACE INTO `category` (`CategoryID`, `Name`, `Description`) VALUES
	(1, 'Văn học', 'Sách văn học, tiểu thuyết, truyện ngắn'),
	(2, 'Khoa học', 'Sách khoa học, công nghệ, kỹ thuật'),
	(3, 'Kinh tế', 'Sách về kinh doanh, tài chính'),
	(4, 'Giáo dục', 'Sách giáo trình, tham khảo, học thuật'),
	(5, 'Trẻ em', 'Sách thiếu nhi, truyện tranh');

-- Dumping structure for table bookstoredb.order
CREATE TABLE IF NOT EXISTS `order` (
  `OrderID` int NOT NULL AUTO_INCREMENT,
  `UserID` int DEFAULT NULL,
  `OrderDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `Status` enum('pending','confirmed','delivered_success','canceled') NOT NULL DEFAULT 'pending',
  `TotalAmount` decimal(10,2) NOT NULL,
  `AddressID` int DEFAULT NULL,
  `PaymentMethodID` int DEFAULT NULL,
  PRIMARY KEY (`OrderID`),
  KEY `UserID` (`UserID`),
  KEY `AddressID` (`AddressID`),
  KEY `PaymentMethodID` (`PaymentMethodID`),
  CONSTRAINT `order_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`),
  CONSTRAINT `order_ibfk_2` FOREIGN KEY (`AddressID`) REFERENCES `address` (`AddressID`),
  CONSTRAINT `order_ibfk_3` FOREIGN KEY (`PaymentMethodID`) REFERENCES `paymentmethod` (`PaymentMethodID`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bookstoredb.order: ~45 rows (approximately)
REPLACE INTO `order` (`OrderID`, `UserID`, `OrderDate`, `Status`, `TotalAmount`, `AddressID`, `PaymentMethodID`) VALUES
	(1, 1, '2025-04-10 11:50:24', 'delivered_success', 200000.00, 1, 1),
	(2, 6, '2025-04-10 11:50:24', 'delivered_success', 450000.00, 2, 2),
	(3, 6, '2024-04-08 11:50:24', 'delivered_success', 200000.00, 3, 3),
	(4, 6, '2024-05-09 11:50:24', 'delivered_success', 240000.00, 4, 4),
	(5, 6, '2025-04-10 11:50:24', 'delivered_success', 500000.00, 5, 5),
	(6, 6, '2024-12-11 12:17:36', 'delivered_success', 120000.00, 7, 1),
	(7, 6, '2025-04-11 12:48:59', 'delivered_success', 120000.00, 4, 3),
	(8, 6, '2025-04-11 12:52:55', 'delivered_success', 320000.00, 4, 1),
	(9, 6, '2025-04-11 13:05:42', 'delivered_success', 500000.00, 4, 1),
	(10, 6, '2025-03-11 13:06:05', 'delivered_success', 400000.00, 4, 1),
	(11, 6, '2025-03-11 13:07:19', 'delivered_success', 200000.00, 4, 1),
	(12, 5, '2025-01-11 13:08:21', 'delivered_success', 120000.00, 4, 2),
	(13, 6, '2025-02-11 13:14:05', 'delivered_success', 120000.00, 4, 1),
	(14, 1, '2025-04-11 13:58:16', 'delivered_success', 600000.00, 4, 1),
	(15, 9, '2025-04-11 14:18:52', 'delivered_success', 320000.00, 4, 1),
	(16, 5, '2025-04-11 14:21:53', 'delivered_success', 240000.00, 8, 3),
	(17, 8, '2025-04-11 15:47:23', 'delivered_success', 150000.00, 10, 1),
	(18, 8, '2025-04-11 17:01:30', 'confirmed', 120000.00, 9, 1),
	(19, 6, '2025-04-13 17:22:19', 'canceled', 480000.00, 11, 1),
	(20, 6, '2025-04-14 09:17:59', 'pending', 120000.00, 4, 1),
	(21, 6, '2025-04-14 09:19:49', 'pending', 120000.00, 4, 1),
	(22, 6, '2025-04-14 09:21:47', 'confirmed', 150000.00, 4, 1),
	(23, 6, '2025-04-15 09:24:52', 'pending', 120000.00, 4, 1),
	(24, 6, '2025-04-15 11:25:14', 'pending', 120000.00, 4, 1),
	(25, 6, '2025-04-16 01:31:25', 'canceled', 920000.00, 4, 1),
	(26, 6, '2025-04-16 01:40:25', 'delivered_success', 400000.00, 4, 1),
	(27, 6, '2025-04-16 10:21:13', 'pending', 240000.00, 4, 1),
	(28, 6, '2025-04-16 12:56:21', 'confirmed', 150000.00, 4, 1),
	(29, 6, '2025-04-17 10:11:55', 'confirmed', 360000.00, 4, 1),
	(30, 6, '2025-04-17 15:46:14', 'confirmed', 240000.00, 4, 1),
	(31, 6, '2025-04-18 08:50:58', 'confirmed', 360000.00, 4, 1),
	(32, 6, '2025-04-18 09:05:35', 'canceled', 180000.00, 16, 1),
	(33, 6, '2025-04-18 09:25:14', 'canceled', 120000.00, 17, 1),
	(34, 6, '2025-04-18 09:28:34', 'canceled', 200000.00, 4, 1),
	(35, 6, '2025-04-18 09:29:37', 'canceled', 400000.00, 4, 1),
	(36, 6, '2025-04-18 09:54:27', 'canceled', 850000.00, 18, 1),
	(37, 6, '2025-04-18 13:27:12', 'delivered_success', 360000.00, 4, 1),
	(38, 6, '2025-04-18 13:27:38', 'canceled', 120000.00, 19, 1),
	(39, 6, '2025-04-18 13:28:01', 'delivered_success', 150000.00, 4, 1),
	(40, 6, '2025-04-18 17:07:04', 'canceled', 180000.00, 4, 2),
	(41, 6, '2025-04-18 17:08:51', 'delivered_success', 120000.00, 4, 1),
	(42, 6, '2025-04-18 17:11:10', 'delivered_success', 200000.00, 4, 1),
	(43, 6, '2025-04-18 21:06:20', 'canceled', 180000.00, 4, 1),
	(44, 6, '2025-04-18 21:06:31', 'delivered_success', 200000.00, 21, 1),
	(45, 6, '2025-04-18 21:26:17', 'pending', 120000.00, 4, 1),
	(46, 6, '2025-04-18 23:21:56', 'pending', 1690000.00, 4, 1),
	(47, 6, '2025-04-18 23:37:31', 'pending', 720000.00, 4, 1),
	(48, 6, '2025-04-19 00:31:15', 'canceled', 0.00, 4, 1),
	(49, 6, '2025-04-19 00:41:32', 'canceled', 540000.00, 4, 1),
	(50, 6, '2025-04-19 05:25:55', 'pending', 840000.00, 4, 1),
	(51, 6, '2025-04-19 05:41:05', 'pending', 300000.00, 4, 4),
	(52, 35, '2025-04-19 05:43:59', 'pending', 400000.00, 22, 1);

-- Dumping structure for table bookstoredb.orderdetail
CREATE TABLE IF NOT EXISTS `orderdetail` (
  `OrderID` int NOT NULL,
  `ProductID` int NOT NULL,
  `Quantity` int NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`OrderID`,`ProductID`),
  KEY `ProductID` (`ProductID`),
  CONSTRAINT `orderdetail_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`),
  CONSTRAINT `orderdetail_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `book` (`BookID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bookstoredb.orderdetail: ~53 rows (approximately)
REPLACE INTO `orderdetail` (`OrderID`, `ProductID`, `Quantity`, `Price`) VALUES
	(1, 1, 2, 100000.00),
	(2, 2, 3, 150000.00),
	(3, 3, 1, 200000.00),
	(4, 4, 2, 120000.00),
	(5, 5, 3, 180000.00),
	(6, 19, 1, 120000.00),
	(7, 19, 1, 120000.00),
	(8, 14, 1, 120000.00),
	(8, 18, 1, 200000.00),
	(9, 18, 1, 200000.00),
	(9, 19, 1, 120000.00),
	(9, 20, 1, 180000.00),
	(10, 18, 2, 200000.00),
	(11, 18, 1, 200000.00),
	(12, 19, 1, 120000.00),
	(13, 19, 1, 120000.00),
	(14, 18, 3, 200000.00),
	(15, 13, 1, 200000.00),
	(15, 19, 1, 120000.00),
	(16, 9, 1, 120000.00),
	(16, 14, 1, 120000.00),
	(17, 17, 1, 150000.00),
	(18, 19, 1, 120000.00),
	(19, 19, 4, 120000.00),
	(20, 19, 1, 120000.00),
	(21, 19, 1, 120000.00),
	(22, 17, 1, 150000.00),
	(23, 19, 1, 120000.00),
	(24, 19, 1, 120000.00),
	(25, 18, 4, 200000.00),
	(25, 19, 1, 120000.00),
	(26, 18, 2, 200000.00),
	(27, 19, 2, 120000.00),
	(28, 17, 1, 150000.00),
	(29, 19, 3, 120000.00),
	(30, 19, 2, 120000.00),
	(31, 4, 3, 120000.00),
	(32, 20, 1, 180000.00),
	(33, 19, 1, 120000.00),
	(34, 18, 1, 200000.00),
	(35, 18, 2, 200000.00),
	(36, 17, 3, 150000.00),
	(36, 18, 2, 200000.00),
	(37, 15, 1, 180000.00),
	(37, 20, 1, 180000.00),
	(38, 4, 1, 120000.00),
	(39, 12, 1, 150000.00),
	(40, 20, 1, 180000.00),
	(41, 19, 1, 120000.00),
	(42, 18, 1, 200000.00),
	(43, 20, 1, 180000.00),
	(44, 18, 1, 200000.00),
	(45, 19, 1, 120000.00),
	(46, 13, 5, 200000.00),
	(46, 14, 2, 120000.00),
	(46, 17, 3, 150000.00),
	(47, 14, 1, 120000.00),
	(47, 18, 3, 200000.00),
	(49, 14, 2, 120000.00),
	(49, 17, 2, 150000.00),
	(50, 18, 3, 200000.00),
	(50, 19, 2, 120000.00),
	(51, 17, 2, 150000.00),
	(52, 15, 1, 180000.00),
	(52, 16, 1, 100000.00),
	(52, 19, 1, 120000.00);

-- Dumping structure for table bookstoredb.paymentmethod
CREATE TABLE IF NOT EXISTS `paymentmethod` (
  `PaymentMethodID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Description` text,
  PRIMARY KEY (`PaymentMethodID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bookstoredb.paymentmethod: ~5 rows (approximately)
REPLACE INTO `paymentmethod` (`PaymentMethodID`, `Name`, `Description`) VALUES
	(1, 'Tiền mặt', 'Khách hàng thanh toán tiền mặt khi nhận hàng'),
	(2, 'Thẻ tín dụng', 'Thanh toán bằng thẻ tín dụng Visa, MasterCard'),
	(3, 'Chuyển khoản ngân hàng', 'Khách hàng thanh toán qua tài khoản ngân hàng'),
	(4, 'Ví điện tử Momo', 'Thanh toán qua ví Momo'),
	(5, 'Ví điện tử ZaloPay', 'Thanh toán qua ví ZaloPay');

-- Dumping structure for table bookstoredb.user
CREATE TABLE IF NOT EXISTS `user` (
  `UserID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('user','admin') NOT NULL DEFAULT 'user',
  `Phone` varchar(20) DEFAULT NULL,
  `Status` tinyint(1) NOT NULL DEFAULT '1',
  `AddressID` int DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `Phone` (`Phone`),
  KEY `AddressID` (`AddressID`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`AddressID`) REFERENCES `address` (`AddressID`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bookstoredb.user: ~18 rows (approximately)
REPLACE INTO `user` (`UserID`, `Name`, `Email`, `Password`, `Role`, `Phone`, `Status`, `AddressID`) VALUES
	(1, 'Nguyễn Văn A', 'nguyenvana@example.com', 'hashed_password', 'user', '0123456789', 1, 1),
	(2, 'Trần Thị B', 'tranthib@example.com', 'hashed_password', 'user', '0987654321', 1, 2),
	(3, 'Lê Văn C', 'levanc@example.com', 'hashed_password', 'user', '0345678901', 1, 3),
	(4, 'Phạm Thị D', 'phamthid@example.com', 'hashed_password', 'user', '0765432109', 1, 4),
	(5, 'Hoàng Văn E', 'hoangvane@example.com', 'hashed_password', 'user', '0912345678', 1, 5),
	(6, 'Lee Chong Wei', 'wei@gmail.com', '$2y$10$b5QT87LravaaMkwwLV43cOhYUxS.iOPDca1CiGQO9A4/C56..DU.O', 'user', '0912345677', 1, 4),
	(7, 'Vegeta', 'Vegeta@gmail.com', '$2y$10$eTNemFhdthTvyE7MLJdrPeTr3heh/oSpxJFmyE07yvK0kUBeuKTkS', 'user', '+84012345678', 1, NULL),
	(8, 'Tui la hihi', '123456@gmail.com', '$2y$10$VTRm7XnUTE1KRX4O3nL3Ve7RzqEideJKpPeughiBhz94X7X48paMG', 'user', '0912345679', 1, 9),
	(9, 'Ahuhu', 'a@gmail.com', '$2y$10$ntgx.gapTVyougYza1Nbme4C39lAdA0xMIUWWRfULrDNbivJSr4d.', 'user', '0912345697', 1, NULL),
	(10, 'Admin', 'admin@gmail.com', '$2y$10$6qgVol2RfAsfkRk03.mltO0hUJxsBx3wly3bf8fov.Pr1EJnESI2y', 'admin', '0333666999', 1, NULL),
	(35, 'test', 'mra@gmail.com', '$2y$10$N8jJEqYpTjSHkzA.0RD1cu.uIx32KbMn.eOUFDcB5iFc7.nxJ.UdG', 'user', '0913245784', 1, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
