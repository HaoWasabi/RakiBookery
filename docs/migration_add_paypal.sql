-- ============================================================
-- Migration: Thêm PayPal vào bảng PaymentMethod
-- Chạy script này một lần trên database hiện tại
-- ============================================================

-- Thêm PayPal vào bảng PaymentMethod (bỏ qua nếu đã tồn tại)
INSERT IGNORE INTO `paymentmethod` (`Name`)
VALUES ('PayPal');

-- Kiểm tra kết quả
SELECT * FROM `paymentmethod`;
