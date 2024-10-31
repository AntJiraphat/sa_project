<?php
// ตัวอย่างข้อมูลสินค้า
$listEmployee = [
    ['id' => '001', 'username' => '@จิราพัชร1234', 'firstName' => 'จิราพัชร', 'lastName' => 'ฆารไสว', 'dapartment' => 'บัญชี', 'address' => 'สระบุรี แม่น้ำป่าสัก', 'phoneNum' => '092-398-8888', 'email' => 'JiJira@gmail.com'],
    // เพิ่มสินค้าตัวอย่างเพิ่มเติมได้ที่นี่
];

// ตรวจสอบการค้นหา
$search = null;
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    // กรองข้อมูลสินค้าโดยใช้คำค้นหา
    $listEmployee = array_filter($listEmployee, function($listEmployee) use ($search) {
        return stripos($listEmployee['firstname'], $search) !== false ||
               stripos($listEmployee['id'], $search) !== false ||
               stripos($listEmployee['lastName'], $search) !== false;
    });
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

        <form action="employeesSearch.php" method="get" style="position: absolute; top: 28px; left: 1140px; color: white; font-size: 1.2rem; text-decoration: none;">
            <input type="search_box" name="search" placeholder="ค้นหาพนักงาน" value="<?php echo htmlspecialchars($search); ?>">
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
                <?php if (count($listEmployee) > 0): ?>
                    <?php foreach ($listEmployee as $listEmployee): ?>
                        <tr>
                            <td><?php echo $listEmployee['id']; ?></td>
                            <td><?php echo $listEmployee['username']; ?></td>
                            <td><?php echo $listEmployee['firstName']; ?></td>
                            <td><?php echo $listEmployee['lastName']; ?></td>
                            <td><?php echo $listEmployee['dapartment']; ?></td>
                            <td><?php echo $listEmployee['address']; ?></td>
                            <td><?php echo $listEmployee['phoneNum']; ?></td>
                            <td><?php echo $listEmployee['email']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
