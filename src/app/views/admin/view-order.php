<!-- Order Detail View -->
<div class="container-fluid px-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Chi tiết đơn hàng #<?= $order['OrderID'] ?></h1>
    <a href="javascript:history.back()" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<?php
$status_text = ''; $statusStage = 0;
$badgeBg = '#e9ecef'; $badgeColor = '#495057';
switch ($order['Status']) {
    case 'pending':
        $status_text = 'Chờ xác nhận'; $statusStage = 1;
        $badgeBg = '#fff3cd'; $badgeColor = '#856404'; break;
    case 'confirmed':
        $status_text = 'Đã xác nhận'; $statusStage = 2;
        $badgeBg = '#d1ecf1'; $badgeColor = '#0c5460'; break;
    case 'delivered_success':
        $status_text = 'Đã giao hàng'; $statusStage = 4;
        $badgeBg = '#d4edda'; $badgeColor = '#155724'; break;
    case 'canceled':
        $status_text = 'Đã hủy'; $statusStage = 0;
        $badgeBg = '#f8d7da'; $badgeColor = '#721c24'; break;
}
$s = function(int $t) use ($statusStage): array {
    $a = $statusStage >= $t;
    return ['op'=>$a?'1':'0.4','bg'=>$a?'#dc3545':'#e9ecef','fill'=>$a?'#fff':'#6c757d','fw'=>$a?'600':'400','clr'=>$a?'#212529':'#6c757d'];
};
$s1=$s(1); $s2=$s(2); $s3=$s(4);
$pct = min(($statusStage/3)*100, 100);
?>

<!-- TRẠNG THÁI ĐƠN HÀNG -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">Trạng thái đơn hàng</h5>
    </div>
    <div class="card-body">
        <?php if ($statusStage === 0): ?>
            <div class="alert alert-danger d-flex align-items-center mb-3">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div>Đơn hàng đã bị hủy.</div>
            </div>
        <?php else: ?>
            <div style="height:6px;background:#e9ecef;border-radius:4px;margin-bottom:1.75rem;overflow:hidden;">
                <div style="height:100%;background:#dc3545;border-radius:4px;width:<?= $pct ?>%;"></div>
            </div>
            <div class="row text-center mb-2">
                <div class="col-4">
                    <div style="display:flex;flex-direction:column;align-items:center;opacity:<?= $s1['op'] ?>;">
                        <div style="width:52px;height:52px;border-radius:50%;background:<?= $s1['bg'] ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 0.6rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="<?= $s1['fill'] ?>" viewBox="0 0 16 16">
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2zM5 8h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1zm0 2h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1zm0-4h1a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1z"/>
                            </svg>
                        </div>
                        <p style="font-size:.875rem;margin-bottom:2px;font-weight:<?= $s1['fw'] ?>;color:<?= $s1['clr'] ?>;">Đặt hàng</p>
                        <p style="font-size:.75rem;color:#6c757d;margin:0;"><?= date('H:i:s d/m/Y', strtotime($order['OrderDate'])) ?></p>
                    </div>
                </div>
                <div class="col-4">
                    <div style="display:flex;flex-direction:column;align-items:center;opacity:<?= $s2['op'] ?>;">
                        <div style="width:52px;height:52px;border-radius:50%;background:<?= $s2['bg'] ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 0.6rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="<?= $s2['fill'] ?>" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                        </div>
                        <p style="font-size:.875rem;margin-bottom:2px;font-weight:<?= $s2['fw'] ?>;color:<?= $s2['clr'] ?>;">Xác nhận</p>
                    </div>
                </div>
                <div class="col-4">
                    <div style="display:flex;flex-direction:column;align-items:center;opacity:<?= $s3['op'] ?>;">
                        <div style="width:52px;height:52px;border-radius:50%;background:<?= $s3['bg'] ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 0.6rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="<?= $s3['fill'] ?>" viewBox="0 0 16 16">
                                <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2 8.186 1.113zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                            </svg>
                        </div>
                        <p style="font-size:.875rem;margin-bottom:2px;font-weight:<?= $s3['fw'] ?>;color:<?= $s3['clr'] ?>;">Đã giao</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="text-center mt-2">
            <span style="display:inline-block;padding:.4em 1.1em;border-radius:20px;font-size:.85rem;font-weight:600;background:<?= $badgeBg ?>;color:<?= $badgeColor ?>;">
                ● <?= $status_text ?>
            </span>
        </div>
    </div>
</div>

