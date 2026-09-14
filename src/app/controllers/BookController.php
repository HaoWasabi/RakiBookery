<?php
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../controllers/BaseController.php';

class BookController extends BaseController
{
    private $bookModel;
    private $categoryModel;

    public function __construct()
    {
        $this->bookModel = new Book();
        $this->categoryModel = new Category();
    }

    // Hiển thị danh sách sách
    public function index()
    {
        $books = $this->bookModel->getAllBooks();
        require_once __DIR__ . '/../views/admin-books.php';  // ✅ Đúng
    }

    // Tạo sách mới
/*     public function createBook()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $stock = $_POST['stock'] ?? 500000;
            $imageURL = $_POST['imageURL'] ?? '';
            $categoryId = $_POST['categoryId'] ?? 0;
            $length = $_POST['length'] ?? 0;
            $weight = $_POST['weight'] ?? 0;
            $dimensions = $_POST['dimensions'] ?? '';
            $language = $_POST['language'] ?? '';
            $format = $_POST['format'] ?? '';
            $author = $_POST['author'] ?? '';
            $publisher = $_POST['publisher'] ?? '';
            $releaseDate = $_POST['releaseDate'] ?? '';
            $status = $_POST['status'] ?? 0;

            return $this->bookModel->createBook($name, $description, $price, $stock, $imageURL, $categoryId, $length, $weight, $dimensions, $language, $format, $author, $publisher, $releaseDate, $status);
        }
    } */


    // Cập nhật sách
/*     public function updateBook()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $bookId = $_POST['bookId'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $stock = $_POST['stock'] ?? 500000;
            $imageURL = $_POST['imageURL'];
            $categoryId = $_POST['categoryId'];
            $length = $_POST['length'];
            $weight = $_POST['weight'];
            $dimensions = $_POST['dimensions'];
            $language = $_POST['language'];
            $format = $_POST['format'];
            $author = $_POST['author'];
            $publisher = $_POST['publisher'];
            $releaseDate = $_POST['releaseDate'];
            $status = $_POST['status'];

            return $this->bookModel->updateBook($bookId, $name, $description, $price, $stock, $imageURL, $categoryId, $length, $weight, $dimensions, $language, $format, $author, $publisher, $releaseDate, $status);
        }
    } */

    // Xóa sách
    public function deleteBook()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $bookId = $_POST['bookId'];

