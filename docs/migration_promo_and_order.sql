-- ============================================================
-- Migration: Tạo bảng Promo + thêm PromoID vào bảng Order
-- Chạy script này một lần trên database hiện tại
-- ============================================================

-- 1. Tạo bảng Promo (nếu chưa tồn tại)
--    Status: 1 = hiển thị (hoạt động), 0 = ẩn (soft delete)
--    Promo KHÔNG bao giờ bị hard delete để bảo toàn lịch sử đơn hàng.
CREATE TABLE IF NOT EXISTS `promo` (
    `PromoID`     INT          NOT NULL AUTO_INCREMENT,
    `Name`        VARCHAR(100) NOT NULL,
    `Discounted`  DECIMAL(5,2) NOT NULL COMMENT 'Phần trăm giảm giá (0-100)',
    `DateCreated` DATE         DEFAULT NULL,
    `Status`      TINYINT(1)   NOT NULL DEFAULT 1 COMMENT '1 = hiển thị, 0 = ẩn',
    PRIMARY KEY (`PromoID`),
    UNIQUE KEY `uq_promo_name` (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 2. Thêm cột PromoID vào bảng Order (bỏ qua nếu đã tồn tại)
--    Dùng RESTRICT thay vì SET NULL vì promo không bao giờ bị xóa thật.
--    Nếu muốn an toàn hơn vẫn có thể dùng ON DELETE SET NULL.
ALTER TABLE `order`
    ADD COLUMN IF NOT EXISTS `PromoID` INT NULL DEFAULT NULL AFTER `PaymentMethodID`,
    ADD CONSTRAINT `order_ibfk_4`
        FOREIGN KEY (`PromoID`) REFERENCES `promo` (`PromoID`)
        ON DELETE SET NULL ON UPDATE CASCADE;

-- 3. Thêm cột OriginalAmount để lưu tổng tiền trước khi áp dụng khuyến mãi
--    Nếu không có promo thì OriginalAmount = TotalAmount
ALTER TABLE `order`
    ADD COLUMN IF NOT EXISTS `OriginalAmount` DECIMAL(15,2) NULL DEFAULT NULL
        COMMENT 'Tổng tiền trước khi áp dụng khuyến mãi'
        AFTER `TotalAmount`;

-- Cập nhật các đơn hàng cũ: OriginalAmount = TotalAmount (chưa có promo)
UPDATE `order` SET `OriginalAmount` = `TotalAmount` WHERE `OriginalAmount` IS NULL;

-- 4. Dữ liệu mẫu
INSERT IGNORE INTO `promo` (`Name`, `Discounted`, `DateCreated`, `Status`) VALUES
    ('SALE10',   10, CURDATE(), 1),
    ('SALE20',   20, CURDATE(), 1),
    ('SUMMER30', 30, CURDATE(), 1);
