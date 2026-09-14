<!-- Breadcrumb -->
<div class="breadcrumb-container py-2 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>

                <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                    <?php foreach ($breadcrumbs as $index => $crumb): ?>
                        <?php if ($index == count($breadcrumbs) - 1): ?>
                            <!-- Mục cuối cùng (trang hiện tại) -->
                            <li class="breadcrumb-item active" aria-current="page"><?= $crumb['title'] ?></li>
                        <?php else: ?>
                            <!-- Mục điều hướng -->
                            <li class="breadcrumb-item">
                                <a href="<?= $crumb['url'] ?>"><?= $crumb['title'] ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</div>

<style>
    .breadcrumb-container {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 20px;
    }

    .breadcrumb {
        padding: 0.75rem 0;
        margin-bottom: 0;
        background-color: transparent;
        font-size: 1rem;
    }

    .breadcrumb-item+.breadcrumb-item::before {
        content: ">";
        color: #6c757d;
    }

    .breadcrumb-item a {
        color: #dc3545;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .breadcrumb-item a:hover {
        color: #c82333;
        text-decoration: underline;
    }

    .breadcrumb-item.active {
        color: #6c757d;
        font-weight: 500;
    }
</style>