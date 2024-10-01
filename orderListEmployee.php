<?php
// ตัวอย่างรายการคำสั่งซื้อ
$products = [
    ['orderID' => 'P001', 'userID' => 'asdfgh456', 'orderDate' => '01/09/2567', 'receiveOrderDate' => '02/09/2567', 'totalPrice' => '3,000', 'carrierName' => 'ปัทมาพร', 'doImage' => 'DO_P001', 'slipImage' => 'Slip_P001', 'receiptImage' => 'Receipt_P001', 'orderStatus' => 'รอดำเนินการ'],
    // เพิ่มรายการคำสั่งซื้ออย่างเพิ่มเติมได้ที่นี่
];

// ตรวจสอบการค้นหา
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    // กรองข้อมูลสินค้าโดยใช้คำค้นหา
    $products = array_filter($products, function($product) use ($search) {
        return stripos($product['orderID'], $search) !== false ||
               stripos($product['userID'], $search) !== false ||
               stripos($product['carrierName'], $search) !== false;
    });
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการคำสั่งซื้อ</title>
    <link rel="stylesheet" href="styleEmployee.css">

</head>
<body>

    <div class="header">
        <a href="profileEmployee.php" class="back-button">&larr; </a>

        <span>รายการคำสั่งซื้อ</span>

        <form action="all_products_search.php" method="get">
            <input type="text" name="search" placeholder="ค้นหา" value="<?php echo htmlspecialchars($search); ?>">
        </form>

        <a href="settingEmployee.php" class="settings-button">
            <img src="images/setting_icon.png" alt="Setting" class="setting-icon">
        </a>

        <a href="profileEmployee.php" class="profile-button">
            <img src="images/profile_icon.png" alt="Profile" class="profile-icon">
        </a>

    </div>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>รหัสคำสั่งซื้อ</th>
                    <th>รหัสลูกค้า</th>
                    <th>วันที่สั่งสินค้า</th>
                    <th>วันที่ส่งสินค้า</th>
                    <th>ยอดรวมสินค้า</th>
                    <th>ชื่อพนักงานส่งสินค้า</th>
                    <th>ใบส่งสินค้า</th>
                    <th>สลิปของลูกค้า</th>
                    <th>ใบเสร็จสินค้า</th>
                    <th>สถานะคำสั่งซื้อ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['orderID']; ?></td>
                            <td><?php echo $product['userID']; ?></td>
                            <td><?php echo $product['orderDate']; ?></td>
                            <td><?php echo $product['receiveOrderDate']; ?></td>
                            <td><?php echo $product['totalPrice']; ?></td>
                            <td><?php echo $product['carrierName']; ?></td>
                            <td><?php echo $product['doImage']; ?></td>
                            <td><?php echo $product['slipImage']; ?></td>
                            <td><?php echo $product['receiptImage']; ?></td>
                            <td><?php echo $product['orderStatus']; ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">ไม่พบข้อมูลที่ตรงกับการค้นหา</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
