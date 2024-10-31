<?php
// เชื่อมต่อฐานข้อมูล
require 'database.php'; 

// ตรวจสอบว่ามีรหัสสินค้าถูกส่งมาหรือไม่
$product = null;
if (isset($_GET['product_ID'])) {
    $product_ID = $conn->real_escape_string($_GET['product_ID']);

    // ดึงข้อมูลสินค้าจากฐานข้อมูลตามรหัสสินค้า
    $sql = "SELECT * FROM products WHERE Product_ID = '$product_ID'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "<p>ไม่พบข้อมูลสินค้านี้</p>";
    }
}

// ตรวจสอบว่ามีการส่งข้อมูลผ่านฟอร์มหรือไม่ (อัปเดตสินค้า)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_ID = $conn->real_escape_string($_POST['product_ID']);
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $product_detail = $conn->real_escape_string($_POST['product_detail']);
    $product_size = $conn->real_escape_string($_POST['product_size']);
    $product_color = $conn->real_escape_string($_POST['product_color']);
    $product_type = $conn->real_escape_string($_POST['product_type']);
    $product_price = $conn->real_escape_string($_POST['product_price']);

    // ดึงข้อมูลรูปภาพเดิมจากฐานข้อมูล
    $sql = "SELECT Product_image FROM products WHERE Product_ID = '$product_ID'";
    $result = $conn->query($sql);
    $current_product = $result->fetch_assoc();
    $product_image = $current_product['Product_image']; // ใช้รูปภาพเดิมเป็นค่าเริ่มต้น

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพใหม่หรือไม่
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploadProduct/";
        $targetFile = $targetDir . basename($_FILES["product_image"]["name"]);
        
        // ตรวจสอบว่าสามารถอัปโหลดไฟล์ได้หรือไม่
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFile)) {
            $product_image = $targetFile; // อัปเดตเป็น path รูปใหม่เมื่ออัปโหลดสำเร็จ
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ');</script>";
            // ยังคงใช้รูปภาพเดิมถ้าอัปโหลดไม่สำเร็จ
        }
    }

        // อัปเดตข้อมูลในฐานข้อมูล
        $sql = "UPDATE products SET 
        Product_name='$product_name', 
        Product_detail='$product_detail', 
        Product_size='$product_size', 
        Product_color='$product_color', 
        Product_type='$product_type', 
        Product_price='$product_price', 
        Product_image='$product_image' 
        WHERE Product_ID='$product_ID'";

    if ($conn->query($sql) === TRUE) {
    echo "<script>
            alert('อัปเดตข้อมูลสำเร็จ!');
            window.location.href = 'allProductsEmployee.php';
        </script>";
    exit();
    } else {
    echo "<p>เกิดข้อผิดพลาดในการอัปเดต: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสินค้า</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="editStyle.css">
    <script>
    function previewImage(event) {
        const preview = document.getElementById('preview-image');
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function() {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>
</head>
<body>

    <div class="header">
        <a href="allProductsEmployee.php" class="back-button">&lt; แก้ไขสินค้า</a>

        <a href="settingEmployee.php" class="settings-button" style="position: absolute; top: 20px; left: 1300px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-cog setting-icon"></i>
        </a>

        <a href="profileEmployee.php" class="profile-button" style="position: absolute; top: 20px; left: 1350px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-user profile-icon"></i>
        </a>

    </div>
    
    <div class="container">
        <!-- ส่งข้อมูลที่แก้ไขไปหน้าที่ส่งให้ฐานข้อมูล -->
        <form action="editProduct.php" method="POST" enctype="multipart/form-data">  
            <div class="form-wrapper">
                <!-- Image Upload -->
                <!-- Left side -->
                <div class="left">
                    <div class="image-upload">
                        <label for="file-input">
                            <img id="preview-image" src="<?php echo $product['Product_image']; ?>" alt="Current Image">
                        </label>
                        <input id="file-input" type="file" name="product_image" accept="image/*" onchange="previewImage(event)">
                    </div>
                </div>

                <!-- Right side -->
                <div class="right">
                    <?php if ($product): ?>
                        <input type="hidden" name="product_ID" value="<?php echo $product['Product_ID']; ?>">

                        <label for="product_name">ชื่อสินค้า</label>
                        <input type="text" id="product_name" name="product_name" value="<?php echo $product['Product_name']; ?>" required>

                        <label for="product_detail">รายละเอียดสินค้า</label>
                        <textarea id="product_detail" name="product_detail" required><?php echo $product['Product_detail']; ?></textarea>

                        <label for="product_size">ขนาดสินค้า</label>
                        <input type="text" id="product_size" name="product_size" value="<?php echo $product['Product_size']; ?>" required>

                        <label for="product_color">สี</label>
                        <input type="text" id="product_color" name="product_color" value="<?php echo $product['Product_color']; ?>" required>

                        <label for="product_type">ประเภท</label>
                        <select id="product_type" name="product_type" required>
                            <option value="Wardrobe" <?php echo ($product['Product_type'] === 'ตู้เสื้อผ้า') ? 'selected' : ''; ?>>ตู้เสื้อผ้า</option>
                            <option value="Bed" <?php echo ($product['Product_type'] === 'เตียง') ? 'selected' : ''; ?>>เตียง</option>
                            <option value="Cabinet" <?php echo ($product['Product_type'] === 'ตู้เก็บของ') ? 'selected' : ''; ?>>ตู้เก็บของ</option>
                            <option value="Vanity" <?php echo ($product['Product_type'] === 'โต๊ะเครื่องแป้ง') ? 'selected' : ''; ?>>โต๊ะเครื่องแป้ง</option>
                        </select>

                        <label for="product_price">ราคา</label>
                        <input type="number" id="product_price" name="product_price" value="<?php echo $product['Product_price']; ?>" required>

                    <?php else: ?>
                        <p>ไม่พบข้อมูลสินค้านี้</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="line"></div>
            <div class="buttomContainer">
            <input type="submit" value="แก้ไขสินค้า">
            </div>

        </form>

    </div>

</body>
</html>

<?php $conn->close(); ?>
