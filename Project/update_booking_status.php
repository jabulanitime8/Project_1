<?php
// update_booking_status.php
session_start();
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON

// เปิดการแสดง error เพื่อการ debug (ควรปิดในการใช้งานจริง)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_config.php'; // ตรวจสอบเส้นทางของไฟล์ db_config.php

$response = ['success' => false, 'message' => ''];

// ตรวจสอบว่าผู้ใช้เป็น Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $response['message'] = 'คุณไม่มีสิทธิ์ในการดำเนินการนี้';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่า booking_id และ status จาก AJAX request
    $booking_id = $_POST['booking_id'] ?? null;
    $new_status = $_POST['status'] ?? null; // 'approved' หรือ 'rejected'

    // ตรวจสอบค่าที่ได้รับ
    if (empty($booking_id) || empty($new_status)) {
        $response['message'] = 'ข้อมูลไม่ครบถ้วนสำหรับการอัปเดตสถานะ';
        echo json_encode($response);
        exit();
    }

    // ตรวจสอบว่า status ที่ส่งมาถูกต้องหรือไม่
    if (!in_array($new_status, ['approved', 'rejected', 'cancelled'])) { // เพิ่ม 'cancelled' เผื่อไว้ในอนาคต
        $response['message'] = 'สถานะที่ส่งมาไม่ถูกต้อง';
        echo json_encode($response);
        exit();
    }

    try {
        // เตรียมคำสั่ง SQL สำหรับอัปเดตสถานะ
        $stmt = $pdo->prepare("UPDATE bookings SET status = :new_status WHERE id = :booking_id");
        $stmt->execute([
            ':new_status' => $new_status,
            ':booking_id' => $booking_id
        ]);

        // ตรวจสอบว่ามีแถวที่ถูกอัปเดตหรือไม่
        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'อัปเดตสถานะการจองเรียบร้อยแล้ว';
            $response['booking_id'] = $booking_id; // ส่ง booking_id กลับไปด้วย เพื่อให้ JS อัปเดต UI ได้ง่าย
            $response['new_status'] = $new_status; // ส่ง status ใหม่กลับไปด้วย
        } else {
            $response['message'] = 'ไม่พบรายการจอง หรือสถานะไม่ได้เปลี่ยนแปลง';
        }

    } catch (PDOException $e) {
        $response['message'] = 'เกิดข้อผิดพลาดในการอัปเดตสถานะ: ' . $e->getMessage();
        // ใน Production ควร log error แทนการ echo
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit();
?>