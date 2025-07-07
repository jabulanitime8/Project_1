<?php
session_start();
// ตรวจสอบการ Login เหมือนกับ index.php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html');
    exit;
}
$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองของฉัน - ระบบจองห้องประชุม</title>
    <!-- Material CDN -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp"
      rel="stylesheet">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="./bookings.css">
</head>
<body>
    <div class="container">

        <?php include 'sidebar.php'; // เรียกใช้ sidebar สำหรับทุกหน้า ?> 
    <!---- END OF ASIDE ---->

        <main>
            <h1>การจองของฉัน</h1>
                <div class="recent-orders" >
                    <h2>รายการการจองของคุณ</h2>
                    <div id="bookingListContainer">
                        <p>กำลังโหลดการจองของคุณ...</p>
                        </div>
                    <div id="bookingMessage" class="message-box"></div>
                    <a href="#">Show All</a>
                </div>
        </main>
    <!-- End of Main -->

        <?php include 'rights.php'; // เรียกใช้ rights สำหรับทุกหน้า ?> 
<!---- END OF Rights ----> 
    </div>

    <div id="messageBox" class="message-box-popup"></div>

    <script src="common.js"></script>
    <script>
        const currentLoggedInUserId = <?php echo json_encode($current_user_id); ?>;
    </script>
    <script src="my_bookings.js"></script>
</body>
</html>