<!-- CHI TIẾT + THÔNG TIN ĐƠN HÀNG -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">Chi tiết đơn hàng</h5>
            </div>
            <div class="card-body">
                <div class="border rounded p-3 mb-3">
                    <h6 class="fw-semibold mb-3 pb-2 border-bottom">
                        <i class="fas fa-user me-2 text-secondary"></i>Thông tin khách hàng
                    </h6>
                    <p class="mb-2"><strong>Tên:</strong> <?= htmlspecialchars($order['UserName']) ?></p>
                    <p class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($order['Email'] ?? 'Không có') ?></p>
                    <p class="mb-3"><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['Phone']) ?></p>
                    <a href="/admin/users/user-info?id=<?= $order['UserID'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i>Xem hồ sơ
                    </a>
                </div>
                <div class="border rounded p-3">
                    <h6 class="fw-semibold mb-3 pb-2 border-bottom">
                        <i class="fas fa-map-marker-alt me-2 text-secondary"></i>Địa chỉ giao hàng
                    </h6>
                    <p class="mb-2"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['Address']) ?></p>
                    <p class="mb-2"><strong>Phường/Xã:</strong> <?= htmlspecialchars($order['Ward']) ?></p>
                    <p class="mb-2"><strong>Quận/Huyện:</strong> <?= htmlspecialchars($order['District']) ?></p>
                    <p class="mb-0"><strong>Tỉnh/Thành phố:</strong> <?= htmlspecialchars($order['City']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">Thông tin đơn hàng</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-secondary">Mã đơn hàng</span>
                        <span class="fw-bold">#<?= $order['OrderID'] ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-secondary">Ngày đặt hàng</span>
                        <span><?= date('H:i:s d/m/Y', strtotime($order['OrderDate'])) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-secondary">Phương thức TT</span>
                        <span><?= htmlspecialchars($order['PaymentMethod']) ?></span>
                    </li>
                </ul>
                <hr>
                <?php
                $origAmt  = isset($order['OriginalAmount']) ? (float)$order['OriginalAmount'] : (float)$order['TotalAmount'];
                $totAmt   = (float)$order['TotalAmount'];
                $hasPromo = !empty($order['PromoName']) && $origAmt > $totAmt;
                $discAmt  = $origAmt - $totAmt;
                ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Tạm tính</span>
                    <span><?= number_format($origAmt, 0, ',', '.') ?> đ</span>
                </div>
                <?php if ($hasPromo): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span style="color:#198754;">Giảm giá
                        <small style="color:#6c757d;display:block;"><?= htmlspecialchars($order['PromoName']) ?> (-<?= (int)$order['PromoDiscounted'] ?>%)</small>
                    </span>
                    <span style="color:#198754;font-weight:500;">- <?= number_format($discAmt, 0, ',', '.') ?> đ</span>
                </div>
                <?php endif; ?>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Tổng cộng</span>
                    <span style="color:#dc3545;font-weight:700;"><?= number_format($totAmt, 0, ',', '.') ?> đ</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SẢN PHẨM TRONG ĐƠN -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">Sản phẩm trong đơn</h5>
        <span class="badge bg-primary rounded-pill"><?= count($orderDetails) ?> sản phẩm</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">STT</th>
                        <th width="12%">Ảnh</th>
                        <th>Sản phẩm</th>
                        <th width="8%" class="text-center">SL</th>
                        <th width="15%" class="text-end">Đơn giá</th>
                        <th width="15%" class="text-end">Thành tiền</th>
                        <th width="8%" class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderDetails as $i => $item): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><img src="<?= $item['ImageURL'] ?>" alt="" style="width:65px;height:85px;object-fit:cover;border-radius:4px;"></td>
                        <td>
                            <div class="fw-medium"><?= htmlspecialchars($item['ProductName']) ?></div>
                            <div class="text-secondary small">
                                <div>Tác giả: <?= htmlspecialchars($item['Author']) ?></div>
                                <div>Thể loại: <?= htmlspecialchars($item['Category']) ?></div>
                            </div>
                        </td>
                        <td class="text-center"><?= $item['Quantity'] ?></td>
                        <td class="text-end"><?= number_format($item['Price'], 0, ',', '.') ?> đ</td>
                        <td class="text-end fw-bold"><?= number_format($item['Price'] * $item['Quantity'], 0, ',', '.') ?> đ</td>
                        <td class="text-center">
                            <a href="/admin/products/product-detail?id=<?= $item['ProductID'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Xem sản phẩm">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div><!-- end container-fluid -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[title]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });
});
</script>
