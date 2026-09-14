<?php

define('ROOT_PATH', dirname(__DIR__));

require_once "../app/controllers/CategoryController.php";
require_once "../app/controllers/OrderController.php";
require_once "../app/controllers/StatisticsController.php";
require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/CategoryController.php";
require_once "../app/controllers/UserController.php";
require_once "../app/controllers/ClientController.php";
require_once "../app/controllers/AdminController.php";
require_once "../app/controllers/BookController.php";

$orderController = new OrderController();
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
// Order processing routes
elseif ($_SERVER["REQUEST_URI"] === "/process_checkout") {
    $orderController->processCheckout();
} elseif ($requestUri === "/api/orders/update-status") {
    $orderController->updateStatus();
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
