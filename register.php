<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

session_start();
$errors = array();
$defaultProfileImage = 'uploads/default_profile.png'; 
$Profile_image = $defaultProfileImage;

if (isset($_SESSION["user"])) {
   header("Location: index.php");
   exit();
}

function ensureUploadsDirectory() {
    $uploadDir = "uploads/";
    
    // เพิ่มการตรวจสอบโฟลเดอร์
    if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
        throw new Exception("โฟลเดอร์สำหรับอัพโหลดไม่พร้อมใช้งาน กรุณาติดต่อผู้ดูแลระบบ");
    }
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception("Failed to create uploads directory");
        }
    }
    
    // Ensure directory is writable
    if (!is_writable($uploadDir)) {
        if (!chmod($uploadDir, 0755)) {
            throw new Exception("Failed to set directory permissions");
        }
    }
    
    return $uploadDir;
}

// Modified file upload handling
if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
    try {
        $uploadDir = ensureUploadsDirectory();
        
        // เพิ่มการตรวจสอบขนาดไฟล์
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        if ($_FILES['profileImage']['size'] > $maxFileSize) {
            throw new Exception("ขนาดไฟล์ใหญ่เกินไป ต้องไม่เกิน 5MB");
        }
        
        // Sanitize filename and ensure it's unique
        $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $_FILES["profileImage"]["name"]);
        $targetFilePath = $uploadDir . $fileName;
        
        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['profileImage']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Invalid file type. Only JPG, PNG and GIF are allowed.");
        }
        
        // Move the uploaded file
        if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFilePath)) {
            $Profile_image = $targetFilePath;
        } else {
            throw new Exception("Failed to upload file: " . error_get_last()['message']);
        }
    } catch (Exception $e) {
        array_push($errors, $e->getMessage());
    }
}

