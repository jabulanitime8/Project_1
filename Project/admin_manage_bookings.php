<?php
session_start(); // เริ่มต้นการทำงานของ PHP Session
require_once 'db_config.php'; // ตรวจสอบเส้นทางของไฟล์ db_config.php

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

// ดึงข้อมูลการจองที่อยู่ในสถานะ 'pending'
$pending_bookings = [];
try {
    $stmt = $pdo->prepare("
        SELECT 
            b.id AS booking_id, 
            b.title, 
            b.start_time, 
            b.end_time, 
            b.status,
            u.username AS user_name,
            r.room_name,
            o.officer_name
        FROM 
            bookings b
        JOIN 
            users u ON b.user_id = u.id
        JOIN 
            rooms r ON b.room_id = r.id
        JOIN
            officers o ON b.officer_id = o.id
        WHERE 
            b.status = 'pending'
        ORDER BY 
            b.start_time ASC
    ");
    $stmt->execute();
    $pending_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // หากเกิดข้อผิดพลาดในการดึงข้อมูล
    echo "Error: " . $e->getMessage();
    // ใน Production ควร log error แทนการ echo
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจัดการการจอง (Admin)</title>

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
            <h1>การจองที่รออนุมัติ</h1>

            <div class="recent-orders"> <h2>รายการจอง</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ผู้จอง</th>
                            <th>ห้องประชุม</th>
                            <th>เจ้าหน้าที่</th>
                            <th>หัวข้อ</th>
                            <th>วันที่</th>
                            <th>เวลาเริ่มต้น</th>
                            <th>เวลาสิ้นสุด</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pending_bookings)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center;">ไม่มีการจองที่รออนุมัติ</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pending_bookings as $booking): ?>
                                <tr data-booking-id="<?php echo $booking['booking_id']; ?>">
                                    <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['officer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['title']); ?></td>
                                    <td><?php echo (new DateTime($booking['start_time']))->format('Y-m-d'); ?></td>
                                    <td><?php echo (new DateTime($booking['start_time']))->format('H:i'); ?></td>
                                    <td><?php echo (new DateTime($booking['end_time']))->format('H:i'); ?></td>
                                    <td class="status <?php echo strtolower($booking['status']); ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </td>
                                    <td class="actions">
                                        <button class="btn btn-success approve-btn" data-id="<?php echo $booking['booking_id']; ?>">อนุมัติ</button>
                                        <button class="btn btn-danger reject-btn" data-id="<?php echo $booking['booking_id']; ?>">ปฏิเสธ</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <!-- End of Main -->

        <?php include 'rights.php'; // เรียกใช้ rights สำหรับทุกหน้า ?> 
        <!---- END OF Rights ----> 
    </div>

    <script src="./common.js"></script>
    <script src="./index.js"></script>
    <script src="./update_booking_status.js"></script>
</body>
</html>