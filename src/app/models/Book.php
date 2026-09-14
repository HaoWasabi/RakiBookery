<?php
require_once __DIR__ . '/../../config/database.php';

class Book
{
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = Database::getInstance()->getConnection();
        } catch (PDOException $e) {
            error_log("Lỗi kết nối DB: " . $e->getMessage());
            die("Không thể kết nối đến cơ sở dữ liệu.");
        }
    }

    // Thêm sách mới
    public function createBook($name, $description, $price, $stock, $imageURL, $categoryID, $length, $weight, $dimensions, $language, $format, $author, $publisher, $releaseDate, $status)
    {
        try {
            $stmt = $this->conn->prepare("INSERT INTO Book (Name, Description, Price, Stock, ImageURL, CategoryID, Length, Weight, Dimensions, Language, Format, Author, Publisher, ReleaseDate, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([$name, $description, $price, $stock, $imageURL, $categoryID, $length, $weight, $dimensions, $language, $format, $author, $publisher, $releaseDate, $status]);
        } catch (PDOException $e) {
            error_log("Lỗi thêm sách: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật sách
    /*  public function updateBook($bookID, $name, $description, $price, $stock, $imageURL, $categoryID, $length, $weight, $dimensions, $language, $format, $author, $publisher, $releaseDate, $status)
     {
         try {
             $stmt = $this->conn->prepare("UPDATE Book SET Name = ?, Description = ?, Price = ?, Stock = ?, ImageURL = ?, CategoryID = ?, Length = ?, Weight = ?, Dimensions = ?, Language = ?, Format = ?, Author = ?, Publisher = ?, ReleaseDate = ?, Status = ? WHERE BookID = ? AND Status <> 0");
             return $stmt->execute([$name, $description, $price, $stock, $imageURL, $categoryID, $length, $weight, $dimensions, $language, $format, $author, $publisher, $releaseDate, $status, $bookID]);
         } catch (PDOException $e) {
             error_log("Lỗi cập nhật sách: " . $e->getMessage());
             return false;
         }
     } */

    // Xóa sách
    public function deleteBook($bookID)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM Book WHERE BookID = ?");
            return $stmt->execute([$bookID]);
        } catch (PDOException $e) {
            error_log("Lỗi xóa sách: " . $e->getMessage());
            return false;
        }
    }

    // Hủy xóa sách
    public function undeleteBook($bookID)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE Book SET Status = 1 WHERE BookID = ? AND Status = 0");
            return $stmt->execute([$bookID]);
        } catch (PDOException $e) {
            error_log("Lỗi hủy xóa sách: " . $e->getMessage());
            return false;
        }
    }

    // Lấy danh sách tất cả sách
    public function getAllBooks()
    {
        try {
            $stmt = $this->conn->prepare("SELECT b.*, c.Name AS Category FROM Book b JOIN Category c ON b.CategoryID = c.CategoryID ORDER BY b.BookID DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách sách: " . $e->getMessage());
            return [];
        }
    }

    public function getAllAvailableBooks()
    {
        try {
            $stmt = $this->conn->prepare("SELECT b.*, c.Name AS Category FROM Book b JOIN Category c ON b.CategoryID = c.CategoryID WHERE b.Status <> 0 ORDER BY b.BookID DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách sách: " . $e->getMessage());
            return [];
        }
    }

    public function getBookById($bookID)
    {
        try {
            $stmt = $this->conn->prepare("SELECT b.*, c.Name AS Category FROM Book b JOIN Category c ON b.CategoryID = c.CategoryID WHERE b.BookID = ?");
            $stmt->execute([$bookID]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy thông tin sách: " . $e->getMessage());
            return null;
        }
    }

    public function getAvailableBookById($bookID)
    {
        try {
            $stmt = $this->conn->prepare("SELECT b.*, c.Name AS Category FROM Book b JOIN Category c ON b.CategoryID = c.CategoryID WHERE b.BookID = ? AND b.Status = 1");
            $stmt->execute([$bookID]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy thông tin sách: " . $e->getMessage());
            return null;
        }
    }

    // Thống kê sách bán chạy nhất kèm số lượt bán
    public function getTopBestSellingBooks($limit = 5)
    {
        try {
            $query = "
            SELECT
                b.*,
                c.Name AS Category,
                SUM(od.Quantity) AS TotalSold,
                SUM(od.Quantity * od.Price) AS TotalRevenue
            FROM OrderDetail od
            INNER JOIN `Order` o ON od.OrderID = o.OrderID
            INNER JOIN Book b ON od.ProductID = b.BookID
            INNER JOIN Category c ON b.CategoryID = c.CategoryID
            WHERE o.Status = 'delivered_success'
            GROUP BY b.BookID, c.Name
            ORDER BY TotalSold DESC
            LIMIT :limit
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi thống kê sách bán chạy: " . $e->getMessage());
            return [];
        }
    }

    // Get total number of books
    public function getTotalBooks()
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Book WHERE Status <> 0");
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Lỗi đếm tổng số sách: " . $e->getMessage());
            return 0;
        }
    }

    // Get filtered books with pagination for admin panel
    public function getFilteredBooks($offset = '', $limit = '', $name = '', $author = '', $categoryId = '', $status = '')
    {
        try {
            $query = "
                SELECT b.*, c.Name AS Category 
                FROM Book b 
                JOIN Category c ON b.CategoryID = c.CategoryID 
                WHERE 1=1
            ";

            $params = [];

            if (!empty($name)) {
                $query .= " AND b.Name LIKE ?";
                $params[] = "%{$name}%";
            }

            if (!empty($author)) {
                $query .= " AND b.Author LIKE ?";
                $params[] = "%{$author}%";
            }

            if (!empty($categoryId)) {
                $query .= " AND b.CategoryID = ?";
                $params[] = $categoryId;
            }

            if ($status !== '') {
                $query .= " AND b.Status = ?";
                $params[] = (int) $status;
            }

            // Always order by ID
            $query .= " ORDER BY b.BookID DESC";

            // Apply pagination only if limit is not 0
            if (!empty($limit) && $limit > 0) {
                $query .= " LIMIT ?, ?";
                $params[] = (int) $offset;
                $params[] = (int) $limit;
            }

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách sách: " . $e->getMessage());
            return [];
        }
    }

    // Count filtered books for pagination
    public function countFilteredBooks($name = '', $author = '', $categoryId = '', $status = '')
    {
        try {
            $query = "
                SELECT COUNT(*) 
                FROM Book b 
                JOIN Category c ON b.CategoryID = c.CategoryID 
                WHERE 1=1
            ";

            $params = [];

            if (!empty($name)) {
                $query .= " AND b.Name LIKE ?";
                $params[] = "%{$name}%";
            }

            if (!empty($author)) {
                $query .= " AND b.Author LIKE ?";
                $params[] = "%{$author}%";
            }

            if (!empty($categoryId)) {
                $query .= " AND b.CategoryID = ?";
                $params[] = $categoryId;
            }

            if ($status !== '') {
                $query .= " AND b.Status = ?";
                $params[] = (int) $status;
            }

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Lỗi đếm số sách: " . $e->getMessage());
            return 0;
        }
    }

    // Check if a book has orders
    public function hasOrders($bookId)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) 
                FROM OrderDetail od 
                JOIN `Order` o ON od.OrderID = o.OrderID
                WHERE od.ProductID = ?
            ");
            $stmt->execute([$bookId]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra đơn hàng của sách: " . $e->getMessage());
            return false;
        }
    }

    // Disable a book (soft delete)
    public function disableBook($bookId)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE Book SET Status = 0 WHERE BookID = ?");
            return $stmt->execute([$bookId]);
        } catch (PDOException $e) {
            error_log("Lỗi vô hiệu hóa sách: " . $e->getMessage());
            return false;
        }
    }

    // Add a new book
    public function addBook($data)
    {
        try {
            $query = "
                INSERT INTO Book 
                (Name, Description, Price, Stock, ImageURL, CategoryID, Length, Weight, Dimensions, 
                Language, Format, Author, Publisher, ReleaseDate, Status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";

            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([
                $data['Name'],
                $data['Description'] ?? null,
                $data['Price'],
                $data['Stock'],
                $data['ImageURL'],
                $data['CategoryID'],
                $data['Length'] ?? null,
                $data['Weight'] ?? null,
                $data['Dimensions'] ?? null,
                $data['Language'] ?? null,
                $data['Format'] ?? null,
                $data['Author'],
                $data['Publisher'],
                $data['ReleaseDate'],
                $data['Status'] ?? 1
            ]);

            if ($result) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Lỗi thêm sách: " . $e->getMessage());
            return false;
        }
    }

    // Update an existing book
    public function updateBook($bookId, $data)
    {
        try {
            $fields = [];
            $params = [];

            $possibleFields = [
                'Name',
                'Description',
                'Price',
                'Stock',
                'CategoryID',
                'Length',
                'Weight',
                'Dimensions',
                'Language',
                'Format',
                'Author',
                'Publisher',
                'ReleaseDate',
                'Status'
            ];

            foreach ($possibleFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }

            // Handle image separately
            if (isset($data['ImageURL'])) {
                $fields[] = "ImageURL = ?";
                $params[] = $data['ImageURL'];
            }

            if (empty($fields)) {
                return true; // Nothing to update
            }

            $query = "UPDATE Book SET " . implode(', ', $fields) . " WHERE BookID = ?";
            $params[] = $bookId;

            $stmt = $this->conn->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật sách: " . $e->getMessage());
            return false;
        }
    }
}
