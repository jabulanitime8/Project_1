<?php
// กำหนดค่าการเชื่อมต่อฐานข้อมูล
$host = 'localhost'; // หรือ IP Address ของ Database Server ของคุณ
$dbname = 'meeting_room_booking_system'; // *** ชื่อ Database ของคุณ ***
$user = 'root'; // User สำหรับเข้าถึง Database (โดยทั่วไปคือ 'root' สำหรับ XAMPP/WAMP)
$pass = ''; // รหัสผ่านสำหรับ User (โดยทั่วไปคือช่องว่างสำหรับ 'root' ใน XAMPP/WAMP)
$charset = 'utf8mb4'; // Character set สำหรับการเชื่อมต่อ

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // เปิดการแสดง Error แบบ Exception
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // กำหนดให้ Fetch ข้อมูลเป็น Associative Array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // ปิดการ Emulate Prepares (เพื่อความปลอดภัยและประสิทธิภาพ)
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "Database connected successfully!"; // สำหรับทดสอบว่าเชื่อมต่อได้จริง (สามารถลบออกได้เมื่อใช้งานได้แล้ว)
} catch (\PDOException $e) {
    // หากเชื่อมต่อฐานข้อมูลไม่สำเร็จ
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
    // หรือแสดงข้อความ Error (สำหรับ Debugging เท่านั้น ไม่ควรแสดงใน Production)
    // die("Could not connect to the database: " . $e->getMessage()); 
}
?>