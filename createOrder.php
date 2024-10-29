<?php
// Connect to the database
require 'database.php';

// Get the product_id from the URL
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// Fetch the specific product based on product_id
$sql = "SELECT * FROM products WHERE Product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสินค้า</title>
    <link rel="stylesheet" href="createOrderStyle.css"> 
</head>
<body>
    <header>
        <div class="header-title">
            <img src="images/arrow_icon.png" alt="ย้อนกลับ" role="button">
            <h1>รายละเอียดสินค้า</h1>
        </div>
        <div class="header-icons">
            <a href="#"><img src="images/settings_icon.png" alt="Settings" class="header-icon"></a>
            <a href="#"><img src="images/cart_icon.png" alt="Cart" class="header-icon"></a>
            <a href="#"><img src="images/user_icon.png" alt="User" class="header-icon"></a>
        </div>
    </header>

    <main class="product-container">
        <div class="product-info">
            <img src="<?= htmlspecialchars($row['Product_image']); ?>" alt="<?= htmlspecialchars($row['Product_name']); ?>" class="product-image">
            <div class="product-card">
                <h2 class="Product_name"><?= htmlspecialchars($row['Product_name']); ?></h2>
                <p class="Product_detail"><?= htmlspecialchars($row['Product_detail']); ?></p>
                <div class="product-specs">
                    <p><strong>ประเภท:</strong> <span class="Product_type"><?= htmlspecialchars($row['Product_type']); ?></span></p>
                    <p><strong>ขนาดสินค้า:</strong> <span class="Product_size"><?= htmlspecialchars($row['Product_size']); ?></span></p>
                    <p><strong>สี:</strong> <span class="Product_color"><?= htmlspecialchars($row['Product_color']); ?></span></p>
                </div>
                <p><strong>ราคา:</strong> <span class="Product_price"><?= number_format($row['Product_price'], 2); ?> ฿</span></p>
                <div class="quantity">
                    <label for="quantity">จำนวนสินค้า:</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1">
                </div>
            </div>
        </div>
    </main>

    <div class="bottom-container">
        <form action="myOrder.php" method="POST">
            <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['Product_name']); ?>">
            <input type="hidden" name="product_color" value="<?= htmlspecialchars($product['Product_color']); ?>">
            <input type="hidden" name="product_price" value="<?= htmlspecialchars($product['Product_price']); ?>">

            <div class="button-group">
                <button type="button" class="add-to-cart">เพิ่มไปยังรายการ</button>
                <button type="submit" class="order-btn">สั่งสินค้า</button>
            </div>
        </form>
    </div>
</body>
</html>
