<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "sa_project");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user details and check role
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE User_ID = ? AND Role IN ('Accountant', 'Carrier', 'Manufacturer')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// If user not found or incorrect role, redirect
if ($result->num_rows == 0) {
    header("Location: login.php");
    exit();
}

$user = $result->fetch_assoc();

// Function to get profile image path
function getProfileImage($image_path) {
    if ($image_path && file_exists($image_path)) {
        return $image_path;
    }
    return "uploads/default_profile.png";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ของฉัน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="profileEmployeeStyle.css">
</head>
<body>
    <div class="header">
        <a href="settingEmployee.php" class="back-button">&lt; โปรไฟล์ของฉัน</a>

        <a href="settingEmployee.php" class="settings-button">
            <i class="fas fa-cog setting-icon"></i>
        </a>

        <a href="profileEmployee.php" class="profile-button">
            <i class="fas fa-user profile-icon"></i>
        </a>
        
        <div class="profile-info">
            <img src="<?php echo htmlspecialchars(getProfileImage($user['Profile_image'])); ?>" alt="Profile Picture" class="profile-picture">
            <div class="profile-details">
                <h2><?php echo htmlspecialchars($user['First_name'] . ' ' . $user['Last_name']); ?></h2>
                <p>@<?php echo htmlspecialchars($user['Username']); ?></p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="menu">
            <div class="menu-item">
                <i class="fas fa-receipt menu-item-icon"></i>
                <a href="ordersEmployee.php">
                    <p>รายการคำสั่งซื้อ</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>