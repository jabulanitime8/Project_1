<?php
// เริ่มต้น Session ที่มีอยู่ (สำคัญมาก)
session_start();

// ตั้งค่า Header สำหรับ JSON Response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // สำหรับการพัฒนา ใน Production ควรระบุโดเมนที่แน่นอน
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// ตรวจสอบว่า Request มาแบบ POST (เพื่อความปลอดภัยเบื้องต้น)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. ลบตัวแปร Session ทั้งหมด
    $_SESSION = array();

    // 2. ทำลาย Session cookie (ถ้ามี)
    // การทำลาย Session cookie จะทำให้ Session ID ที่เก็บในเบราว์เซอร์ของผู้ใช้ถูกลบทิ้งไป
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // 3. ทำลาย Session บน Server
    session_destroy();

    // คืนค่า JSON กลับไปที่ Client ว่า Logout สำเร็จ
    echo json_encode(['success' => true, 'message' => 'ออกจากระบบเรียบร้อย.']);
    exit;

} else {
    // ถ้าไม่ได้ส่ง Request แบบ POST
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
?>