<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? $page_title : 'RakiBookery' ?></title>

<!-- Favicon -->
<link rel="icon" href="/img/logo.jpg" type="image/jpeg">
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.css">

<!-- NoUiSlider CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.5.0/nouislider.min.css">

<!-- AOS CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />

<!-- Animate.css for animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">



<!-- Custom CSS -->
<?php
$cdn = rtrim(env('CDN_URL', ''), '/');
$assetBase = $cdn !== '' ? $cdn : '../..';
?>
<link rel="stylesheet" href="<?= $assetBase ?>/assets/client/css/style.css" />
<link rel="stylesheet" href="<?= $assetBase ?>/assets/client/css/cart-offcanvas.css" />
<link rel="stylesheet" href="<?= $assetBase ?>/assets/client/css/chat-bubble.css" />

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Bundle JS (đã bao gồm cả Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Swiper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- PaginationJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.6.0/pagination.min.js"></script>

<!-- AOS - Animate On Scroll Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<!-- noUiSlider - Range Slider JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>

<!-- Autonumeric - Input Number -->
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.8"></script>

<!-- Custom JS files -->
<script type="module" src="<?= $assetBase ?>/assets/client/js/main.js"></script>

<!-- < ?php if (!isset($_SESSION['UserID'])): ?> -->
<script type="module" src="<?= $assetBase ?>/assets/client/js/auth.js"></script>
<link rel="stylesheet" href="<?= $assetBase ?>/assets/client/css/auth-modal.css" />
<!-- < ?php endif; ?> -->

<script>
    let bestSellerBooks = <?php echo json_encode($bestSellerBooks); ?>;
    let allBooks = <?php echo json_encode($books); ?>;
    window.RAKI_CHAT_API = <?php echo json_encode(
        env('APP_ENV', 'production') === 'local'
            ? '/api/chat'
            : env('AI_API_URL', '')
    ); ?>;
</script>

<!-- Chat Bubble JS (defer để DOM đã sẵn sàng) -->
<script src="<?= $assetBase ?>/assets/client/js/chat-bubble.js" defer></script>
