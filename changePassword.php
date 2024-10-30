<?php
require_once 'database.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // ตรวจสอบว่ามี username นี้ในระบบหรือไม่
    $stmt = $conn->prepare("SELECT User_ID, Username, Password FROM users WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // ตรวจสอบรหัสผ่านเดิม
        if (password_verify($old_password, $user['Password'])) {
            // ตรวจสอบว่ารหัสผ่านใหม่และยืนยันรหัสผ่านตรงกัน
            if ($new_password === $confirm_password) {
                // ตรวจสอบความยาวรหัสผ่านใหม่
                if (strlen($new_password) >= 8) {
                    // เข้ารหัสรหัสผ่านใหม่
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // อัพเดทรหัสผ่านในฐานข้อมูล
                    $update_stmt = $conn->prepare("UPDATE users SET Password = ? WHERE User_ID = ?");
                    $update_stmt->bind_param("ss", $hashed_password, $user['User_ID']);

                    if ($update_stmt->execute()) {
                        $message = "เปลี่ยนรหัสผ่านสำเร็จ";
                    } else {
                        $error = "เกิดข้อผิดพลาดในการอัพเดทรหัสผ่าน";
                    }
                    $update_stmt->close();
                } else {
                    $error = "รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 8 ตัวอักษร";
                }
            } else {
                $error = "รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน";
            }
        } else {
            $error = "รหัสผ่านเดิมไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบชื่อผู้ใช้นี้";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chainarong Furniture - เปลี่ยนรหัสผ่าน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styleChangePassword.css">
    <script>
        function confirmLogout() {
            if(confirm('คุณต้องการออกจากระบบใช่หรือไม่?')) {
                window.location.href = 'logout.php';
            }
        }

        function showAlert(message) {
            alert(message);
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="register-page">
            <p style="text-align:left;">
                <a href="login.php" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </p>
        </div>
        <div class="register-section">
            <div>
                <h1>เปลี่ยนรหัสผ่าน</h1>
            </div>
            <form class="register-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
                <input type="password" name="old_password" placeholder="รหัสผ่านเดิม" required>
                <input type="password" name="new_password" placeholder="รหัสผ่านใหม่" required>
                <input type="password" name="confirm_password" placeholder="ยืนยันรหัสผ่านใหม่" required>
                <button type="submit">เปลี่ยนรหัสผ่าน</button>
            </form>
        </div>
    </div>

    <?php if ($message): ?>
        <script>
            showAlert("<?php echo $message; ?>");
            setTimeout(() => window.location.href = 'login.php', 2000);
        </script>
    <?php endif; ?>

    <?php if ($error): ?>
        <script>
            showAlert("<?php echo $error; ?>");
        </script>
    <?php endif; ?>
</body>
</html>
