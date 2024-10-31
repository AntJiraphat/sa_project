<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// นำเข้าไฟล์ database.php เพื่อเชื่อมต่อฐานข้อมูล
require 'database.php';

// ตรวจสอบว่าคำขอเป็นแบบ POST สำหรับการเพิ่มข้อมูลลงในฐานข้อมูล
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Content-Type: application/json; charset=UTF-8");

    // รับค่าจากฟอร์ม
    $product_name = $_POST['product_name'];
    $product_detail = $_POST['product_detail'];
    $product_size = $_POST['product_size'];
    $product_color = $_POST['product_color'];
    $product_type = $_POST['product_type'];
    $product_price = $_POST['product_price'];

    // พิมพ์ค่าอินพุตออกมาเพื่อตรวจสอบ
    echo "product_name: " . $product_name . "<br>";
    echo "product_detail: " . $product_detail . "<br>";
    echo "product_size: " . $product_size . "<br>";
    echo "product_color: " . $product_color . "<br>";
    echo "product_type: " . $product_type . "<br>";
    echo "product_price: " . $product_price . "<br>";

    // ตรวจสอบและจัดการอัปโหลดรูปภาพ
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $upload_dir = "uploadProduct/"; 
        $file_name = basename($_FILES['product_image']['name']);
        $target_file = $upload_dir . uniqid() . "_" . $file_name;

        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($image_file_type, $allowed_types)) {
            echo json_encode(["message" => "รูปภาพต้องเป็นไฟล์ JPG, JPEG, PNG หรือ GIF เท่านั้น"]);
            exit();
        }

        if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
            echo json_encode(["message" => "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ"]);
            exit();
        }
        $product_image = $target_file;
        echo "product_image: " . $product_image . "<br>";
    } else {
        $product_image = null; // กำหนดค่าเริ่มต้นหากไม่มีการอัปโหลดรูปภาพ
    }

        // Prepare new Product ID based on product type
    $type_prefix = '';
    switch ($product_type) {
        case 'Wardrobe':
            $type_prefix = 'WD';
            break;
        case 'Bed':
            $type_prefix = 'BD';
            break;
        case 'Cabinet':
            $type_prefix = 'CB';
            break;
        case 'Vanity':
            $type_prefix = 'VN';
            break;
        default:
            $type_prefix = 'PR'; // Default prefix if the type does not match any case
            break;
    }

    // Query to get the last Product_ID for the specific type
    $sql = "SELECT Product_ID FROM products WHERE Product_ID LIKE '{$type_prefix}%' ORDER BY Product_ID DESC LIMIT 1";
    $result = $conn->query($sql);
    $last_id = $result->fetch_assoc();
    $last_numeric_id = $last_id ? (int)substr($last_id['Product_ID'], 2) : 0;
    $next_id = $type_prefix . str_pad($last_numeric_id + 1, 8, '0', STR_PAD_LEFT);

    // Print the Product ID to be used
    echo "next_id: " . $next_id . "<br>";

    // บันทึกข้อมูลลงฐานข้อมูล
    $sql = "INSERT INTO products (Product_ID, Product_name, Product_price, Product_image, Product_detail, Product_color, Product_type, Product_size)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $next_id, $product_name, $product_price, $product_image, $product_detail, $product_color, $product_type, $product_size);

    if ($stmt->execute()) {
        echo "<script>alert('เพิ่มสินค้าเรียบร้อยแล้ว');</script>";
        header("Location: allProductsEmployee.php");
        exit();
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้า</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="addStyle.css">

    <!-- เพิ่มสคริปต์สำหรับแสดงภาพที่อัปโหลด -->
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('output');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

    </script>

</head>
<body>
    
    <div class="header">
        <a href="profileAdmin.php" class="back-button">&lt; เพิ่มสินค้า</a>

        <a href="settingEmployee.php" class="settings-button" style="position: absolute; top: 20px; left: 1300px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-cog setting-icon"></i>
        </a>

        <a href="profileEmployee.php" class="profile-button" style="position: absolute; top: 20px; left: 1350px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-user profile-icon"></i>
        </a>

    </div>

    <div class="container">
        <!-- ส่งข้อมูลที่เพิ่มไปหน้าที่ส่งให้ฐานข้อมูล -->
        <form action="addProduct.php" method="POST" enctype="multipart/form-data">
                <div class="form-wrapper">
                <!-- Left side -->
                <div class="left">
                    <!-- Image Upload -->
                    <div class="image-upload">
                        <label for="file-input">
                            <img id="output" src="images/camera_icon.png" alt="Upload Image">
                        </label>
                        <input id="file-input" type="file" name="product_image" onchange="previewImage(event)">
                    </div>
                </div>

                <!-- Right side -->
                <div class="right">
                    <!-- Product Name -->
                    <label for="product_name">ชื่อสินค้า</label>
                    <input type="text" id="product_name" name="product_name" placeholder="ตู้เสื้อผ้า" required>

                    <!-- Product Detail -->
                    <label for="product_detail">รายละเอียดสินค้า</label>
                    <textarea id="product_detail" name="product_detail" rows="4" placeholder="รายละเอียดสินค้า" required></textarea>

                    <!-- Product Size -->
                    <label for="product_size">ขนาดสินค้า (กว้าง x ยาว x สูง)</label>
                    <input type="text" id="product_size" name="product_size" placeholder="W 80 cm. x D 50 cm. x H 180 cm." required>

                    <!-- Product Color -->
                    <label for="product_color">สี</label>
                    <input type="text" id="product_color" name="product_color" placeholder="Solid, Mable, Capuchino, Ashbrown, Mocha" required>

                    <!-- Product Type -->
                    <label for="product_type">ประเภท</label>
                    <select id="product_type" name="product_type">
                        <option value="Wardrobe">ตู้เสื้อผ้า</option>
                        <option value="Bed">เตียง</option>
                        <option value="Cabinet">ตู้เก็บของ</option>
                        <option value="Vanity">โต๊ะเครื่องแป้ง</option>
                    </select>

                    <!-- Product Price -->
                    <label for="product_price">ราคา</label>
                    <input type="number" id="product_price" name="product_price" placeholder="1500.00" placeholder="1500.00" pattern="^\d+(\.\d{1,2})?$" required title="กรุณาระบุตัวเลขทศนิยมสองตำแหน่ง">


                </div>
                </div>

                <div class="line"></div>
                <div class="buttomContainer">

                <script>
                    document.querySelector('form').addEventListener('submit', function(event) {
                        const productPriceInput = document.getElementById('product_price');
                        const priceValue = productPriceInput.value;        
                        // ตรวจสอบให้เป็นตัวเลขทศนิยมสองตำแหน่ง
                        const decimalPattern = /^\d+(\.\d{1,2})?$/;        
                        if (!decimalPattern.test(priceValue)) {
                            alert('กรุณากรอกจำนวนเงินให้เป็นทศนิยมสองตำแหน่ง');
                            productPriceInput.focus();
                            event.preventDefault();
                        }
                    });
                </script>

                <button type="submit" name="submit">เพิ่มสินค้า</button>
                </div>
           
        </form>    

    </div>

</body>
</html>
