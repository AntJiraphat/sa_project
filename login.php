<?php
session_start();
require_once 'database.php';

// ตรวจสอบว่ามีการล็อกอินอยู่แล้วหรือไม่
if (isset($_SESSION["user"])) {
    // ตรวจสอบ role และ redirect ไปยังหน้าที่เหมาะสม
    switch ($_SESSION["user"]["Role"]) {
        case "Admin":
            header("Location: profileAdmin.php");
            break;
        case "Accountant":
        case "Carrier":
        case "Manufacturer":
            header("Location: ordersEmployee.php");
            break;
        case "Customer":
            header("Location: homePage.php");
            break;
        default:
            header("Location: profileUser.php");
            break;
    }
    exit();
}

$errors = array();
$success = "";

// เมื่อมีการ submit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // ตรวจสอบการล็อกอิน
    $sql = "SELECT * FROM users WHERE Username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // ตรวจสอบรหัสผ่าน
            if (password_verify($password, $user["Password"])) {
                // สร้าง session
                $_SESSION["user"] = [
                    "User_ID" => $user["User_ID"],
                    "Username" => $user["Username"],
                    "Role" => $user["Role"], 
                    "Name" => $user["First_name"] . " " . $user["Last_name"], 
                    "First_name" => $user["First_name"],
                    "Last_name" => $user["Last_name"],
                    "Email" => $user["Email"],
                    "PhoneNum" => $user["PhoneNum"],
                    "Address" => $user["Address"],
                    "Profile_image" => $user["Profile_image"]
                ];

                // Redirect ตาม role
                switch ($user["Role"]) {
                    case "Admin":
                        header("Location: profileAdmin.php");
                        break;
                    case "Accountant":
                    case "Carrier":
                    case "Manufacturer":
                        header("Location: ordersEmployee.php");
                        break;
                    case "Customer":
                        header("Location: homePage.php");
                        break;
                    default:
                        header("Location: profileUser.php");
                        break;
                }
                exit();
            } else {
                $errors[] = "รหัสผ่านไม่ถูกต้อง";
            }
        } else {
            $errors[] = "ไม่พบชื่อผู้ใช้นี้";
        }
        $stmt->close();
    } else {
        $errors[] = "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chainarong Furniture - Login</title>
    <link rel="stylesheet" href="loginStyle.css">
</head>
<body>
    <div class="container">
        <div class="login-register-section">
            <div class="brand-logo">
                <h2>CHAINARONG<br>FURNITURE</h2>
            </div>
            <div style="margin-top: 15px;">
                <h1>เข้าสู่ระบบ</h1>
            </div>

            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
                <input type="password" name="password" placeholder="รหัสผ่าน" required>
                <h2> </h2>
                <button type="submit">เข้าสู่ระบบ</button>
            </form>

            <p><a href="register.php">สร้างบัญชีผู้ใช้</a> | <a href="changePassword.php">ลืมรหัสผ่าน</a></p>
        </div>
        <div class="image-sectionn"></div>
    </div>
</body>
</html>