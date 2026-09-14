<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: #ffffff;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .header .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .header .nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            padding: 0;
            margin: 0;
        }

        .header .nav ul li a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            transition: color 0.3s;
        }

        .header .nav ul li a:hover {
            color: #007bff;
        }

        .header .icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header .icons a {
            text-decoration: none;
            font-size: 18px;
            color: #555;
            transition: color 0.3s;
        }

        .header .icons a:hover {
            color: #007bff;
        }

        /* Container */
        .container {
            margin-top: 100px;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Footer */
        .footer {
            background-color: #343a40;
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="logo">Meep Bookery</div>
        <nav class="nav">
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="#">Shop</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
        <div class="icons">
            <a href="cart.html">🛒</a>
            <a href="#">👤</a>
        </div>
    </header>
    <div class="container">
        <h2 class="text-center mb-4 text-uppercase fw-bold">Thanh Toán</h2>
        <form action="/process_checkout" method="post" class="form-container">
            <div class="mb-4">
                <label class="form-label fw-bold">📍 Địa Chỉ Nhận Hàng</label>
                <?php if ($address): ?>
                    <input type="hidden" name="address_id" value="<?= $address['AddressID'] ?>">
                    <div class="p-3 border rounded bg-light">
                        <p class="m-0">
                            <?= htmlspecialchars($address['Address']) ?>,
                            <?= htmlspecialchars($address['Ward']) ?>,
                            <?= htmlspecialchars($address['District']) ?>,
                            <?= htmlspecialchars($address['City']) ?>
                        </p>
                    </div>
                    <p id="changeAddress" class="text-primary mt-2" style="cursor: pointer;">📝 Nhập địa chỉ mới</p>
                <?php endif; ?>
                <div id="newAddressFields" class="border p-3 rounded bg-light" style="display: <?= $address ? 'none' : 'block' ?>;">
                    <h5 class="text-primary">Nhập Địa Chỉ Mới</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="new_address" class="form-control" <?= $address ? '' : 'required' ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phường/Xã</label>
                            <input type="text" name="new_ward" class="form-control" <?= $address ? '' : 'required' ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Quận/Huyện</label>
                            <input type="text" name="new_district" class="form-control" <?= $address ? '' : 'required' ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Thành phố</label>
                            <input type="text" name="new_city" class="form-control" <?= $address ? '' : 'required' ?>>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">💳 Chọn Phương Thức Thanh Toán</label>
                <select name="payment_method_id" class="form-select">
                    <?php foreach ($paymentMethods as $method): ?>
                        <option value="<?= $method['PaymentMethodID'] ?>">
                            <?= $method['Name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold py-3 rounded">✅ Xác Nhận Đặt Hàng</button>
        </form>
    </div>
    <footer class="footer">
        <p>© 2025 Meep Bookery. All rights reserved.</p>
        <p>Contact us: info@meepbookery.com | +123 456 789</p>
    </footer>
    <script>
        document.getElementById("changeAddress")?.addEventListener("click", function() {
            const fields = document.getElementById("newAddressFields");
            fields.style.display = "block";

            // Lấy các input trong địa chỉ mới và thêm thuộc tính required
            fields.querySelectorAll("input").forEach(function(input) {
                input.setAttribute("required", "required");
            });
        });
    </script>
</body>

</html>