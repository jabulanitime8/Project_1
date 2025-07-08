<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// ตรวจสอบการ Login เหมือนกับ index.php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html');
    exit;
}
$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$host = 'localhost';
$db   = 'meeting_room_booking_system'; // <--- ชื่อฐานข้อมูลของคุณ
$user = 'root';      // <--- User ของ MySQL ใน XAMPP
$pass = '';        // <--- Password ของ MySQL ใน XAMPP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $user_id = $_SESSION['user_id'];

    // ดึงข้อมูลการจองของผู้ใช้
    // ใช้ JOIN เพื่อดึง room_name และ officer_name มาแสดงด้วย
    $stmt = $pdo->prepare("
        SELECT 
            b.id AS booking_id, 
            b.title, 
            b.description, 
            b.start_time, 
            b.end_time, 
            b.status,
            r.room_name,
            o.officer_name
        FROM 
            bookings b
        JOIN 
            rooms r ON b.room_id = r.id
        LEFT JOIN 
            officers o ON b.officer_id = o.id
        WHERE 
            b.user_id = ?
        ORDER BY 
            b.start_time DESC
    ");
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll();

    echo json_encode(['success' => true, 'bookings' => $bookings]);

} catch (\PDOException $e) {
    error_log("Database error in api_get_my_bookings.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถดึงข้อมูลการจองได้: ' . $e->getMessage()]);
} catch (\Exception $e) {
    error_log("General error in api_get_my_bookings.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดภายใน: ' . $e->getMessage()]);
}
?>