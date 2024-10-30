<?php
session_start();

// ตรวจสอบว่าผู้ใช้ได้ล็อกอินแล้วหรือยัง
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลผู้ใช้จาก session
$user = $_SESSION["user"];

// ตรวจสอบและตั้งค่า Role
$userRole = '';
if (isset($user['Role']) && !empty($user['Role'])) {
    $userRole = trim($user['Role']);
} else {
    $userRole = 'customer'; // ค่าเริ่มต้นถ้าไม่พบ Role
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ของฉัน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styleProfile.css">
</head>
<body>
    <div class="profileUser-header">
        <div class="header-buttons">
            <a class="settings-button" href="javascript:void(0);" 
               onclick="goToSettings('<?php echo htmlspecialchars($userRole); ?>')"
               style="position: absolute; top: 20px; left: 1350px; color: white; font-size: 1.2rem; text-decoration: none;">
                <i class="fas fa-cog"></i> 
            </a>

            <a class="cart-button" href="#" 
               style="position: absolute; top: 20px; right: 60px; color: white; font-size: 1.2rem; text-decoration: none;">
                <i class="fas fa-shopping-cart"></i> 
            </a>

            <a class="logout-button" href="javascript:void(0)" 
               onclick="confirmLogout()" 
               style="position: absolute; top: 20px; right: 20px; color: white; font-size: 1.2rem; text-decoration: none;">
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
                <p>Role: <?php echo htmlspecialchars($userRole); ?></p>
            </div>
        </div>
    </div>

    <div class="profileUser-container">
        <div class="profileUser-menu">
            <div class="profileUser-menu-item" onclick="window.location.href='pending_shipments.php'">
                <i class="fas fa-box profileUser-menu-icon"></i>
                <p>ที่ต้องจัดส่ง</p>
            </div>

            <div class="profileUser-menu-item" onclick="window.location.href='pending_receipts.php'">
                <i class="fas fa-truck profileUser-menu-icon"></i>
                <p>ที่ต้องได้รับ</p>
            </div>

            <div class="profileUser-menu-item" onclick="window.location.href='order_history.php'">
                <i class="fas fa-history profileUser-menu-icon"></i>
                <p>ประวัติการสั่งซื้อ</p>
            </div>
        </div>
    </div>

    <script>
        function confirmLogout() {
            if(confirm('คุณต้องการออกจากระบบใช่หรือไม่?')) {
                window.location.href = 'logout.php';
            }
        }

        function goToSettings(role) {
            if (!role || role.trim() === '') {
                alert('ไม่พบข้อมูล Role กรุณาเข้าสู่ระบบใหม่อีกครั้ง');
                window.location.href = 'logout.php';
                return;
            }
            
            role = role.toLowerCase().trim();
            
            if (['accountant', 'carrier', 'manufacturer'].includes(role)) {
                window.location.href = 'settingEmployee.php';
            } else if (role === 'customer') {
                window.location.href = 'settingUser.php';
            } else {
                alert('ไม่พบประเภทผู้ใช้งาน กรุณาติดต่อผู้ดูแลระบบ');
            }
        }
    </script>
</body>
</html>