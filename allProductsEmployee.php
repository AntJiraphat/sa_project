<?php

// เรียกใช้การเชื่อมต่อฐานข้อมูล
require 'database.php';

// ตรวจสอบการค้นหา
$search = '';
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']); 
    // ปรับคำสั่ง SQL ให้กรองด้วยคำค้นหา
    $sql = "SELECT * FROM products WHERE Product_name LIKE '%$search%' OR Product_ID LIKE '%$search%' OR Product_detail LIKE '%$search%'";
} else {
    // ดึงข้อมูลทั้งหมดถ้าไม่มีคำค้นหา
    $sql = "SELECT * FROM products";
}

$result = $conn->query($sql);
if (!$result) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สินค้าทั้งหมด</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="allProductsStyle.css">

</head>
<body>

    <div class="header">
        <a href="profileAdmin.php" class="back-button">&lt; สินค้าทั้งหมด</a>

        <form action="allProductsEmployee.php" method="get" style="position: absolute; top: 20px; left: 180px; color: white; font-size: 1.2rem; text-decoration: none;">
            <input type="text" name="search" placeholder="ค้นหาสินค้า" value="<?php echo htmlspecialchars($search); ?>">
        </form>

        <a href="settingEmployee.php" class="settings-button" style="position: absolute; top: 20px; left: 1300px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-cog setting-icon"></i>
        </a>

        <a href="profileAdmin.php" class="profile-button" style="position: absolute; top: 20px; left: 1350px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-user profile-icon"></i>
        </a>

    </div>


    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>รหัสสินค้า</th>
                    <th>ชื่อสินค้า</th>
                    <th>รูปสินค้า</th>
                    <th>รายละเอียดสินค้า</th>
                    <th>ขนาดสินค้า</th>
                    <th>สี</th>
                    <th>ประเภท</th>
                    <th>ราคา</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($product = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <!-- ทำให้รหัสสินค้ากลายเป็นลิงก์ -->
                                <a href="editProduct.php?product_ID=<?php echo $product['Product_ID']; ?>"><?php echo $product['Product_ID']; ?></a>
                                </a>
                            </td>
                            <td><?php echo $product['Product_name']; ?></td>
                            <td><img src="<?php echo $product['Product_image']; ?>" alt="<?php echo $product['Product_name']; ?>" width="100"></td>
                            <td><?php echo $product['Product_detail']; ?></td>
                            <td><?php echo $product['Product_size']; ?></td>
                            <td><?php echo $product['Product_color']; ?></td>
                            <td><?php echo $product['Product_type']; ?></td>
                            <td><?php echo number_format($product['Product_price'], 2); ?> บาท</td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">ไม่พบสินค้าที่จะแสดง</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php
// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
