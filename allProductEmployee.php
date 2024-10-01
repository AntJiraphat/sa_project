<?php
// ตัวอย่างข้อมูลสินค้า
$products = [
    ['code' => 'WS001', 'name' => 'ตู้เสื้อผ้า', 'description' => 'ตู้เสื้อผ้า', 'size' => '80x50x180', 'color' => 'คาปูชิโน่', 'type' => 'ตู้เสื้อผ้า', 'price' => '6,500'],
    // เพิ่มสินค้าตัวอย่างเพิ่มเติมได้ที่นี่
];

// ตรวจสอบการค้นหา
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    // กรองข้อมูลสินค้าโดยใช้คำค้นหา
    $products = array_filter($products, function($product) use ($search) {
        return stripos($product['name'], $search) !== false ||
               stripos($product['code'], $search) !== false ||
               stripos($product['description'], $search) !== false;
    });
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สินค้าทั้งหมด</title>
    <link rel="stylesheet" href="styleEmployee.css">

</head>
<body>

    <div class="header">
        <a href="profileEmployee.php" class="back-button">&larr; </a>

        <span>สินค้าทั้งหมด</span>

        <form action="all_products_search.php" method="get">
            <input type="text" name="search" placeholder="ค้นหาสินค้า" value="<?php echo htmlspecialchars($search); ?>">
        </form>

        <a href="settingEmployee.php" class="settings-button">
            <img src="images/setting_icon.png" alt="Setting" class="setting-icon">
        </a>

        <a href="profileEmployee.php" class="profile-button">
            <img src="images/profile_icon.png" alt="Profile" class="profile-icon">
        </a>

    </div>

    <!-- <div class="search-box">
        <form action="all_products_search.php" method="get">
            <input type="text" name="search" placeholder="ค้นหาสินค้า" value="<?php echo htmlspecialchars($search); ?>">
        </form>
    </div> -->

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>รหัสสินค้า</th>
                    <th>ชื่อสินค้า</th>
                    <th>รายละเอียดสินค้า</th>
                    <th>ขนาดสินค้า</th>
                    <th>สี</th>
                    <th>ประเภท</th>
                    <th>ราคา</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['code']; ?></td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['description']; ?></td>
                            <td><?php echo $product['size']; ?></td>
                            <td><?php echo $product['color']; ?></td>
                            <td><?php echo $product['type']; ?></td>
                            <td><?php echo $product['price']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">ไม่พบสินค้าที่ตรงกับการค้นหา</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
