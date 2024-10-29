<?php
// ตัวอย่างรายการคำสั่งซื้อ
$order = [
    ['orderID' => 'P001', 'userID' => 'asdfgh456', 'orderDate' => '01/09/2567', 'receiveOrderDate' => '02/09/2567', 'totalPrice' => '3,000', 'carrierName' => 'ปัทมาพร', 'orderStatus' => 'รอดำเนินการ', 'paymaentID' => 'PM001'],
    ['orderID' => 'P002', 'userID' => 'asdfgh456', 'orderDate' => '01/09/2567', 'receiveOrderDate' => '02/09/2567', 'totalPrice' => '3,000', 'carrierName' => 'ปัทมาพร', 'orderStatus' => 'รอดำเนินการ', 'paymaentID' => 'PM002'],
    // เพิ่มรายการคำสั่งซื้ออย่างเพิ่มเติมได้ที่นี่
];

// ตรวจสอบการค้นหา
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    // กรองข้อมูลสินค้าโดยใช้คำค้นหา
    $order = array_filter($order, function($order) use ($search) {
        return stripos($order['orderID'], $search) !== false ||
               stripos($order['userID'], $search) !== false ||
               stripos($order['carrierName'], $search) !== false;
    });
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการคำสั่งซื้อ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="orderStyle.css">

    <script>
        function goToPaymentEvidence() {
            const checkboxes = document.querySelectorAll('input[name="selectedOrders[]"]:checked');
            if (checkboxes.length === 0) {
                alert('กรุณาเลือกคำสั่งซื้ออย่างน้อย 1 รายการ');
                return;
            }
            
            // ใช้เฉพาะคำสั่งซื้อที่เลือก
            const selectedOrderID = checkboxes[0].value; // เลือกคำสั่งซื้อแรกที่ถูกเลือก
            window.location.href = `paymentEvidence.php?orderID=${selectedOrderID}`;
        }
    </script>

</head>
<body>

    <div class="header">
        <a href="profileEmployee.php" class="back-button">&lt; รายการคำสั่งซื้อ</a>

        <form action="ordersEmployee.php" method="get" style="position: absolute; top: 20px; left: 180px; color: white; font-size: 1.2rem; text-decoration: none;">
            <input type="text" name="search" placeholder="ค้นหารหัสคำสั่งซื้อ" value="<?php echo htmlspecialchars($search); ?>">
        </form>

        <a href="settingEmployee.php" class="settings-button" style="position: absolute; top: 20px; left: 1300px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-cog setting-icon"></i>
        </a>

        <a href="profileEmployee.php" class="profile-button" style="position: absolute; top: 20px; left: 1350px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-user profile-icon"></i>
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
                    <th>สถานะคำสั่งซื้อ</th>
                    <th>รหัสการชำระเงิน</th>
                </tr>
            </thead>
            <tbody>
                
                <?php if (count($order) > 0): ?>
                    <?php foreach ($order as $order): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="selectedOrders[]" value="<?php echo $order['orderID']; ?>">
                                <!-- ทำให้รหัสำสั่งซื้อกลายเป็นลิงก์ -->
                                <a href="detailOrderEmployee.php?orderID=<?php echo $order['orderID']; ?>">
                                    <?php echo $order['orderID']; ?>
                                </a>
                            </td>
                            <td><?php echo $order['userID']; ?></td>
                            <td><?php echo $order['orderDate']; ?></td>
                            <td><?php echo $order['receiveOrderDate']; ?></td>
                            <td><?php echo $order['totalPrice']; ?></td>
                            <td><?php echo $order['carrierName']; ?></td>
                            <td>
                                <select id=<?php echo $order['orderStatus']; ?> name="orderStatus">
                                    <option value="Pending">รอดำเนินการ</option>
                                    <option value="Currently in production">กำลังผลิต</option>
                                    <option value="Delivery order">จัดส่งสินค้า</option>
                                    <option value="Payment made">ชำระเงินแล้ว</option>
                                    <option value="Claim product">เคลมสินค้า</option>
                                    <option value="Confirm receipt of product">ยืนยันการรับสินค้า</option>
                                </select>
                            </td>
                            <td><?php echo $order['paymaentID']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">ไม่พบข้อมูลที่ตรงกับการค้นหา</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table> 
        <!-- ปุ่มส่งข้อมูล -->
        <div class="button-container">
            <button type="button" onclick="goToPaymentEvidence()" class="payment-button">หลักฐานการชำระเงิน</button>
        </div>
    </div>

</body>
</html>
