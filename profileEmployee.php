<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ของฉัน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="profileStyle.css"> 
</head>
<body>

    <div class="header">
        <a href="settingEmployee.php" class="back-button">&lt; โปรไฟล์ของฉัน</a>

        <a href="settingEmployee.php" class="settings-button" style="position: absolute; top: 20px; left: 1300px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-cog setting-icon"></i>
        </a>

        <a href="profileEmployee.php" class="profile-button" style="position: absolute; top: 20px; left: 1350px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-user profile-icon"></i>
        </a>
        
        <div class="profile-info">
            <img src="images/profile.png" alt="Profile Picture" class="profile-picture">
            <div class="profile-details">
                <h2>จิราพัชร ขารสไว</h2>
                <p>@จิราพัชร1234</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="menu">
            <div class="menu-item">
                <i class="fas fa-receipt menu-item-icon"></i>
                <a href="ordersEmployee.php">
                <p>รายการคำสั่งซื้อ</p>
            </div>

            <div class="menu-item">
                <i class="fas fa-plus-circle menu-item-icon"></i>
                <a href="addProduct.php">
                <p>เพิ่มสินค้า</p>
            </div>

            <div class="menu-item">
                <i class="fas fa-box-open menu-item-icon"></i>
                <a href="allProductsEmployee.php">
                <p>สินค้าทั้งหมด</p>
            </div>
        </div>
    </div>
</body>
</html>