function generateUserID($conn) {
    $query = "SELECT User_ID FROM users ORDER BY User_ID DESC LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastID = intval(substr($row['User_ID'], 6)) + 1;
        return 'USER' . str_pad($lastID, 6, '0', STR_PAD_LEFT);
    } else {
        return 'USER000001';
    }
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if (isset($_POST["submit"])) {
    try {
        $Username = sanitize_input($_POST["username"]);
        $First_name = sanitize_input($_POST["firstName"]);
        $Last_name = sanitize_input($_POST["lastName"]);
        $Email = sanitize_input($_POST["email"]);
        $PhoneNum = sanitize_input($_POST["phoneNum"]);
        $Password = $_POST["password"];
        $Address = sanitize_input($_POST["address"]);
        $Role = 'Customer';
        
        // Validate empty fields
        if (empty($Username)) array_push($errors, "Username is required");
        if (empty($First_name)) array_push($errors, "First name is required");
        if (empty($Last_name)) array_push($errors, "Last name is required");
        if (empty($Email)) array_push($errors, "Email is required");
        if (empty($PhoneNum)) array_push($errors, "Phone number is required");
        if (empty($Password)) array_push($errors, "Password is required");
        else {
            // เพิ่มการตรวจสอบความซับซ้อนของรหัสผ่าน
            if (strlen($Password) < 8) {
                array_push($errors, "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร");
            }
            if (!preg_match("/[A-Z]/", $Password)) {
                array_push($errors, "รหัสผ่านต้องมีตัวพิมพ์ใหญ่อย่างน้อย 1 ตัว");
            }
            if (!preg_match("/[a-z]/", $Password)) {
                array_push($errors, "รหัสผ่านต้องมีตัวพิมพ์เล็กอย่างน้อย 1 ตัว");
            }
            if (!preg_match("/[0-9]/", $Password)) {
                array_push($errors, "รหัสผ่านต้องมีตัวเลขอย่างน้อย 1 ตัว");
            }
        }
        if (empty($Address)) array_push($errors, "Address is required");

        // เพิ่มการตรวจสอบ username และ email ซ้ำ
        // ตรวจสอบ username ซ้ำ
        $checkUsername = "SELECT Username FROM users WHERE Username = ?";
        if($stmt = $conn->prepare($checkUsername)) {
            $stmt->bind_param("s", $Username);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 0) {
                array_push($errors, "ชื่อผู้ใช้นี้ถูกใช้งานแล้ว");
            }
            $stmt->close();
        }

        // ตรวจสอบ email ซ้ำ
        $checkEmail = "SELECT Email FROM users WHERE Email = ?";
        if($stmt = $conn->prepare($checkEmail)) {
            $stmt->bind_param("s", $Email);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 0) {
                array_push($errors, "อีเมลนี้ถูกใช้งานแล้ว");
            }
            $stmt->close();
        }
        
        // ถ้าไม่มี error ให้ทำการ insert
        if (count($errors) == 0) {
            $userID = generateUserID($conn);
            // เตรียม SQL statement
            $sql = "INSERT INTO users (User_ID, Username, First_name, Last_name, Email, PhoneNum, Password, Address, Role, Profile_image) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                // เข้ารหัสรหัสผ่าน
                $hashedPassword = password_hash($Password, PASSWORD_DEFAULT);
                
                // ผูกพารามิเตอร์
                $stmt->bind_param("ssssssssss", 
                    $userID,
                    $Username, 
                    $First_name, 
                    $Last_name, 
                    $Email, 
                    $PhoneNum, 
                    $hashedPassword, 
                    $Address, 
                    $Role,
                    $Profile_image
                );
                
                // ทำการ execute
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>ลงทะเบียนสำเร็จ</div>";
                    // Redirect ไปหน้า login
                    header("Location: login.php");
                    exit();
                } else {
                    throw new Exception("Error executing statement: " . $stmt->error);
                }
                
                $stmt->close();
            } else {
                throw new Exception("Error preparing statement: " . $conn->error);
            }
        } else {
            // แสดง errors
            foreach($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chainarong Furniture - Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styleRegister.css">
    
    <script>
    function validateForm(event) {
        let username = document.getElementById("username").value;
        let firstName = document.getElementById("firstName").value;
        let lastName = document.getElementById("lastName").value;
        let email = document.getElementById("email").value;
        let phoneNum = document.getElementById("phoneNum").value;
        let password = document.getElementById("password").value;
        let address = document.getElementById("address").value;
        
        let errors = [];
        
        // Validate required fields
        if (!username) errors.push("กรุณากรอกชื่อผู้ใช้");
        if (!firstName) errors.push("กรุณากรอกชื่อ");
        if (!lastName) errors.push("กรุณากรอกนามสกุล");
        if (!email) errors.push("กรุณากรอกอีเมล");
        if (!phoneNum) errors.push("กรุณากรอกเบอร์โทรศัพท์");
        if (!password) errors.push("กรุณากรอกรหัสผ่าน");
        if (!address) errors.push("กรุณากรอกที่อยู่");
        
        // เพิ่มการตรวจสอบรหัสผ่าน
        if (password) {
            if (password.length < 8) {
                errors.push("รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร");
            }
            if (!/[A-Z]/.test(password)) {
                errors.push("รหัสผ่านต้องมีตัวพิมพ์ใหญ่อย่างน้อย 1 ตัว");
            }
            if (!/[a-z]/.test(password)) {
                errors.push("รหัสผ่านต้องมีตัวพิมพ์เล็กอย่างน้อย 1 ตัว");
            }
            if (!/[0-9]/.test(password)) {
                errors.push("รหัสผ่านต้องมีตัวเลขอย่างน้อย 1 ตัว");
            }
        }
        
        // Validate email format
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && !emailRegex.test(email)) {
            errors.push("รูปแบบอีเมลไม่ถูกต้อง");
        }
        
        // Validate phone number (10 digits)
        let phoneRegex = /^[0-9]{10}$/;
        if (phoneNum && !phoneRegex.test(phoneNum)) {
            errors.push("เบอร์โทรศัพท์ต้องเป็นตัวเลข 10 หลัก");
        }
        
        if (errors.length > 0) {
            alert(errors.join("\n"));
            event.preventDefault();
            return false;
        }
        
        return true;
    }
    
    window.onload = function() {
        document.querySelector("form").addEventListener("submit", validateForm);
    }
    </script>
</head>
<body>
<div class="container">
    <div class="register-page">
        <p style="text-align:left;">
            <a href="login.php" class="back-link">
            <i class="fas fa-arrow-left"></i></a>
        </p>
        <h1 class="footer-text">CHAINARONG FURNITURE</h1>
    </div>

    <div class="register-section">
        <div>
            <h1>สร้างบัญชีผู้ใช้</h1>
        </div>

        <form class="register-form" method="POST" action="register.php" enctype="multipart/form-data" novalidate>
            <div class="input-group full-width">
                <label for="username">ชื่อผู้ใช้</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="firstName">ชื่อ</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>
            <div class="input-group">
                <label for="lastName">นามสกุล</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>
            <div class="input-group">
                <label for="email">อีเมล</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="phoneNum">เบอร์โทรศัพท์</label>
                <input type="tel" id="phoneNum" name="phoneNum" maxlength="10" required>
            </div>
            <div class="input-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group full-width">
                <label for="address">ที่อยู่</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="profile-image-container">
                <label for="profileImage">เลือกรูปโปรไฟล์:</label>
                <input type="file" id="profileImage" name="profileImage" accept="image/*" onchange="previewImage(event)">
                <img id="profilePreview" src="#" alt="Preview Profile Image" style="display:none;">
            </div>
            <div class="form-btn">
                <button type="submit" name="submit">สร้างบัญชี</button>
            </div>
        </form>
    </div>
</div>
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('profilePreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
</body>
</html>