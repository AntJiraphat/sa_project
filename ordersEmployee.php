<?php
session_start();
// นำเข้าไฟล์ database.php เพื่อเชื่อมต่อฐานข้อมูล
require 'database.php';
// ตรวจสอบว่ามี Payment_ID ที่ถูกสร้างใหม่ใน session หรือไม่
if (isset($_SESSION['payment_ID'])) {
    
    // Retrieve the latest payment ID from the session
    $new_payment_ID = $_SESSION['payment_ID'];

    // Assuming you have the order_ID stored in the session or passed as a GET parameter
    $order_ID = isset($_GET['order_ID']) ? $conn->real_escape_string($_GET['order_ID']) : null;

    if ($order_ID) {

        // อัปเดตข้อมูลในฐานข้อมูล
        $sql = "UPDATE orders SET 
        Payment_ID='$new_payment_ID', 
        WHERE Order_ID='$order_ID'";

    if ($conn->query($sql) === TRUE) {
    echo "<script>
            alert('อัปเดตข้อมูลสำเร็จ!');
            window.location.href = 'ordersEmployee.php';
        </script>";
    exit();
    } else {
    echo "<p>เกิดข้อผิดพลาดในการอัปเดต: " . $conn->error . "</p>";
    }
    unset($_SESSION['payment_ID']); // ล้างค่า session เมื่อแสดงผลแล้ว
    }
}
// ตรวจสอบการค้นหา
$search = NULL;
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']); 
    // ปรับคำสั่ง SQL ให้กรองด้วยคำค้นหา
    $sql = "SELECT * FROM orders WHERE Order_ID LIKE '%$search%' OR User_ID LIKE '%$search%' OR Payment_ID LIKE '%$search%'";
} else {
    // ดึงข้อมูลทั้งหมดถ้าไม่มีคำค้นหา
    $sql = "SELECT * FROM orders";
}
$result = $conn->query($sql);
if (!$result) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . $conn->error);
}
function generatePaymentID() {
    global $conn;

    // ดึง Payment_ID ล่าสุดจากตาราง payments
    $sql = "SELECT Payment_ID FROM payments ORDER BY Payment_ID DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastPaymentID = $row['Payment_ID'];
        
        // ตัดเลขลำดับที่ต่อจาก 'INV' ออกมาและเพิ่มค่า
        $newID = intval(substr($lastPaymentID, 3)) + 1;
        
        // สร้าง Payment_ID ใหม่ในรูปแบบ INV0000001
        return 'INV' . str_pad($newID, 7, '0', STR_PAD_LEFT);
    } else {
        // ถ้าไม่มี Payment_ID ในฐานข้อมูล เริ่มต้นจาก INV0000001
        return 'INV0000001';
    }
}

