<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการคำสั่งซื้อของฉัน</title>
    <link rel="stylesheet" href="myOrderStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <header>
        <div class="header-title">
            <img src="images/arrow_icon.png" alt="ย้อนกลับ" role="button">
            <h1>รายการคำสั่งซื้อของฉัน</h1>
        </div>
        <div class="header-icons">
            <a href="#"><img src="images/settings_icon.png" alt="Settings" class="header-icon"></a>
            <a href="#"><img src="images/cart_icon.png" alt="Cart" class="header-icon"></a>
            <a href="#"><img src="images/user_icon.png" alt="User" class="header-icon"></a>
        </div>
    </header>

    <main class="order-container">
        <h2>คำสั่งซื้อของฉัน</h2>

        <?php
        // รับข้อมูลจาก createOrder.php
        $productName = $_POST['product_name'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;
        $productColor = $_POST['product_color'] ?? null;
        $productPrice = $_POST['product_price'] ?? null;

        // แสดงรายการสินค้าที่เพิ่งสั่งซื้อ ถ้ามีข้อมูล
        if ($productName && $productColor && $productPrice):
            $totalPrice = $quantity * $productPrice;
        ?>
            <div class="order-card">
                <img src="images/product_placeholder.png" alt="<?= htmlspecialchars($productName); ?>">
                <div class="item-info">
                    <h3><?= htmlspecialchars($productName); ?></h3>
                    <p>จำนวน x<?= htmlspecialchars($quantity); ?></p>
                    <p>สี: <?= htmlspecialchars($productColor); ?></p>
                    <p class="price">฿<?= number_format($totalPrice, 2); ?></p>
                </div>
                <button class="delete-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- แสดงสินค้าที่มีอยู่แล้วในคำสั่งซื้อ -->
        <!-- Item 1, Item 2, Item 3, Item 4 -->

        <div class="footer-container">
            <div>รวม ฿<?= number_format($totalPrice ?? 0, 2); ?></div>
            <button class="order-button">สั่งผลิตสินค้า</button>
        </div>
    </main>

    <script>
        // Function to remove an item by its id
        function removeItem(itemId) {
            const item = document.getElementById(itemId);
            if (item) {
                item.remove();
            }
        }
    </script>
</body>
</html>
