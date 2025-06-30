<?php
// ตั้งค่าส่วนหัวของ HTTP Response เพื่อระบุว่าเป็น JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // อนุญาตให้ทุกโดเมนเรียก API นี้ได้ (สำหรับการพัฒนาเท่านั้น! ใน Production ควรระบุโดเมนที่แน่นอน)
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// เริ่ม Session (สำคัญมากสำหรับการจัดการสถานะการ Login)
session_start();

// รับข้อมูลที่ส่งมาจาก Form (ผ่าน Fetch API)
// ตรวจสอบว่ามีการส่งข้อมูลแบบ POST มาหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? ''; // ใช้ ?? เพื่อให้ค่าเป็นสตริงว่างถ้าไม่มี
    $password = $_POST['password'] ?? '';

    // ============ ตรวจสอบข้อมูลผู้ใช้กับฐานข้อมูล ============
    // นี่คือส่วนที่คุณต้องเชื่อมต่อกับฐานข้อมูลของคุณ
    // ตัวอย่างการเชื่อมต่อ MySQL ด้วย PDO
    $host = 'localhost';
    $db   = 'meeting_room_booking_system'; // ชื่อฐานข้อมูลของคุณ
    $user = 'root';   // ชื่อผู้ใช้ฐานข้อมูลของคุณ
    $pass = '';   // รหัสผ่านฐานข้อมูลของคุณ
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // เตรียมคำสั่ง SQL เพื่อดึงข้อมูล user รวมถึง role
        // สำคัญ: ควรเก็บรหัสผ่านในฐานข้อมูลแบบ Hashed (เช่น password_hash() ใน PHP)
        // และตรวจสอบด้วย password_verify()
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            // ตรวจสอบรหัสผ่านที่ Hashed (สำคัญมาก: ห้ามเก็บรหัสผ่านแบบ Plain Text)
            if (password_verify($password, $user['password_hash'])) {
                // รหัสผ่านถูกต้อง
                // ============ สร้าง Session ============
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role']; // <--- เพิ่มบรรทัดนี้เพื่อเก็บ role ใน Session
                
                // คืนค่า JSON กลับไปที่ Client
                echo json_encode(['success' => true, 'message' => 'Login successful!']);
            } else {
                // รหัสผ่านไม่ถูกต้อง
                echo json_encode(['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
            }
        } else {
            // ไม่พบชื่อผู้ใช้
            echo json_encode(['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
        }

    } catch (\PDOException $e) {
        // จัดการข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล
        error_log("Database error: " . $e->getMessage()); // บันทึก error ลง log
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์ (DB Error)']);
    }

} else {
    // ถ้าไม่ได้ส่งข้อมูลแบบ POST มา
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>