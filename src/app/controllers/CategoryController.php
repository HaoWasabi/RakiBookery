<?php
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../controllers/BaseController.php';
class CategoryController extends BaseController
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function getAll()
    {
        $category = $this->categoryModel->getAll();
        // require_once __DIR__ . '/../views/admin-category.php';
        return $category;
    }

    public function create()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = $_POST["name"];
            $description = $_POST["description"];
            if ($this->categoryModel->create($name, $description)) {
                $_SESSION['category_success'] = "Thêm danh mục thành công!";
            } else {
                $_SESSION['category_error'] = "Thêm danh mục thất bại!";
            }
            header("Location: /category");
            exit;
        }
        require_once __DIR__ . '/../views/admin-category0-update-create.php';
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if ($id === null) {
            header("Location: /category");
            exit;
        }
        $category = $this->categoryModel->getCategoryById($id);
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = $_POST["name"];
            $description = $_POST["description"];
            if ($this->categoryModel->update($id, $name, $description)) {
                $_SESSION['category_success'] = "Cập nhật danh mục thành công!";
            } else {
                $_SESSION['category_error'] = "Cập nhật danh mục thất bại!";
            }
            header("Location: /category");
            exit;
        }
        // require_once __DIR__ . '/../views/admin-category0-update-create.php';
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id === null) {
            header("Location: /category");
            exit;
        }

        if ($this->categoryModel->delete($id)) {
            $_SESSION['category_success'] = "Xóa danh mục thành công!";
        } else {
            $_SESSION['category_error'] = "Xóa danh mục thất bại!";
        }
        // header("Location: /category");
        exit;
    }

    public function addCategory()
    {
        $this->requirePost();

        $categoryData = $this->getRequestData();

        // Check if category name already exists
        if ($this->categoryModel->categoryExists($categoryData['Name'])) {
            $this->responseJson([
                'success' => false,
                'message' => 'Tên thể loại đã tồn tại trong hệ thống. Vui lòng nhập tên khác!'
            ]);
            return;
        }

        $result = $this->categoryModel->addCategory($categoryData);

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => 'Thêm thể loại thành công',
                'redirect' => '/admin/categories'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Thêm thể loại thất bại. Vui lòng thử lại sau.'
            ]);
        }
    }

    public function updateCategory()
    {
        $this->requirePost();

        $categoryData = $this->getRequestData();

        // Check if category name already exists for other categories
        if ($this->categoryModel->categoryExistsForOtherCategory($categoryData['Name'], $categoryData['id'])) {
            $this->responseJson([
                'success' => false,
                'message' => 'Tên thể loại đã tồn tại trong hệ thống. Vui lòng nhập tên khác!'
            ]);
            return;
        }

        $result = $this->categoryModel->updateCategory($categoryData['id'], $categoryData);

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => 'Cập nhật thể loại thành công',
                'redirect' => '/admin/categories'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Cập nhật thể loại thất bại. Vui lòng thử lại sau.'
            ]);
        }
    }
}
