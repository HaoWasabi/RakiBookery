<?php
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/MockBookData.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../../config/s3.php';

class BookController extends BaseController
{
    private $bookModel;
    private $categoryModel;

    /** true → dùng MockBookData, false → dùng DB + S3 thật */
    private bool $useMock;

    public function __construct()
    {
        $this->useMock = (env('BOOK_DATA_SOURCE', 'live') === 'mock');

        $this->bookModel    = new Book();
        $this->categoryModel = new Category();
    }

    // Hiển thị danh sách sách
    public function index()
    {
        $books = $this->getAllBooks();
        require_once __DIR__ . '/../views/admin-books.php';
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
        return $this->useMock
            ? MockBookData::getAll()
            : $this->bookModel->getAllBooks();
    }

    public function getAllAvailableBooks()
    {
        return $this->useMock
            ? MockBookData::getAvailable()
            : $this->bookModel->getAllAvailableBooks();
    }

    public function getBookById($bookId)
    {
        return $this->useMock
            ? MockBookData::getById((int) $bookId)
            : $this->bookModel->getBookById($bookId);
    }

    public function getAvailableBookById($bookId)
    {
        if ($this->useMock) {
            $book = MockBookData::getById((int) $bookId);
            return ($book && $book['Status'] == 1) ? $book : null;
        }
        return $this->bookModel->getAvailableBookById($bookId);
    }

    public function getTopBestSellingBooks($limit = 5)
    {
        return $this->useMock
            ? MockBookData::getTopBestSelling($limit)
            : $this->bookModel->getTopBestSellingBooks($limit);
    }

    /** Presign ImageURL cho mảng sách trước khi trả về JSON/view */
    private function presignImageUrls(array &$books): void
    {
        foreach ($books as &$book) {
            if (!empty($book['ImageURL'])) {
                $book['ImageURL'] = s3_url($book['ImageURL']);
            }
        }
    }

    public function getFilteredBooks()
    {
        $this->requireAdmin();
        $categoryId = isset($_GET['category']) ? $_GET['category'] : '';
        $status     = isset($_GET['status'])   ? $_GET['status']   : '';

        if ($this->useMock) {
            $books      = MockBookData::getFiltered($categoryId, $status);
            $categories = MockBookData::getCategories();
        } else {
            if (!empty($categoryId) || $status !== '') {
                $books = $this->bookModel->getFilteredBooks(0, 0, '', '', $categoryId, $status);
            } else {
                $books = $this->bookModel->getAllBooks();
            }
            $categories = $this->categoryModel->getAll();
        }

        $this->presignImageUrls($books);

        $this->responseJson([
            'success' => true,
            'data'    => [
                'books'      => $books,
                'categories' => $categories,
            ],
        ]);
    }

    public function createBook()
    {
        $this->requireAdmin();
        $this->requirePost();

        $productData = $this->getRequestData();

        $productData['Description'] = !empty($productData['Description']) ? $productData['Description'] : null;
        $productData['Length'] = !empty($productData['Length']) ? $productData['Length'] : null;
        $productData['Weight'] = !empty($productData['Weight']) ? $productData['Weight'] : null;
        $productData['Dimensions'] = !empty($productData['Dimensions']) ? $productData['Dimensions'] : null;
        $productData['Language'] = !empty($productData['Language']) ? $productData['Language'] : null;
        $productData['Format'] = !empty($productData['Format']) ? $productData['Format'] : null;

        // Handle image upload
        $imageURL = '/img/img-not-available.png';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Lấy BookID mới sau khi insert để đặt tên file
            // Upload trước với tên tạm, sau đó rename nếu cần
            $tempUrl = $this->uploadImage($_FILES['image'], null);
            if ($tempUrl) {
                $imageURL = $tempUrl;
            }
        }

        $productData['ImageURL'] = $imageURL;

        $result = $this->bookModel->addBook($productData);

        if ($result) {
            $newBookId = (int) $result;

            // Nếu đã upload ảnh tạm, re-upload với tên theo BookID mới
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK && $imageURL !== '/img/img-not-available.png') {
                // Xóa ảnh tạm cũ trên S3
                s3_delete($imageURL);

                // Upload lại với BookID
                $finalUrl = $this->uploadImage($_FILES['image'], $newBookId);
                if ($finalUrl) {
                    $this->bookModel->updateBook($newBookId, ['ImageURL' => $finalUrl]);
                    $imageURL = $finalUrl;
                }
            }
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
        $this->requireAdmin();
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
            $bookId = !empty($productData['id']) ? (int) $productData['id'] : null;

            // Xóa ảnh cũ trên S3 (nếu tên theo uniqid cũ, không cần xóa thủ công vì uploadImage đã xóa theo ID)
            $oldImageURL = $_POST['ImageURL'] ?? '';
            if (!empty($oldImageURL) && $bookId === null) {
                s3_delete($oldImageURL);
            }

            $uploadedImage = $this->uploadImage($_FILES['image'], $bookId);
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
        $this->requireAdmin();
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

    private function uploadImage($file, ?int $bookId = null): string|false
    {
        return s3_upload($file, $bookId);
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
