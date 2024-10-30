<!DOCTYPE html>
<?php
require 'database.php';

// รับค่าจาก form
$order_id = $_POST['order_id'] ?? '';
$order_date = $_POST['order_date'] ?? '';
$product_name = $_POST['product_name'] ?? '';
$product_color = $_POST['product_color'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$product_price = $_POST['product_price'] ?? '';
$total_price = $_POST['total_price'] ?? '';

// ดึงข้อมูล carrier จากตาราง users
$sqlCarrier = "SELECT PhoneNum FROM users WHERE Role = 'Carrier' LIMIT 1";
$resultCarrier = $conn->query($sqlCarrier);
if (!$resultCarrier) {
    echo "Error: " . $conn->error;
}
$carrierData = $resultCarrier->fetch_assoc();

// ดึงข้อมูล customer จากตาราง users
$customer_id = 'CUST001'; // ควรรับค่าจาก session จริงๆ
$sqlCustomer = "SELECT First_name, Last_name, Address, PhoneNum FROM users WHERE User_ID = ?";
$stmtCustomer = $conn->prepare($sqlCustomer);
if (!$stmtCustomer) {
    echo "Prepare failed: " . $conn->error;
}
$stmtCustomer->bind_param("s", $customer_id);
if (!$stmtCustomer->execute()) {
    echo "Execute failed: " . $stmtCustomer->error;
}
$resultCustomer = $stmtCustomer->get_result();
$customerData = $resultCustomer->fetch_assoc();

// บันทึกลงฐานข้อมูล orders
$sql = "INSERT INTO orders (Order_ID, User_ID, Order_date, Order_status, Total_price, Total_product) 
        VALUES (?, ?, ?, 'Pending', ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssdd", $order_id, $customer_id, $order_date, $total_price, $quantity);

if (!$stmt->execute()) {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการที่ต้องจัดส่ง</title>
    <link rel="stylesheet" href="sentStyle.css">
</head>
<body>
    <header>
        <div class="header-title">
        <img src="images/arrow_icon.png" alt="ย้อนกลับ" role="button">
            <h1>รายการที่ต้องจัดส่ง</h1>
        </div>
        <div class="header-icons">
            <a href="#"><img src="images/settings_icon.png" alt="Settings" class="header-icon"></a>
            <a href="#"><img src="images/cart_icon.png" alt="Cart" class="header-icon"></a>
            <a href="#"><img src="images/user_icon.png" alt="User" class="header-icon"></a>
        </div>
    </header>

    <main>
        <div class="order-details">
            <div class="order-info">
                <p><strong>รหัสคำสั่งซื้อ</strong> <?= htmlspecialchars($order_id) ?></p>
                <p><strong>วันที่สั่งสินค้า</strong> <?= date('d M Y H:i', strtotime($order_date)) ?></p>
            </div>

            <section class="delivery-info">
                <h2>ข้อมูลการจัดส่ง</h2>
                <p> ผู้จัดส่ง: CHAINARONG FURNITURE
                    เบอร์โทร: <?= isset($carrierData['PhoneNum']) ? htmlspecialchars($carrierData['PhoneNum']) : 'ไม่ระบุ' ?></p>
            </section>

            <section class="delivery-address">
                <h2>ที่อยู่ในการจัดส่ง</h2>
                <p>ชื่อ: <?= isset($customerData['First_name'], $customerData['Last_name']) ? 
                        htmlspecialchars($customerData['First_name'] . ' ' . $customerData['Last_name']) : 'ไม่ระบุ' ?><br>
                   เบอร์โทร: <?= isset($customerData['PhoneNum']) ? htmlspecialchars($customerData['PhoneNum']) : 'ไม่ระบุ' ?><br>
                   ที่อยู่: <?= isset($customerData['Address']) ? htmlspecialchars($customerData['Address']) : 'ไม่ระบุ' ?>
                </p>
            </section>

        <section class="order-items">
            <h2>คำสั่งซื้อของฉัน</h2>
            <div class="item">
                <img src="<?= htmlspecialchars($productImage) ?>" alt="<?= htmlspecialchars($product_name) ?>" class="item-image">
                <div class="item-details">
                    <h3><?= htmlspecialchars($product_name) ?></h3>
                    <p>จำนวน: x<?= htmlspecialchars($quantity) ?></p>
                    <p>สี: <?= htmlspecialchars($product_color) ?></p>
                    <p class="price">฿<?= number_format($product_price, 2) ?></p>
                </div>
            </div>

            <div class="order-status">
                <p>สถานะ: จัดส่งสินค้า 2 ส.ค.</p>
            </div>
        </div>
    </main>
</body>
</html>