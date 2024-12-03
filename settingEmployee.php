<?php
session_start();

// ตรวจสอบว่ามีการล็อกอินหรือไม่
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// เชื่อมต่อกับฐานข้อมูล
require_once 'database.php';

// ดึงข้อมูลผู้ใช้จาก session
$user = $_SESSION["user"];

// ตรวจสอบว่ามีข้อมูล Username หรือไม่
if (isset($user['Username']) && !empty($user['Username'])) {
    $username = $user['Username'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    } else {
        echo "ไม่พบข้อมูลผู้ใช้ในฐานข้อมูล";
        exit();
    }
} else {
    echo "ไม่พบข้อมูลผู้ใช้ใน session";
    exit();
}

// จัดการการอัพเดตข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['username'];
    $currentUsername = $username;
    $firstName = $_POST['firstName'];
    $lastName = $_POST['Last_Name'];
    $email = $_POST['Email'];
    $phoneNum = $_POST['PhoneNum'];
    $address = $_POST['address'];
    $errors = array();

    // ตรวจสอบ username ซ้ำ
    if ($newUsername !== $currentUsername) {
        $checkUsername = "SELECT Username FROM users WHERE Username = ? AND Username != ?";
        if($stmt = $conn->prepare($checkUsername)) {
            $stmt->bind_param("ss", $newUsername, $currentUsername);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 0) {
                array_push($errors, "ชื่อผู้ใช้นี้ถูกใช้งานแล้ว");
            }
            $stmt->close();
        }
    }
    
    // จัดการการอัปโหลดรูปโปรไฟล์
    $profile_image = !empty($userData['Profile_image']) ? $userData['Profile_image'] : 'default-profile.png';
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        $file_extension = strtolower(pathinfo($_FILES["profileImage"]["name"], PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_types)) {
            $new_filename = time() . '_profile.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            if (!empty($userData['Profile_image']) && $userData['Profile_image'] != 'default-profile.png' && file_exists('uploads/' . $userData['Profile_image'])) {
                unlink('uploads/' . $userData['Profile_image']);
            }
            
            if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $target_file)) {
                $profile_image = $new_filename;
            } else {
                array_push($errors, "เกิดข้อผิดพลาดในการอัปโหลดไฟล์");
            }
        } else {
            array_push($errors, "อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG และ GIF เท่านั้น");
        }
    }

    // ถ้าไม่มี error ให้อัพเดตข้อมูล
    if (empty($errors)) {
        $update_sql = "UPDATE users SET 
                    Username = ?, 
                    First_name = ?, 
                    Last_name = ?, 
                    Email = ?, 
                    PhoneNum = ?, 
                    Address = ?, 
                    Profile_image = ?
                    WHERE Username = ?";
        
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssssss", $newUsername, $firstName, $lastName, $email, $phoneNum, $address, $profile_image, $currentUsername);

        if ($stmt->execute()) {
            // อัพเดต session ให้สอดคล้องกับข้อมูลใหม่
            $_SESSION['user'] = array(
                'Username' => $newUsername,
                'Name' => $firstName . ' ' . $lastName,
                'First_name' => $firstName,
                'Last_name' => $lastName,
                'Email' => $email,
                'PhoneNum' => $phoneNum,
                'Address' => $address,
                'Role' => $user['Role'],
                'Profile_image' => $profile_image
            );
            
            $_SESSION['success_message'] = "อัพเดตข้อมูลสำเร็จ";
            header("Location: settingUser.php");
            exit();
        } else {
            array_push($errors, "เกิดข้อผิดพลาดในการอัพเดตข้อมูล: " . $stmt->error);
        }
    }

    // แสดงข้อผิดพลาดถ้ามี
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("\\n", $errors);
    }
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chainarong Furniture - Setting</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="settingStyle.css">
</head>
<body>
    <div class="container">
        <div class="register-page">
            <p style="text-align:left;">
                <a href="profileUser.php"><i class="fas fa-arrow-left"></i></a>
            </p>
            <h1 class="footer-text">CHAINARONG FURNITURE</h1>
        </div>
        <div class="register-section">
            <div>
                <h1>ตั้งค่าบัญชี</h1>
            </div>
            <form class="register-form" method="POST" enctype="multipart/form-data">
                <div class="input-group full-width">
                    <label for="username">ชื่อผู้ใช้</label>
                    <input type="text" id="username" name="username" 
                        value="<?php echo isset($userData['Username']) ? htmlspecialchars($userData['Username']) : ''; ?>" required>
                </div>
                <div class="input-group">
                    <label for="firstName">ชื่อ</label>
                    <input type="text" id="firstName" name="firstName" 
                        value="<?php echo isset($userData['First_name']) ? htmlspecialchars($userData['First_name']) : ''; ?>" required>
                </div>
                <div class="input-group">
                    <label for="Last_Name">นามสกุล</label>
                    <input type="text" id="Last_Name" name="Last_Name" 
                        value="<?php echo isset($userData['Last_name']) ? htmlspecialchars($userData['Last_name']) : ''; ?>" required>
                </div>
                <div class="input-group">
                    <label for="Email">อีเมล</label>
                    <input type="email" id="Email" name="Email" 
                        value="<?php echo isset($userData['Email']) ? htmlspecialchars($userData['Email']) : ''; ?>" required>
                </div>
                <div class="input-group">
                    <label for="PhoneNum">เบอร์โทรศัพท์</label>
                    <input type="text" id="PhoneNum" name="PhoneNum" 
                        value="<?php echo isset($userData['PhoneNum']) ? htmlspecialchars($userData['PhoneNum']) : ''; ?>" required>
                </div>
                <div class="input-group full-width">
                    <label for="address">ที่อยู่</label>
                    <input type="text" id="address" name="address" 
                        value="<?php echo isset($userData['Address']) ? htmlspecialchars($userData['Address']) : ''; ?>" required>
                </div>
                <div class="profile-image-container">
                    <label for="profileImage">เลือกรูปโปรไฟล์:</label>
                    <input type="file" id="profileImage" name="profileImage" accept="image/*" onchange="previewImage(event)">
                    <div class="profile-image-wrapper">
                        <?php
                        $profileImagePath = '';
                        if (!empty($userData['Profile_image'])) {
                            if (file_exists('uploads/' . $userData['Profile_image'])) {
                                $profileImagePath = 'uploads/' . htmlspecialchars($userData['Profile_image']);
                            }
                        }
                        
                        if ($profileImagePath) {
                            echo "<img id='profilePreview' src='$profileImagePath' alt='Profile Image'>";
                        } else {
                            echo "<img id='profilePreview' src='uploads/default-profile.png' alt='Default Profile Image'>";
                        }
                        ?>
                    </div>
                </div>
                <button type="submit" name="save" class="btn">ยืนยัน</button>
                <div class="additional-buttons">
                    <a href="changePassword.php" class="button">เปลี่ยนรหัสผ่าน</a>
                    <a href="logout.php" class="button">ออกจากระบบ</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        function previewImage(event) {
            var output = document.getElementById('profileImagePreview');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src) 
            }
        }
        
        <?php if (isset($_SESSION['success_message'])): ?>
            alert("<?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>");
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            alert("<?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>");
        <?php endif; ?>
    </script>
</body>
</html>