            return $this->bookModel->deleteBook($bookId);
        }
    }

    // Hủy xóa sách
    public function undeleteBook()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $bookId = $_POST['bookId'];

            return $this->bookModel->undeleteBook($bookId);
        }
    }

    public function getAllBooks()
    {
        return $this->bookModel->getAllBooks();
    }

    public function getAllAvailableBooks()
    {
        return $this->bookModel->getAllAvailableBooks();
    }

    public function getBookById($bookId)
    {
        return $this->bookModel->getBookById($bookId);
    }

    public function getAvailableBookById($bookId)
    {
        return $this->bookModel->getAvailableBookById($bookId);
    }

    public function getTopBestSellingBooks($limit = 5)
    {
        return $this->bookModel->getTopBestSellingBooks($limit);
    }
    public function getFilteredBooks()
    {
        $categoryId = isset($_GET['category']) ? $_GET['category'] : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';

        // Load books based on filters
        if (!empty($categoryId) || $status !== '') {
            $books = $this->bookModel->getFilteredBooks(0, 0, '', '', $categoryId, $status);
        } else {
            // Get all books without pagination
            $books = $this->bookModel->getAllBooks();
        }

        // Get categories for the filter dropdown
        $categories = $this->categoryModel->getAll();

        $this->responseJson([
            'success' => true,
            'data' => [
                'books' => $books,
                'categories' => $categories
            ]
        ]);
    }

    public function createBook()
    {
        $this->requirePost();

        $productData = $this->getRequestData();

        $productData['Description'] = !empty($productData['Description']) ? $productData['Description'] : null;
        $productData['Length'] = !empty($productData['Length']) ? $productData['Length'] : null;
        $productData['Weight'] = !empty($productData['Weight']) ? $productData['Weight'] : null;
        $productData['Dimensions'] = !empty($productData['Dimensions']) ? $productData['Dimensions'] : null;
        $productData['Language'] = !empty($productData['Language']) ? $productData['Language'] : null;
        $productData['Format'] = !empty($productData['Format']) ? $productData['Format'] : null;

        // Handle image upload
        $imageURL = '/img/img-not-available.png'; // Default image path
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadedImage = $this->uploadImage($_FILES['image']);
            if ($uploadedImage) {
                $imageURL = $uploadedImage;
            }
        }

        $productData['ImageURL'] = $imageURL;

        $result = $this->bookModel->addBook($productData);

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => 'Thêm sản phẩm thành công',
                'redirect' => '/admin/products'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Thêm sản phẩm thất bại. Vui lòng thử lại sau.'
            ]);
        }

    }

    public function updateBook()
    {
        $this->requirePost();

        $productData = $this->getRequestData();

        $productData['Description'] = !empty($productData['Description']) ? $productData['Description'] : null;
        $productData['Length'] = !empty($productData['Length']) ? $productData['Length'] : null;
        $productData['Weight'] = !empty($productData['Weight']) ? $productData['Weight'] : null;
        $productData['Dimensions'] = !empty($productData['Dimensions']) ? $productData['Dimensions'] : null;
        $productData['Language'] = !empty($productData['Language']) ? $productData['Language'] : null;
        $productData['Format'] = !empty($productData['Format']) ? $productData['Format'] : null;

        // Handle image upload - keep existing image if no new one uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // User uploaded a new image
            $uploadedImage = $this->uploadImage($_FILES['image']);
            if ($uploadedImage) {
                $productData['ImageURL'] = $uploadedImage;
            }
        } else if (isset($_POST['ImageURL']) && !empty($_POST['ImageURL'])) {
            $productData['ImageURL'] = $_POST['ImageURL'];
        } else {
            $productData['ImageURL'] = '/img/img-not-available.png';
        }

        $result = $this->bookModel->updateBook($productData['id'], $productData);

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => 'Cập nhật sản phẩm thành công',
                'redirect' => '/admin/products'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Cập nhật sản phẩm thất bại. Vui lòng thử lại sau.'
            ]);
        }

    }
    public function deleteProduct()
    {
        $this->requirePost();

        $data = $this->getRequestData();
        $productId = $data['product_id'];
        $action = $data['action'] ?? 'delete'; // delete or restore

        if (!$productId) {
            $this->responseJson([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ'
            ]);
            return;
        }

        // Restore the product
        if ($action === 'restore') {
            $result = $this->bookModel->undeleteBook($productId);
            $message = 'Sản phẩm đã được hiển thị lại thành công';
        } else {
            // Check if the product has been sold/ordered
            $hasOrders = $this->bookModel->hasOrders($productId);

            if ($hasOrders) {
                // Disable the product instead of deleting
                $result = $this->bookModel->disableBook($productId);
                $message = 'Sản phẩm đã được ẩn thành công';
            } else {
                // Delete the product if it hasn't been sold
                $result = $this->bookModel->deleteBook($productId);
                $message = 'Xóa sản phẩm thành công';
            }
        }

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => $message
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => $action === 'restore' ? 'Không thể hiển thị lại sản phẩm' : 'Không thể ẩn sản phẩm'
            ]);
        }
    }

    private function uploadImage($file)
    {
        $targetDir = ROOT_PATH . "/public/uploads/";

        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $targetFilePath = $targetDir . $fileName;

        // Check if image file is an actual image
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            return false;
        }

        // Check file size (limit to 5MB)
        if ($file['size'] > 5000000) {
            return false;
        }

        // Allow certain file formats
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if (!in_array($fileType, $allowedTypes)) {
            return false;
        }

        // Upload file
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            return '/uploads/' . $fileName;
        } else {
            return false;
        }
    }
}

// $bookController = new BookController();
// $limit = 4;
// $topBooks = $bookController->getTopBestSellingBooks($limit);

// if (empty($topBooks)) {
//     echo "Không có sách nào được bán.";
// } else {
//     echo "Top $limit sách bán chạy nhất:<br>";
// }

// foreach ($topBooks as $book) {
//     echo "Tên sách: {$book['Name']} - Đã bán: {$book['TotalSold']} lượt<br>";
// }
