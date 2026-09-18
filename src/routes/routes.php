<?php

define('ROOT_PATH', dirname(__DIR__));

require_once "../app/controllers/CategoryController.php";
require_once "../app/controllers/OrderController.php";
require_once "../app/controllers/PromoController.php";
require_once "../app/controllers/StatisticsController.php";
require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/CategoryController.php";
require_once "../app/controllers/UserController.php";
require_once "../app/controllers/ClientController.php";
require_once "../app/controllers/AdminController.php";
require_once "../app/controllers/BookController.php";
require_once "../app/controllers/PayPalController.php";

$orderController = new OrderController();
$promoController = new PromoController();
$paypalController = new PayPalController();
$bookController = new BookController();
$statictisController = new StatisticsController();
$categoryController = new CategoryController();
$userController = new UserController();

$authController = new AuthController();

$clientController = new ClientController();
$adminController = new AdminController();

// Parse the URL path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Auth routes
if ($requestUri === "/admin/auth/login" || $requestUri === "/auth/login") {
    $authController->login();
} elseif ($requestUri === "/auth/register") {
    $authController->register();
} elseif ($requestUri === "/admin/logout" || $requestUri === "/logout") {
    $authController->logout();
} elseif ($requestUri === "/auth/get-cart-cookie") {
    $authController->getCartFromCookie();
}
// Category Controller routes
else if ($requestUri === "/api/categories/add") {
    $categoryController->addCategory();
} elseif ($requestUri === "/api/categories/update") {
    $categoryController->updateCategory();
}
// Order Controller routes
elseif ($requestUri === "/api/orders/filtered") {
    $orderController->getFilteredOrders();
} elseif ($requestUri === "/api/orders/recentOrders") {
    $orderController->getRecentOrders();
}
// Promo Controller routes
elseif ($requestUri === "/admin/promos") {
    $adminController->promos();
} elseif ($requestUri === "/admin/promos/add" && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $promoController->addPromo();
} elseif ($requestUri === "/admin/promos/add") {
    $adminController->addPromoPage();
} elseif ($requestUri === "/admin/promos/update") {
    $promoController->updatePromo();
} elseif ($requestUri === "/admin/promos/delete") {
    $promoController->deletePromo();
} elseif ($requestUri === "/admin/promos/restore") {
    $promoController->restorePromo();
} elseif ($requestUri === "/admin/promos/data") {
    $promoController->getPromosData();
} elseif ($requestUri === "/api/promos/validate") {
    $promoController->validatePromo();
}
// Order processing routes
elseif ($_SERVER["REQUEST_URI"] === "/process_checkout") {
    $orderController->processCheckout();
} elseif ($requestUri === "/api/orders/update-status") {
    $orderController->updateStatus();
}
// PayPal routes
elseif ($requestUri === "/paypal/create-order" && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $paypalController->createOrder();
} elseif ($requestUri === "/paypal/capture-order" && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $paypalController->captureOrder();
}
// DEBUG: kiểm tra session và PayPal config (XÓA sau khi debug xong)
elseif ($requestUri === "/debug/paypal") {
    header('Content-Type: application/json');
    require_once ROOT_PATH . '/config/paypal.php';

    $result = [
        'session_id'       => session_id(),
        'session_cart'     => !empty($_SESSION['cart']) ? count($_SESSION['cart']) . ' items' : 'TRỐNG',
        'session_user'     => $_SESSION['UserID'] ?? 'CHƯA ĐĂNG NHẬP',
        'paypal_mode'      => PAYPAL_MODE,
        'paypal_client_id' => PAYPAL_CLIENT_ID ? substr(PAYPAL_CLIENT_ID, 0, 8) . '...' : 'CHƯA CẤU HÌNH',
        'paypal_api_base'  => PAYPAL_API_BASE,
        'curl_enabled'     => function_exists('curl_init') ? 'YES' : 'NO',
        'token_test'       => null,
        'token_error'      => null,
    ];

    // Thử lấy token luôn
    if (function_exists('curl_init') && PAYPAL_CLIENT_ID) {
        $ch = curl_init(PAYPAL_API_BASE . '/v1/oauth2/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_USERPWD        => PAYPAL_CLIENT_ID . ':' . PAYPAL_CLIENT_SECRET,
            CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 10,
        ]);
        $resp     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            $result['token_error'] = 'cURL error: ' . $curlErr;
        } elseif ($httpCode === 200) {
            $result['token_test'] = 'OK - HTTP 200';
        } else {
            $result['token_error'] = 'HTTP ' . $httpCode . ': ' . $resp;
        }
    }

    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
