<?php

// Hiển thị lỗi (chỉ dùng khi debug)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Khởi tạo session
require_once "../config/session.php";

// Kết nối database
require_once "../config/database.php";

// Định tuyến (sử dụng routes.php để xử lý request)
require_once "../routes/routes.php";
