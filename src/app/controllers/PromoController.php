<?php
require_once __DIR__ . '/../models/Promo.php';
require_once __DIR__ . '/../controllers/BaseController.php';

class PromoController extends BaseController
{
    private $promoModel;

    public function __construct()
    {
        $this->promoModel = new Promo();
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

    public function deletePromo()
    {
        $this->requirePost();

        $data = $this->getRequestData();
        $id = $data['id'] ?? null;

        if ($id === null) {
            $this->responseJson([
                'success' => false,
                'message' => 'Thiếu ID khuyến mãi.'
            ]);
            return;
        }

        if ($this->promoModel->delete($id)) {
            $this->responseJson([
                'success' => true,
                'message' => 'Đã ẩn khuyến mãi thành công.'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Ẩn khuyến mãi thất bại. Vui lòng thử lại sau.'
            ]);
        }
    }

    public function restorePromo()
    {
        $this->requirePost();

        $data = $this->getRequestData();
        $id = $data['id'] ?? null;

        if ($id === null) {
            $this->responseJson([
                'success' => false,
                'message' => 'Thiếu ID khuyến mãi.'
            ]);
            return;
        }

        if ($this->promoModel->restore($id)) {
            $this->responseJson([
                'success' => true,
                'message' => 'Đã hiện khuyến mãi thành công.'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Hiện khuyến mãi thất bại. Vui lòng thử lại sau.'
            ]);
        }
    }

    public function validatePromo()
    {
        $data = $this->getRequestData();
        $code = trim($data['code'] ?? '');

        if (empty($code)) {
            $this->responseJson(['success' => false, 'message' => 'Vui lòng nhập mã khuyến mãi.']);
            return;
        }

        $promo = $this->promoModel->getActivePromoByName($code);

        if ($promo) {
            $this->responseJson([
                'success' => true,
                'promo' => [
                    'id'         => $promo['PromoID'],
                    'name'       => $promo['Name'],
                    'discounted' => (int) $promo['Discounted'],
                ]
            ]);
        } else {
            $this->responseJson(['success' => false, 'message' => 'Mã khuyến mãi không hợp lệ hoặc đã hết hạn.']);
        }
    }

    public function getPromosData()
    {
        $promos = $this->promoModel->getAll();
        $this->responseJson([
            'success' => true,
            'promos'  => $promos
        ]);
    }
}
