<?php
session_start();
session_destroy(); // ลบ session ทั้งหมด
header("Location: login.php"); // redirect ไปหน้า login
exit();
?>