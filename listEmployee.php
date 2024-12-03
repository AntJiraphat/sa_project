<?php
// เรียกใช้การเชื่อมต่อฐานข้อมูล
require 'database.php';
// ตรวจสอบการค้นหา
$search = '';
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']); 
    // ปรับคำสั่ง SQL ให้กรองด้วยคำค้นหา
    $sql = "SELECT * FROM USERS WHERE Username LIKE '%$search%' OR User_ID LIKE '%$search%' OR First_name LIKE '%$search%' OR Last_name LIKE '%$search%' AND Role IN ('Accountant', 'Carrier', 'Manufacturer')";
} else {
    // ดึงข้อมูลทั้งหมดถ้าไม่มีคำค้นหาและเฉพาะ Role ที่กำหนด
    $sql = "SELECT * FROM USERS WHERE Role IN ('Accountant', 'Carrier', 'Manufacturer')";
}

$result = $conn->query($sql);
if (!$result) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายชื่อพนักงาน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="listEmployeeStyle.css">

</head>
<body>

    <div class="header">
        <a href="profileAdmin.php" class="back-button">&lt; รายชื่อพนักงาน</a>

        <form action="employeesSearch.php" method="get" style="position: absolute; top: 20px; left: 180px; color: white; font-size: 1.2rem; text-decoration: none;">
            <input type="text" name="search" placeholder="ค้นหาพนักงาน" value="<?php echo htmlspecialchars($search); ?>">
        </form>

        <a href="settingEmployee.php" class="settings-button" style="position: absolute; top: 20px; left: 1300px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-cog setting-icon"></i>
        </a>

        <a href="profileEmployee.php" class="profile-button" style="position: absolute; top: 20px; left: 1350px; color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fas fa-user profile-icon"></i>
        </a>

    </div>


    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>IDพนักงาน</th>
                    <th>ชื่อผู้ใช้</th>
                    <th>ชื่อ</th>
                    <th>นามสกุล</th>
                    <th>แผนก</th>
                    <th>ที่อยู่</th>
                    <th>โทรศัพท์</th>
                    <th>อีเมล</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($listEmployee = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $listEmployee['User_ID']; ?></td>
                            <td><?php echo $listEmployee['Username']; ?></td>
                            <td><?php echo $listEmployee['First_name']; ?></td>
                            <td><?php echo $listEmployee['Last_name']; ?></td>
                            <td><?php echo $listEmployee['Role']; ?></td>
                            <td><?php echo $listEmployee['Address']; ?></td>
                            <td><?php echo $listEmployee['PhoneNum']; ?></td>
                            <td><?php echo $listEmployee['Email']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                        <td colspan="8">ไม่พบชื่อพนักงาน</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>