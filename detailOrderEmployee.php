<?php
// ตัวอย่างข้อมูลคำสั่งซื้อ
$orders = [
    ['orderID' => 'P001', 'userID' => 'asdfgh456', 'orderDate' => '01/09/2567', 'receiveOrderDate' => '02/09/2567', 'totalPrice' => '3,000', 'carrierName' => 'ปัทมาพร', 'orderStatus' => 'รอดำเนินการ', 'paymaentID' => 'PM001'],
    ['orderID' => 'P002', 'userID' => 'asdfgh456', 'orderDate' => '01/09/2567', 'receiveOrderDate' => '02/09/2567', 'totalPrice' => '3,000', 'carrierName' => 'ปัทมาพร', 'orderStatus' => 'รอดำเนินการ', 'paymaentID' => 'PM002'],
    // เพิ่มรายการคำสั่งซื้ออย่างเพิ่มเติมได้ที่นี่
];

$order_products = [
    [
        'orderID' => 'P001',
        'customerName' => 'อมลณัฐ โศลาแก้ว',
        'address' => 'อมลณัฐ โศลาแก้ว เลขที่ xx หมู่บ้าน xx ซอย xx ถนน xx ตำบล xx อำเภอ xx จังหวัด xx 10000',
        'items' => [
            ['code' => 'D001', 'name' => 'ตู้ลิ้นชักไม้สัก 3 ชั้น', 'color' => 'มอคค่า', 'quantity' => 1, 'price' => 1500, 'image_product' => 'images/w001.png'],
            ['code' => 'W002', 'name' => 'ตู้เสื้อผ้าไม้ประตู 4 ประตู', 'color' => 'มอคค่า', 'quantity' => 1, 'price' => 1500, 'image_product' => 'images/w001.png']
        ],
        'status' => 'จัดส่งสำเร็จ 2 ส.ค.'
    ],[
        'orderID' => 'P002',
        'customerName' => 'อมลณัฐ โศลาแก้ว',
        'address' => 'อมลณัฐ โศลาแก้ว เลขที่ xx หมู่บ้าน xx ซอย xx ถนน xx ตำบล xx อำเภอ xx จังหวัด xx 10000',
        'items' => [
            ['code' => 'D001', 'name' => 'ตู้ลิ้นชักไม้สัก 3 ชั้น', 'color' => 'มอคค่า', 'quantity' => 1, 'price' => 1500, 'image_product' => 'images/w001.png'],
            ['code' => 'W002', 'name' => 'ตู้เสื้อผ้าไม้ประตู 4 ประตู', 'color' => 'มอคค่า', 'quantity' => 1, 'price' => 1500, 'image_product' => 'images/w001.png']
        ],
        'status' => 'จัดส่งสำเร็จ 2 ส.ค.'
    ],
    // เพิ่มข้อมูลคำสั่งซื้อตัวอย่างเพิ่มเติมที่นี่
];

// ตรวจสอบการส่ง orderID เข้ามา
$order = null;
$order_product = null;
if (isset($_GET['orderID'])) {
    $orderID = $_GET['orderID'];
    // ค้นหาข้อมูลคำสั่งซื้อ
    foreach ($orders as $o) {
        if ($o['orderID'] === $orderID) {
            $order = $o;
            break;
        }
    }
    
    // ค้นหาข้อมูลสินค้าของคำสั่งซื้อนี้
    foreach ($order_products as $op) {
        if ($op['orderID'] === $orderID) {
            $order_product = $op;
            break;
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
        <div class="order-info">
            <p><strong>รหัสคำสั่งซื้อ</strong> <?php echo $order['orderID']; ?></p>
            <p><strong>วันที่สั่งสินค้า</strong> <?php echo $order['orderDate']; ?></p>
        </div>

        <section class="delivery-info">
                <h2>ข้อมูลการจัดส่ง</h2>
                <p>ชื่อคนส่งสินค้า : <?php echo $order['carrierName']; ?> เบอร์โทร : 089-xxx-xxx</p>
        </section>

        <section class="delivery-address">
                <h2>ที่อยู่ในการจัดส่ง</h2>
                <p><?php echo $order_product['address']; ?></p>
        </section>

        <section class="order-items">
                <h2>คำสั่งซื้อของฉัน</h2>
                <?php foreach ($order_product['items'] as $item): ?>
                <div class="item">
                    <img src="<?php echo $item['image_product']; ?>" alt="<?php echo $item['name']; ?>" class="item-image">
                    <div class="item-details">
                        <h3>ชื่อสินค้า: <?php echo $item['name']; ?></h3>
                        <p>จำนวน: <?php echo $item['quantity']; ?></p>
                        <p>สี: <?php echo $item['color']; ?></p>
                        <p>ราคา: <?php echo $item['price']; ?> บาท</p>
                    </div>
                </div>
                <?php endforeach; ?>
        </section>

        <div class="order-status">
            <p>สถานะ: <?php echo $order_product['status']; ?></p>
        </div>
    </div>

</body>
</html>