// Statistics Controller routes
elseif ($requestUri === "/api/statistics/revenue-by-year") {
    $statictisController->getRevenueByYear();
} elseif ($requestUri === "/api/statistics/top-customers") {
    $statictisController->getTopCustomers();

} elseif ($requestUri === "/api/statistics/orders-by-category") {
    $statictisController->getOrdersByCategory();
}
// Book Controller routes
elseif ($requestUri === "/api/products/filtered") {
    $bookController->getFilteredBooks();
} elseif ($requestUri === "/api/products/add") {
    $bookController->createBook();
} elseif ($requestUri === "/api/products/update") {
    $bookController->updateBook();
} elseif ($requestUri === "/api/products/delete") {
    $bookController->deleteProduct();
}

// User Controller routes
elseif ($requestUri === "/api/users/add") {
    $userController->addUser();
} elseif ($requestUri === "/api/users/toggle-status") {
    $userController->toggleUserStatus();
} elseif ($requestUri === "/api/users/update") {
    $userController->updateUser();
}

// Admin routes
else if ($requestUri === "/admin/login") {
    $adminController->login();
} elseif ($requestUri === "/admin/dashboard") {
    $adminController->dashboard();
}
// Admin products routes
elseif ($requestUri === "/admin/products") {
    $adminController->products();
} elseif ($requestUri === "/admin/products/add") {
    $adminController->addProduct();
} elseif ($requestUri === "/admin/products/product-detail") {
    $adminController->viewProduct();
}
// Admin categories routes
elseif ($requestUri === "/admin/categories") {
    $adminController->categories();
} elseif ($requestUri === "/admin/categories/add") {
    $adminController->addCategory();
} elseif ($requestUri === "/admin/categories/category") {
    $adminController->viewCategory();
}
// Admin orders routes
elseif ($requestUri === "/admin/orders") {
    $adminController->orders();
} elseif ($requestUri === "/admin/orders/order-detail") {
    $adminController->viewOrder();
}
// Admin users routes
elseif ($requestUri === "/admin/users") {
    $adminController->users();
} elseif ($requestUri === "/admin/users/add") {
    $adminController->addUser();
} elseif ($requestUri === "/admin/users/user-info") {
    $adminController->viewUser();
}
// Admin statistics routes
elseif ($requestUri === "/admin/top-customers") {
    $adminController->topCustomers();
} elseif ($requestUri === "/admin/promos/promo") {
    $adminController->viewPromo();
} elseif (strpos($requestUri, "/admin") === 0) {
    $adminController->notFound();
}

// Client Routes
elseif ($requestUri === "/" || $requestUri === "/index") {
    $clientController->index();
} elseif (preg_match("/^\/shop/", $requestUri)) {
    $clientController->shop();
} elseif (preg_match("/^\/product-detail/", $requestUri)) {
    $clientController->productDetail();
} elseif ($requestUri === "/cart") {
    $clientController->cart();
} elseif ($requestUri === "/cart/checkout") {
    $clientController->checkout();
} elseif ($requestUri === "/my-account/order-history") {
    $clientController->orderHistory();
} elseif ($requestUri === "/my-account/order-history/order-detail") {
    $clientController->orderDetail();
} elseif ($requestUri === "/about-us") {
    $clientController->aboutUs();
} elseif ($requestUri === "/contact-us") {
    $clientController->contactUs();
} elseif ($requestUri === "/my-account") {
    $clientController->myAccount();
} elseif ($requestUri === "/my-account/update") {
    $userController->updateUserInfo();
} elseif ($requestUri === "/sync-cart" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $clientController->syncCart();
} else {
    $clientController->notFound();
}
