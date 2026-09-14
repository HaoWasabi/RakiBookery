<!DOCTYPE html>
<html lang="vi">

<?php require_once 'head.php'; ?>

<body>

    <?php require_once 'header.php'; ?>


    <?php require_once 'cart-offcanvas.php'; ?>

    <?php if (isset($show_breadcrumb) && $show_breadcrumb): ?>
        <?php require_once 'breadcrumb.php'; ?>
    <?php endif; ?>

    <!-- Nội dung chính -->
    <main>
        <?= $content ?? '' ?>
    </main>

    <?php require_once 'footer.php'; ?>

</body>

</html>