// ตรวจสอบว่ามีการส่งข้อมูลผ่านฟอร์มหรือไม่ (อัปเดตสถานะ)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['orderStatus']) && isset($_POST['orderID'])) {
        foreach ($_POST['orderID'] as $index => $orderID) {
            $orderStatus = $conn->real_escape_string($_POST['orderStatus'][$index]);
            $receiveDate = $conn->real_escape_string($_POST['receiveDate'][$index]);

            // อัปเดตสถานะในฐานข้อมูลตามเงื่อนไขสถานะกำลังผลิต
            if ($orderStatus === 'Currently in production' && !empty($receiveDate)) {
                $sql = "UPDATE orders SET Order_status='$orderStatus', Receive_order_date='$receiveDate' WHERE Order_ID='$orderID'";
            } else {
                $sql = "UPDATE orders SET Order_status='$orderStatus' WHERE Order_ID='$orderID'";
            }

            if (!$conn->query($sql)) {
                echo "<p>เกิดข้อผิดพลาดในการอัปเดต: " . $conn->error . "</p>";
            }
        }
        
        // รีเฟรชหน้าเพื่อแสดงข้อมูลที่อัปเดต
        echo "<script>
                alert('อัปเดตสถานะสำเร็จ!');
                window.location.href = 'ordersEmployee.php';
              </script>";
        exit();
    }
}
// ตรวจสอบว่ามีการส่งไฟล์รูปภาพการชำระเงินผ่านป็อปอัปหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['receipt_image'])) {
    $order_ID = $conn->real_escape_string($_POST['order_ID']);
    $new_payment_ID = generatePaymentID();

    // เก็บรูปภาพในโฟลเดอร์ (เช่น 'uploads/')
    $uploadDir = 'uploadReceipt/';
    $filePath = $uploadDir . basename($_FILES['receipt_image']['name']);
    move_uploaded_file($_FILES['receipt_image']['tmp_name'], $filePath);

    // เพิ่ม Payment_ID ลงในตาราง payments
    $sql = "INSERT INTO payments (Payment_ID, Receipt_image) VALUES ('$new_payment_ID', '$filePath')";
    if ($conn->query($sql) === TRUE) {
        // อัปเดต Payment_ID ในตาราง orders
        $sql = "UPDATE orders SET Payment_ID='$new_payment_ID' WHERE Order_ID='$order_ID'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert('อัปเดต Payment_ID สำเร็จ!');
                    window.location.href = 'ordersEmployee.php';
                  </script>";
        } else {
            echo "<p>เกิดข้อผิดพลาดในการอัปเดต: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>เกิดข้อผิดพลาดในการบันทึก Payment_ID: " . $conn->error . "</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการคำสั่งซื้อ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="orderStyle.css">

    <script>

        function checkOrderStatus(selectElement, orderID) {
            // ถ้าสถานะถูกเปลี่ยนเป็น "กำลังผลิต"
            if (selectElement.value === 'Currently in production') {
                const receiveDate = prompt('กรุณาใส่วันที่ส่งสินค้า (รูปแบบ YYYY-MM-DD):');
                
                if (receiveDate) {
                    // เก็บค่า Receive_order_date และ Order_ID ใน hidden input
                    document.getElementById(`receiveDate_${orderID}`).value = receiveDate;
                } else {
                    // ถ้าผู้ใช้ยกเลิกหรือไม่ได้ใส่วันที่ ให้ตั้งค่าสถานะกลับไปเป็น Pending
                    selectElement.value = 'Pending';
                    alert('กรุณาใส่วันที่ส่งสินค้าเพื่อเปลี่ยนสถานะเป็น "กำลังผลิต"');
                }
            } else {
                // ล้างค่า Receive_order_date ถ้าสถานะไม่ใช่กำลังผลิต
                document.getElementById(`receiveDate_${orderID}`).value = '';
            }
    }

    function openPaymentPopup(orderID) {
            document.getElementById('paymentPopup').style.display = 'block';
            document.getElementById('orderIDInput').value = orderID;
        }

        // ปิดป็อปอัป
        function closePaymentPopup() {
            document.getElementById('paymentPopup').style.display = 'none';
        }

</script>


</head>
<body>

    <div class="header">
        <a href="profileEmployee.php" class="back-button">&lt; รายการคำสั่งซื้อ</a>

        <form action="ordersEmployee.php" method="get" style="position: absolute; top: 20px; left: 180px; color: white; font-size: 1.2rem; text-decoration: none;">
            <input type="text" name="search" placeholder="ค้นหารหัสคำสั่งซื้อ" value="<?php echo htmlspecialchars($search); ?>">
        </form>

        <a href="settingEmployee.php" class="settings-button" style="position: absolute; top: 20px; left: 1300px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-cog setting-icon"></i>
        </a>

        <a href="profileEmployee.php" class="profile-button" style="position: absolute; top: 20px; left: 1350px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-user profile-icon"></i>
        </a>

    </div>

    <div id="paymentPopup" style="display:none;">
    <div class="popup-content">
        <span onclick="closePaymentPopup()" style="cursor:pointer;">&times;</span>
        <h3>อัปโหลดใบเสร็จ</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="order_ID" id="orderIDInput">
            <label for="receipt_image">เลือกไฟล์ใบเสร็จ:</label>
            <input type="file" name="receipt_image" required>
            <button type="submit">อัปโหลดและยืนยัน</button>
        </form>
    </div>
    </div>

    <div class="container">
        <form method="post" action="ordersEmployee.php">
        <table>
            <thead>
                <tr>
                    <th>รหัสคำสั่งซื้อ</th>
                    <th>รหัสลูกค้า</th>
                    <th>รหัสการชำระเงิน</th>
                    <th>วันที่สั่งสินค้า</th>
                    <th>วันที่ส่งสินค้า</th>
                    <th>สถานะคำสั่งซื้อ</th>
                    <th>ยอดรวมสินค้า</th>
                </tr>
            </thead>
            <tbody>
                
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <!-- ทำให้รหัสำสั่งซื้อกลายเป็นลิงก์ -->
                                <a href="detailOrderEmployee.php?order_ID=<?php echo $order['Order_ID']; ?>">
                                    <?php echo $order['Order_ID']; ?>
                                </a>
                            </td>
                            <td><?php echo $order['User_ID']; ?></td>
                            <td>
                                <?php if (empty($order['Payment_ID'])): ?>
                                    <button type="button" onclick="openPaymentPopup('<?php echo $order['Order_ID']; ?>')">
                                        ใบเสร็จยังไม่ถูกสร้าง
                                    </button>
                                <?php else: ?>
                                    <a href="paymentSlip.php?payment_ID=<?php echo $order['Payment_ID']; ?>">
                                        <?php echo $order['Payment_ID']; ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $order['Order_date']; ?></td>
                            <td><?php echo $order['Receive_order_date'] ?? 'รอดำเนินการ'; ?></td>
                            <td>
                                 <!-- Hidden field to pass order ID -->
                                <input type="hidden" name="orderID[]" value="<?php echo $order['Order_ID']; ?>">
                                <input type="hidden" name="receiveDate[]" id="receiveDate_<?php echo $order['Order_ID']; ?>" value="">

                                <select name="orderStatus[]" onchange="checkOrderStatus(this, '<?php echo $order['Order_ID']; ?>')">
                                    <option value="Pending" <?php echo ($order['Order_status'] === 'Pending') ? 'selected' : ''; ?>>รอดำเนินการ</option>
                                    <option value="Currently in production" <?php echo ($order['Order_status'] === 'Currently in production') ? 'selected' : ''; ?>>กำลังผลิต</option>
                                    <option value="Delivery order" <?php echo ($order['Order_status'] === 'Delivery order') ? 'selected' : ''; ?>>จัดส่งสินค้า</option>
                                    <option value="Payment made" <?php echo ($order['Order_status'] === 'Payment made') ? 'selected' : ''; ?>>ชำระเงินแล้ว</option>
                                    <option value="Claim product" <?php echo ($order['Order_status'] === 'Claim product') ? 'selected' : ''; ?>>เคลมสินค้า</option>
                                    <option value="Confirm receipt of product" <?php echo ($order['Order_status'] === 'Confirm receipt of product') ? 'selected' : ''; ?>>ยืนยันการรับสินค้า</option>
                                </select>
                            </td>
                            <td><?php echo number_format($order['Total_price'], 2); ?> บาท</td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">ไม่พบข้อมูลที่ตรงกับการค้นหา</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table> 
        <!-- ปุ่มส่งข้อมูล -->
        <div class="button-container">

            <button type="submit" name="submit" class="confirm-button">ยืนยัน</button>
        </div>
        </form>
    </div>

</body>
</html>