<?php
session_start();
require_once 'database.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// ตรวจสอบว่าเป็นการส่งข้อมูลแบบ POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: homePage.php');
    exit();
}

// ตรวจสอบข้อมูลที่จำเป็น
if (empty($_POST['order_id']) || empty($_POST['product_name'])) {
    $_SESSION['error'] = 'ข้อมูลไม่ครบถ้วน';
    header('Location: myOrder.php');
    exit();
}

// รับค่าจากฟอร์ม
$order_id = $_POST['order_id'];
$user_id = $_SESSION['user']['User_ID'];
$order_date = $_POST['order_date'] ?? date('Y-m-d H:i:s');
$total_price = $_POST['total_price'];
$total_product = $_POST['quantity'];

// รับค่าสำหรับแสดงผล
$product_name = $_POST['product_name'];
$product_color = $_POST['product_color'];
$product_price = $_POST['product_price'];
$productImage = $_POST['product_image'];
$quantity = $_POST['quantity'];

// ดึงข้อมูล carrier
$sqlCarrier = "SELECT PhoneNum FROM users WHERE Role = 'Carrier' LIMIT 1";
$resultCarrier = $conn->query($sqlCarrier);
$carrierData = $resultCarrier->fetch_assoc();

// ดึงข้อมูลลูกค้า
$sqlCustomer = "SELECT * FROM users WHERE User_ID = ?";
$stmtCustomer = $conn->prepare($sqlCustomer);
$stmtCustomer->bind_param("s", $user_id);
$stmtCustomer->execute();
$resultCustomer = $stmtCustomer->get_result();
$customerData = $resultCustomer->fetch_assoc();

// SQL สำหรับบันทึกข้อมูล
$sql = "INSERT INTO orders (Order_ID, User_ID, Order_date, Order_status, Total_price, Total_product) 
        VALUES (?, ?, ?, 'Pending', ?, ?)";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdd", $order_id, $user_id, $order_date, $total_price, $total_product);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "บันทึกคำสั่งซื้อเรียบร้อยแล้ว";
    } else {
        throw new Exception("เกิดข้อผิดพลาดในการบันทึกข้อมูล");
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
} finally {
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการที่ต้องจัดส่ง</title>
    <link rel="stylesheet" href="sentStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <header>
        <div class="header-title">
            <i class="fas fa-arrow-left" onclick="window.history.back();"></i>
            <h1>รายการที่ต้องจัดส่ง</h1>
        </div>
        <div class="header-icons">
            <a href="homePage.php">
                <i class="fas fa-home"></i>
            </a>
            <a href="settingUser.php">
                <i class="fas fa-cog"></i>
            </a> 
            <a href="cart.php">
                <i class="fas fa-shopping-cart"></i>
            </a>
            <a href="profileUser.php">
                <i class="fas fa-user"></i>
            </a>
        </div>
    </header>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

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
                <p>สถานะ: จัดส่งสินค้า <?= date('Y M d') ?></p>
            </div>

            <div class="actions">
                <button onclick="window.location.href='homePage.php'" class="btn btn-primary">
                    กลับหน้าหลัก
                </button>
            </div>
        </div>
    </main>
</body>
</html>