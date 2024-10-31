<?php
// นำเข้าไฟล์ database.php เพื่อเชื่อมต่อฐานข้อมูล
require 'database.php';

// ตั้งค่าเริ่มต้นตัวแปรข้อมูลคำสั่งซื้อและรายการสินค้า
$order = null;
$order_products = [];

// ตรวจสอบการส่ง order_ID เข้ามา
if (isset($_GET['order_ID'])) {
    $order_ID = $conn->real_escape_string($_GET['order_ID']);
    
    // ดึงข้อมูลคำสั่งซื้อจากตาราง orders
    $sql_order = "SELECT * FROM orders WHERE Order_ID = '$order_ID'";
    $result_order = $conn->query($sql_order);
    if ($result_order && $result_order->num_rows > 0) {
        $order = $result_order->fetch_assoc();

        // ดึงข้อมูลที่อยู่ของผู้ใช้จากตาราง USERS โดยใช้ User_ID
        $user_ID = $order['User_ID'];
        $sql_user = "SELECT Address FROM USERS WHERE User_ID = '$user_ID'";
        $result_user = $conn->query($sql_user);
        if ($result_user && $result_user->num_rows > 0) {
            $user = $result_user->fetch_assoc();
            $order['delivery_address'] = $user['Address'];
        }
    }

    // ดึงข้อมูลสินค้าของคำสั่งซื้อนี้จากตาราง order_products
    $sql_products = "SELECT * FROM order_products WHERE Order_ID = '$order_ID'";
    $result_products = $conn->query($sql_products);
    if ($result_products && $result_products->num_rows > 0) {
        while ($product = $result_products->fetch_assoc()) {

            // ดึงชื่อสินค้าและสีจากตาราง products โดยใช้ Product_ID
            $product_ID = $product['Product_ID'];
            $sql_product_details = "SELECT Product_name, Product_color, Product_image FROM products WHERE Product_ID = '$product_ID'";
            $result_product_details = $conn->query($sql_product_details);
            if ($result_product_details && $result_product_details->num_rows > 0) {
                $product_details = $result_product_details->fetch_assoc();
                $product['name'] = $product_details['Product_name'];
                $product['color'] = $product_details['Product_color'];
                $product['image_product'] = $product_details['Product_image'];
            }
            
            $order_products[] = $product;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดคำสั่งซื้อ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="detailStyle.css">
</head>
<body>
    <div class="header">
        <a href="ordersEmployee.php" class="back-button">&lt; รายละเอียดคำสั่งซื้อ</a>

        <a href="settingEmployee.php" class="settings-button" style="position: absolute; top: 20px; left: 1300px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-cog setting-icon"></i>
        </a>

        <a href="profileEmployee.php" class="profile-button" style="position: absolute; top: 20px; left: 1350px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-user profile-icon"></i>
        </a>
    </div>

    <div class="container">
        <?php if ($order): ?>
            <div class="order-info">
                <p><strong>รหัสคำสั่งซื้อ:</strong> <?php echo $order['Order_ID']; ?></p>
                <p><strong>วันที่สั่งสินค้า:</strong> <?php echo $order['Order_date']; ?></p>
            </div>

            <section class="delivery-info">
                <h2>ข้อมูลการจัดส่ง</h2>
                <p>Chainarong Furniture เบอร์โทร : 089-xxx-xxx</p>
            </section>

            <section class="delivery-address">
                <h2>ที่อยู่ในการจัดส่ง</h2>
                <p><?php echo $order['delivery_address']; ?></p>
            </section>

            <section class="order-items">
                <h2>คำสั่งซื้อของฉัน</h2>
                <?php foreach ($order_products as $product): ?>
                    <div class="item">
                        <img src="<?php echo $product['image_product']; ?>" alt="<?php echo $product['name']; ?>" class="item-image">
                        <div class="item-details">
                            <h3>ชื่อสินค้า: <?php echo $product['name']; ?></h3>
                            <p>จำนวน: <?php echo $product['Quantity']; ?></p>
                            <p>สี: <?php echo $product['color']; ?></p>
                            <p>ราคา: <?php echo number_format($product['Sub_totalprice'], 2); ?> บาท</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>

            <div class="order-status">
                <p>สถานะ: จัดส่งสินค้า 
                    <?php
                    // Array of statuses for which we want to display the delivery date
                    $statusesWithDeliveryDate = [
                        'Currently in production', 
                        'Delivery order', 
                        'Claim product', 
                        'Confirm receipt of product', 
                        'Payment made'
                    ];

                    // Check if the current order status is in the array
                    if (in_array($order['Order_status'], $statusesWithDeliveryDate)) {
                        echo $order['Receive_order_date']; // Display delivery date
                    } else {
                        echo $order['Order_status']; // Display order status if not in specified list
                    }
                    ?>
                </p>
            </div>
        <?php else: ?>
            <p>ไม่พบข้อมูลคำสั่งซื้อหรือสินค้า</p>
        <?php endif; ?>
    </div>
</body>
</html>
