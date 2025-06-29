<?php
// กำหนด Header สำหรับ JSON Response และอนุญาต CORS (สำหรับการพัฒนา)
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

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

    // รับค่า room_id และ date จาก Query String
    $room_id = filter_input(INPUT_GET, 'room_id', FILTER_VALIDATE_INT);
    $date = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING);

    if (!$room_id || !$date) {
        echo json_encode(['success' => false, 'message' => 'Missing room_id or date parameter.']);
        exit;
    }

    // ตรวจสอบความถูกต้องของวันที่
    $dateTime = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dateTime || $dateTime->format('Y-m-d') !== $date) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format. Expected YYYY-MM-DD.']);
        exit;
    }

    // กำหนดช่วงเวลาที่เปิดให้จอง (เช่น 08:00 ถึง 18:00)
    $opening_time = '08:00:00';
    $closing_time = '18:00:00'; // ปิดทำการตอน 18:00 น.

    // กำหนดช่วงเวลาขั้นต่ำในการจอง (เช่น 30 นาที)
    $interval_minutes = 30;

    $all_possible_slots = [];
    $current_time_obj = DateTime::createFromFormat('H:i:s', $opening_time);
    $closing_time_obj = DateTime::createFromFormat('H:i:s', $closing_time);

    while ($current_time_obj < $closing_time_obj) {
        $slot_start = $current_time_obj->format('H:i:s');
        $current_time_obj->modify("+$interval_minutes minutes");
        $slot_end = $current_time_obj->format('H:i:s');

        // ต้องแน่ใจว่า slot_end ไม่เกิน closing_time_obj
        if ($current_time_obj > $closing_time_obj) {
             // ถ้าส่วนท้ายของ slot เกินเวลาปิดทำการ ให้ปรับ slot_end เป็นเวลาปิดทำการ
            $slot_end = $closing_time_obj->format('H:i:s');
        }
        
        // ถ้า slot_start เท่ากับ slot_end (กรณี 17:30-18:00 ถ้า interval 30) ก็ไม่ควรเพิ่ม
        if ($slot_start !== $slot_end) {
            $all_possible_slots[] = [
                'start_time' => $slot_start,
                'end_time'   => $slot_end
            ];
        }
    }

    // ดึงช่วงเวลาที่ถูกจองแล้วสำหรับห้องและวันที่ที่เลือก
    $stmt = $pdo->prepare("SELECT start_time, end_time FROM bookings WHERE room_id = ? AND DATE(start_time) = ? AND status IN ('pending', 'approved')");
    $stmt->execute([$room_id, $date]);
    $booked_slots_db = $stmt->fetchAll();

    // แปลงรูปแบบเวลาที่ถูกจองให้เป็น HH:MM:SS เพื่อเปรียบเทียบ
    $formatted_booked_slots = [];
    foreach ($booked_slots_db as $booked_slot) {
        $booked_start = (new DateTime($booked_slot['start_time']))->format('H:i:s');
        $booked_end = (new DateTime($booked_slot['end_time']))->format('H:i:s');
        $formatted_booked_slots[] = [
            'start_time' => $booked_start,
            'end_time'   => $booked_end
        ];
    }
    
    // ส่งข้อมูลกลับไป
    echo json_encode([
        'success' => true,
        'available_slots' => $all_possible_slots,
        'booked_slots' => $formatted_booked_slots // ส่งช่วงเวลาที่ถูกจองแล้วไปด้วย
    ]);

} catch (\PDOException $e) {
    error_log("Database error in api_get_available_slots.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถดึงช่วงเวลาที่ว่างได้: ' . $e->getMessage()]);
} catch (\Exception $e) {
    error_log("General error in api_get_available_slots.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดภายใน: ' . $e->getMessage()]);
}
?>