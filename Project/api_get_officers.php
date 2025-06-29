<?php
// กำหนด Header สำหรับ JSON Response และอนุญาต CORS (สำหรับการพัฒนา)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // ใน Production ควรระบุโดเมนที่แน่นอน
header('Access-Control-Allow-Methods: GET'); // API นี้รับเฉพาะ GET request
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$host = 'localhost';
$db   = 'meeting_room_booking_system';   // <--- ชื่อฐานข้อมูลของคุณ
$user = 'root';        // <--- User ของ MySQL ใน XAMPP
$pass = '';          // <--- Password ของ MySQL ใน XAMPP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // ดึงข้อมูลเจ้าหน้าที่ทั้งหมดที่ยัง active
    $stmt = $pdo->prepare("SELECT id, officer_name FROM officers WHERE is_active = TRUE ORDER BY officer_name ASC");
    $stmt->execute();
    $officers = $stmt->fetchAll();

    // ส่งข้อมูลเจ้าหน้าที่กลับไปในรูปแบบ JSON
    echo json_encode(['success' => true, 'officers' => $officers]);

} catch (\PDOException $e) {
    // จัดการข้อผิดพลาดในการเชื่อมต่อหรือ Query ฐานข้อมูล
    error_log("Database error in api_get_officers.php: " . $e->getMessage()); // บันทึก error ลง log
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถดึงข้อมูลเจ้าหน้าที่ได้: ' . $e->getMessage()]);
}
?>