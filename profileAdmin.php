<?php
session_start();

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่าเป็น admin หรือไม่ (เปลี่ยนเป็น Admin ตัวใหญ่)
if ($_SESSION["user"]["Role"] !== "Admin") {
    header("Location: profileUser.php");
    exit();
}

// ดึงข้อมูลผู้ใช้จาก session
$user = $_SESSION["user"];

// Debug: ตรวจสอบค่า Role
error_log("User Role: " . $_SESSION["user"]["Role"]);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chainarong Furniture - Admin Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styleProfile.css">
</head>
<body>
    <div class="profileUser-header">
        <div class="header-buttons" style="position: absolute; top: 20px; right: 20px;">
            <a href="logout.php" style="color: gray; font-size: 1.2rem;">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>

        <div class="profileUser-info">
            <?php if (!empty($user['Profile_image']) && file_exists('uploads/' . $user['Profile_image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($user['Profile_image']); ?>" alt="Profile Picture" class="profileUser-picture" style="width: 5rem; height: 5rem; border-radius: 50%;">
            <?php else: ?>
                <i class="fas fa-user-circle profileUser-picture" style="font-size: 5rem; color: white;"></i>
            <?php endif; ?>
            <div class="profileUser-details">
            <h2><?php echo htmlspecialchars($user['Name'] ?? 'ผู้ใช้งาน'); ?></h2>
                <p>@<?php echo htmlspecialchars($user['Username'] ?? 'username'); ?></p>
            </div>
        </div>
    </div>

    <div class="profileUser-container">
        <div class="profileUser-menu">
            <a href="registerEmployee.php" class="profileUser-menu-item">
                <i class="fas fa-user-plus profileUser-menu-icon"></i>
                <p>สร้างบัญชีพนักงาน</p>
            </a>

            <a href="listEmployee.php" class="profileUser-menu-item">
                <i class="fas fa-users profileUser-menu-icon"></i>
                <p>รายชื่อพนักงาน</p>
            </a>

            <a href="addProduct.php" class="profileUser-menu-item">
                <i class="fas fa-plus-circle profileUser-menu-icon"></i>
                <p>เพิ่มสินค้า</p>
            </a>

            <a href="allProductEmployee.php" class="profileUser-menu-item">
                <i class="fas fa-box-open profileUser-menu-icon"></i>
                <p>สินค้าทั้งหมด</p>
            </a>
    </div>


</body>
</html>