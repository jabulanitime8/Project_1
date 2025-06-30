<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // อนุญาตสำหรับพัฒนา! ใน Production ควรระบุโดเมนที่แน่นอน
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // ============ Server-Side Validation ============
    // ตรวจสอบข้อมูลอีกครั้งที่ Server-Side เพื่อความปลอดภัย
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน.']);
        exit;
    }
    if (strlen($username) < 3) {
        echo json_encode(['success' => false, 'message' => 'ชื่อผู้ใช้ต้องมีความยาวอย่างน้อย 3 ตัวอักษร.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'รูปแบบอีเมลไม่ถูกต้อง.']);
        exit;
    }
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร.']);
        exit;
    }

    // ============ การเชื่อมต่อฐานข้อมูล ============
    $host = 'localhost';
    $db   = 'meeting_room_booking_system'; // <--- ชื่อฐานข้อมูลของคุณ
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

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน']);
        exit;
    }

        // ตรวจสอบว่า username หรือ email ซ้ำหรือไม่
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
        $stmt_check->execute(['username' => $username, 'email' => $email]);
        if ($stmt_check->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้ไปแล้ว.']);
            exit;
        }

        // Hash รหัสผ่านก่อนบันทึกลงฐานข้อมูล (สำคัญมาก!)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user'; // <--- กำหนด role เริ่มต้นเป็น 'user'

        // คำสั่ง SQL สำหรับเพิ่มผู้ใช้ใหม่
        $stmt_insert = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (:username, :email, :password_hash)");
        $stmt_insert->execute([
            'username' => $username,
            'email' => $email,
            'password_hash' => $hashed_password,
            'role'=> $role
        ]);

        echo json_encode(['success' => true, 'message' => 'ลงทะเบียนสำเร็จ!']);

    } catch (\PDOException $e) {
        error_log("Database error during registration: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลงทะเบียน (DB Error).']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>