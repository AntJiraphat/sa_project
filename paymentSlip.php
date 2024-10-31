<?php
// นำเข้าไฟล์ database.php เพื่อเชื่อมต่อฐานข้อมูล
require 'database.php';

$payment = null;
$payment_ID = null;
if (isset($_GET['payment_ID'])) {
    $payment_ID = $conn->real_escape_string($_GET['payment_ID']);
    $sql = "SELECT Payment_ID, Receipt_image, Slip_image FROM payments WHERE Payment_ID = '$payment_ID'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $payment = $result->fetch_assoc();
    } else {
        echo "<p>ไม่พบข้อมูลการชำระเงินนี้</p>";
    }
}

// ลบข้อมูลการชำระเงินเมื่อมีการส่งคำขอ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_payment'])) {
    // ลบไฟล์ที่เกี่ยวข้องกับใบเสร็จและสลิป
    if ($payment['Receipt_image'] && file_exists($payment['Receipt_image'])) {
        unlink($payment['Receipt_image']);
    }
    if ($payment['Slip_image'] && file_exists($payment['Slip_image'])) {
        unlink($payment['Slip_image']);
    }

    // อัปเดต Payment_ID ในตาราง orders ให้เป็น NULL
    $sql_update_orders = "UPDATE orders SET Payment_ID = NULL WHERE Payment_ID = '$payment_ID'";
    $conn->query($sql_update_orders);

    // ลบข้อมูลการชำระเงินในฐานข้อมูล
    $sql = "DELETE FROM payments WHERE Payment_ID = '$payment_ID'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('ลบข้อมูลการชำระเงินสำเร็จ'); window.location.href = 'ordersEmployee.php';</script>";
        exit();
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการลบข้อมูลการชำระเงิน');</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $payment_ID) {
    if (isset($_FILES['slip_image']) && $_FILES['slip_image']['error'] == 0) {
        $upload_dir = "uploadSlip/";
        $file_name = basename($_FILES['slip_image']['name']);
        $target_file = $upload_dir . uniqid() . "_" . $file_name;

        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['slip_image']['tmp_name'], $target_file)) {
                $sql = "UPDATE payments SET Slip_image = '$target_file' WHERE Payment_ID = '$payment_ID'";

                if ($conn->query($sql) === TRUE) {
                    echo "<script>
                            alert('อัปโหลดสลิปสำเร็จ');
                            window.location.href = 'paymentSlip.php?payment_ID=" . urlencode($payment_ID) . "';
                        </script>";
                    exit();
                } else {
                    echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตฐานข้อมูล');</script>";
                }
            } else {
                echo "<script>alert('เกิดข้อผิดพลาดในการอัปโหลดไฟล์');</script>";
            }
        } else {
            echo "<script>alert('อนุญาตเฉพาะไฟล์รูปภาพเท่านั้น');</script>";
        }
    } else {
        echo "<script>alert('กรุณาเลือกไฟล์รูปภาพ');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หลักฐานการชำระเงิน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="paymentSlipStyle.css">

    <!-- เพิ่มสคริปต์สำหรับแสดงภาพที่อัปโหลด -->
    <script>
    function previewImage(event, outputId) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById(outputId);
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function deleteImage(type) {
        const imageId = type === 'receipt' ? 'outputReceipt' : 'outputSlip';
        document.getElementById(imageId).src = 'images/camera_icon.png';
        document.getElementById(type === 'receipt' ? 'file-input-receipt' : 'file-input-slip').value = '';
    }
    </script>
</head>
<body>

    <div class="header">
        <a href="ordersEmployee.php" class="back-button">&lt; หลักฐานการชำระเงิน</a>
        <a href="settingEmployee.php" class="settings-button" style="position: absolute; top: 20px; left: 1300px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-cog setting-icon"></i>
        </a>
        <a href="profileEmployee.php" class="profile-button" style="position: absolute; top: 20px; left: 1350px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-user profile-icon"></i>
        </a>
    </div>

    <div class="container">
        <?php if ($payment): ?>
            <p><strong>รหัสการชำระเงิน:</strong> <?php echo $payment['Payment_ID']; ?></p>

            <form action="paymentSlip.php?payment_ID=<?php echo urlencode($payment_ID); ?>" method="POST" enctype="multipart/form-data">
                <div class="containerReceipt">
                    <h2>ใบเสร็จ</h2>
                    <div class="image-upload">
                        <label for="file-input-receipt">
                            <img id="outputReceipt" src="<?php echo $payment['Receipt_image'] ?: 'images/camera_icon.png'; ?>" alt="Upload Image">
                        </label>
                        <input id="file-input-receipt" type="file" name="receipt_image" onchange="previewImage(event, 'outputReceipt')">
                    </div>
                    <div class="buttomContainer">
                        <button type="submit" name="delete_payment" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลการชำระเงินนี้?');">ลบใบเสร็จ</button>
                    </div>
                </div>

                <div class="line"></div>

                <div class="containerSlip">
                    <h2>สลิปการชำระเงิน</h2>
                    <div class="image-upload">
                        <label for="file-input-slip">
                            <img id="outputSlip" src="<?php echo $payment['Slip_image'] ?: 'images/camera_icon.png'; ?>" alt="Upload Image">
                        </label>
                        <input id="file-input-slip" type="file" name="slip_image" onchange="previewImage(event, 'outputSlip')">
                    </div>
                    <button type="button" onclick="deleteImage('slip')" class="delete-button"><i class="fas fa-trash"></i></button>
                    
                    <div class="buttomContainer">
                        <button type="submit" name="submit">ยืนยัน</button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <p>ไม่พบข้อมูลคำสั่งซื้อนี้</p>
        <?php endif; ?>
    </div>

</body>
</html>