<?php
// ตัวอย่างข้อมูลหลักฐานการชำระเงิน
$orders = [
    [
        'orderID' => 'P001',
        'paymentID' => 'PM001'
        
    ],[
        'orderID' => 'P002',
        'paymentID' => 'PM002'
    ],
    // เพิ่มข้อมูล
];

// ตรวจสอบการส่ง orderID เข้ามา
$order = null;
if (isset($_GET['orderID'])) {
    $orderID = $_GET['orderID'];
    // ค้นหาข้อมูล
    foreach ($orders as $o) {
        if ($o['orderID'] === $orderID) {
            $order = $o;
            break;
        }
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
    <link rel="stylesheet" href="paymentEvidenceStyle.css">

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
        const inputId = type === 'receipt' ? 'file-input-receipt' : 'file-input-slip';

        // รีเซ็ต preview รูปภาพให้กลับไปที่ไอคอนกล้อง
        document.getElementById(imageId).src = 'images/camera_icon.png';

        // รีเซ็ตค่าใน input file
        document.getElementById(inputId).value = ''; // ใช้การรีเซ็ตค่า input file
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
        <?php if ($order): ?>
            <p><strong>รหัสคำสั่งซื้อ:</strong> <?php echo $order['orderID']; ?></p>
            <p><strong>รหัสการชำระเงิน:</strong> <?php echo $order['paymentID']; ?></p>

            <div class="containerReceipt">
            <h2>ใบเสร็จ</h2>
            <div class="image-upload">
                <label for="file-input-receipt">
                    <img id="outputReceipt" src="images/camera_icon.png" alt="Upload Image">
                </label>
                <input id="file-input-receipt" type="file" name="product_image" onchange="previewImage(event, 'outputReceipt')">
            </div>
            <button onclick="saveImage('receipt')" class="save-button">ยืนยัน</button>
            <button onclick="deleteImage('receipt')" class="delete-button"><i class="fas fa-trash"></i></button>
            </div>

            <div class="line"></div>

            <div class="containerSlip">
            <h2>สลิปการชำระเงิน</h2>
            <div class="image-upload">
                <label for="file-input-slip">
                    <img id="outputSlip" src="images/camera_icon.png" alt="Upload Image">
                </label>
                <input id="file-input-slip" type="file" name="product_image" onchange="previewImage(event, 'outputSlip')">
            </div>
            <button onclick="saveImage('slip')" class="save-button">ยืนยัน</button>
            <button onclick="deleteImage('slip')" class="delete-button"><i class="fas fa-trash"></i></button>
            </div>
        <?php else: ?>
            <p>ไม่พบข้อมูลคำสั่งซื้อนี้</p>
        <?php endif; ?>
    </div>

</body>
</html>