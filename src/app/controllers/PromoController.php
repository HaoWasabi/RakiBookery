<?php
require_once __DIR__ . '/../models/Pr.php';
require_once __DIR__ . '/../controllers/BaseController.php';
class PromoController extends BaseController
{
    private $promoModel;

    public function __construct()
    {
        $this->promoModel = new Promo();
    }

    public function getAll()
    {
        $promo = $this->promoModel->getAll();
        // require_once __DIR__ . '/../views/admin-promo.php';
        return $promo;
    }

    public function create()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = $_POST["name"];
            $discounted = $_POST["discounted"];
            $datecreated = $_POST["datecreated"];
            if ($this->promoModel->create($name, $discounted, $datecreated)) {
                $_SESSION['promo_success'] = "Thêm khuyến mãi thành công!";
            } else {
                $_SESSION['promo_error'] = "Thêm khuyến mãi thất bại!";
            }
            header("Location: /promo");
            exit;
        }
        require_once __DIR__ . '/../views/admin-promo0-update-create.php';
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if ($id === null) {
            header("Location: /promo");
            exit;
        }
        $promo = $this->promoModel->getPromoById($id);
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = $_POST["name"];
            $discounted = $_POST["discounted"];
            $datecreated = $_POST["datecreated"];
            $status = $_POST["status"];
            if ($this->promoModel->update($id, $name, $discounted, $datecreated, $status)) {
                $_SESSION['promo_success'] = "Cập nhật khuyến mãi thành công!";
            } else {
                $_SESSION['promo_error'] = "Cập nhật khuyến mãi thất bại!";
            }
            header("Location: /promo");
            exit;
        }
        // require_once __DIR__ . '/../views/admin-promo0-update-create.php';
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id === null) {
            header("Location: /promo");
            exit;
        }

        if ($this->promoModel->delete($id)) {
            $_SESSION['promo_success'] = "Xóa khuyến mãi thành công!";
        } else {
            $_SESSION['promo_error'] = "Xóa khuyến mãi thất bại!";
        }
        // header("Location: /promo");
        exit;
    }

    public function addPromo()
    {
        $this->requirePost();

        $promoData = $this->getRequestData();

        // Check if Promo name already exists
        if ($this->promoModel->promoExists($promoData['Name'])) {
            $this->responseJson([
                'success' => false,
                'message' => 'Tên khuyến mãi đã tồn tại trong hệ thống. Vui lòng nhập tên khác!'
            ]);
            return;
        }

        $result = $this->promoModel->addPromo($promoData);

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => 'Thêm khuyến mãi thành công',
                'redirect' => '/admin/promos'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Thêm khuyến mãi thất bại. Vui lòng thử lại sau.'
            ]);
        }
    }

    public function updatePromo()
    {
        $this->requirePost();

        $promoData = $this->getRequestData();

        // Check if Promo name already exists for other promos
        if ($this->promoModel->promoExistsForOtherPromo($promoData['Name'], $promoData['id'])) {
            $this->responseJson([
                'success' => false,
                'message' => 'Tên khuyến mãi đã tồn tại trong hệ thống. Vui lòng nhập tên khác!'
            ]);
            return;
        }

        $result = $this->promoModel->updatePromo($promoData['id'], $promoData);

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => 'Cập nhật khuyến mãi thành công',
                'redirect' => '/admin/promos'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Cập nhật khuyến mãi thất bại. Vui lòng thử lại sau.'
            ]);
        }
    }
}
