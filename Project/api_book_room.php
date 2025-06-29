<?php
session_start(); // ต้องเริ่ม session เพื่อเข้าถึง user_id
// กำหนด Header สำหรับ JSON Response และอนุญาต CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS'); // API นี้รับ POST และ OPTIONS (สำหรับ CORS Preflight)
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// จัดการ OPTIONS request สำหรับ CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ตรวจสอบว่าผู้ใช้ Login อยู่หรือไม่
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: User not logged in.']);
    exit;
}

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$host = 'localhost';
$db   = 'meeting_room_booking_system';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // รับข้อมูลจาก POST request
    $room_id     = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
    $user_id     = $_SESSION['user_id']; // ดึง user_id จาก Session
    $officer_id  = filter_input(INPUT_POST, 'officer_id', FILTER_VALIDATE_INT); // อาจจะเป็น NULL
    $title       = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $start_time  = filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_STRING);
    $end_time    = filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_STRING);

    // ตรวจสอบข้อมูลที่จำเป็น
    if (!$room_id || !$user_id || !$title || !$start_time || !$end_time) {
        echo json_encode(['success' => false, 'message' => 'Missing required booking information.']);
        exit;
    }

    // ตรวจสอบความถูกต้องของรูปแบบเวลา
    // DATETIME ใน MySQL ควรเป็น YYYY-MM-DD HH:MM:SS
    $start_datetime_obj = DateTime::createFromFormat('Y-m-d H:i:s', $start_time);
    $end_datetime_obj = DateTime::createFromFormat('Y-m-d H:i:s', $end_time);

    if (!$start_datetime_obj || $start_datetime_obj->format('Y-m-d H:i:s') !== $start_time ||
        !$end_datetime_obj || $end_datetime_obj->format('Y-m-d H:i:s') !== $end_time) {
        echo json_encode(['success' => false, 'message' => 'Invalid date/time format. Expected YYYY-MM-DD HH:MM:SS.']);
        exit;
    }

    // ตรวจสอบว่าเวลาสิ้นสุดต้องมากกว่าเวลาเริ่มต้น
    if ($start_datetime_obj >= $end_datetime_obj) {
        echo json_encode(['success' => false, 'message' => 'เวลาสิ้นสุดต้องมากกว่าเวลาเริ่มต้น.']);
        exit;
    }

    // ตรวจสอบว่าไม่มีการจองซ้อนทับกัน
    // เงื่อนไขการซ้อนทับ: (StartA < EndB) AND (EndA > StartB)
    $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM bookings WHERE room_id = ? AND status IN ('pending', 'approved') AND (
        (start_time < ? AND end_time > ?) OR
        (start_time >= ? AND start_time < ?) OR
        (end_time > ? AND end_time <= ?)
    )");

    $stmt->execute([
        $room_id,
        $end_time,
        $start_time,
        $start_time, // เพื่อตรวจการซ้อนทับที่จุดเริ่มต้น
        $end_time,   // เพื่อตรวจการซ้อนทับที่จุดเริ่มต้น
        $start_time, // เพื่อตรวจการซ้อนทับที่จุดสิ้นสุด
        $end_time    // เพื่อตรวจการซ้อนทับที่จุดสิ้นสุด
    ]);
    $overlap_count = $stmt->fetchColumn();

    if ($overlap_count > 0) {
        echo json_encode(['success' => false, 'message' => 'ช่วงเวลาที่เลือกไม่ว่าง มีการจองซ้อนทับกัน.']);
        exit;
    }

    // เตรียมคำสั่ง SQL สำหรับ INSERT
    $sql = "INSERT INTO bookings (room_id, user_id, officer_id, title, description, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
    $params = [
        $room_id,
        $user_id,
        $officer_id === 0 ? null : $officer_id, // ถ้า officer_id เป็น 0 (จาก "ไม่ระบุ") ให้ใส่เป็น NULL
        $title,
        $description,
        $start_time,
        $end_time
    ];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'การจองห้องประชุมสำเร็จ! กำลังรอการอนุมัติ.']);

} catch (\PDOException $e) {
    error_log("Database error in api_book_room.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกการจอง: ' . $e->getMessage()]);
} catch (\Exception $e) {
    error_log("General error in api_book_room.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดภายใน: ' . $e->getMessage()]);
}
?>