<?php
session_start(); // เริ่มต้นการทำงานของ PHP Session

// ตรวจสอบว่าผู้ใช้ได้เข้าสู่ระบบแล้วหรือไม่
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // ถ้าผู้ใช้ยังไม่ได้เข้าสู่ระบบ
    // ให้ redirect (ส่งกลับ) ไปยังหน้า login.html
    header('Location: login.html');
    exit; // หยุดการทำงานของ script เพื่อให้แน่ใจว่าไม่มีโค้ดอื่นถูกรัน
}

// ถ้ามาถึงตรงนี้ได้ แสดงว่าผู้ใช้ได้เข้าสู่ระบบแล้ว
// คุณสามารถเข้าถึงข้อมูลผู้ใช้ที่เก็บไว้ใน Session ได้ เช่น:
$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];

// คุณสามารถใช้ตัวแปรเหล่านี้ในโค้ด HTML ของคุณได้ เช่น แสดงชื่อผู้ใช้
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจองห้องประชุม หน่วยบัญชาการทหารพัฒนา</title>

    <!-- Material CDN -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp"
      rel="stylesheet">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="./rooms.css">
    <link rel="stylesheet" href="./book_room.css">
    
</head>
<body>
    <div class="container">

     <?php include 'sidebar.php'; // เรียกใช้ sidebar สำหรับทุกหน้า ?> 
 <!---- END OF ASIDE ---->
        <main>
            <h1>Dashboard</h1>

            <div class="booking-form-section">

                <div class="booking-form">
                    <h2>ห้องประชุมที่มีให้บริการ</h2>
                    <div class="room-card-container" id="roomListContainer">
                        <p>กำลังโหลดข้อมูลห้องประชุม...</p>
                    </div>
                </div>
                
        </main>
<!-- End of Main -->

        <?php include 'rights.php'; // เรียกใช้ rights สำหรับทุกหน้า ?> 
<!---- END OF Rights ---->    
    </div>

    <script src="./common.js"></script><script src="./index.js"></script></script>
    
</body>
</html>