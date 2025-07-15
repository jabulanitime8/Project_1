<?php
session_start();
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

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$rooms = [];
$error_message = '';
$success_message = '';
$edit_room = null; // สำหรับเก็บข้อมูลห้องที่กำลังแก้ไข

// --- การจัดการฟอร์ม (Add/Edit) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $room_name = trim($_POST['room_name'] ?? '');
        $capacity = (int)($_POST['capacity'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $description = trim($_POST['location'] ?? '');

        if (empty($room_name) || $capacity <= 0) {
            $error_message = 'กรุณากรอกชื่อห้องและความจุให้ถูกต้อง';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $pdo->prepare("INSERT INTO rooms (room_name, capacity, description, location) VALUES (:room_name, :capacity, :description, :location)");
                    $stmt->execute([
                        ':room_name' => $room_name,
                        ':capacity' => $capacity,
                        ':description' => $description,
                        ':location' => $location
                    ]);
                    $success_message = 'เพิ่มห้องประชุมสำเร็จ!';
                } elseif ($action === 'edit' && isset($_POST['room_id'])) {
                    $room_id = (int)$_POST['room_id'];
                    $stmt = $pdo->prepare("UPDATE rooms SET room_name = :room_name, capacity = :capacity, description = :description, location = :location WHERE id = :room_id");
                    $stmt->execute([
                        ':room_name' => $room_name,
                        ':capacity' => $capacity,
                        ':description' => $description,
                        ':location' => $location,
                        ':room_id' => $room_id
                    ]);
                    $success_message = 'แก้ไขห้องประชุมสำเร็จ!';
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Duplicate entry for UNIQUE constraint
                    $error_message = 'ชื่อห้องประชุมนี้มีอยู่แล้วในระบบ';
                } else {
                    $error_message = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
                }
            }
        }
    } elseif (isset($_POST['delete_id'])) { // การลบ
        $delete_id = (int)$_POST['delete_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = :id");
            $stmt->execute([':id' => $delete_id]);
            $success_message = 'ลบห้องประชุมสำเร็จ!';
        } catch (PDOException $e) {
            $error_message = 'เกิดข้อผิดพลาดในการลบห้องประชุม: ' . $e->getMessage();
        }
    }
}

// --- ดึงข้อมูลห้องสำหรับแสดงในตาราง ---
try {
    $stmt = $pdo->query("SELECT * FROM rooms ORDER BY room_name ASC");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'เกิดข้อผิดพลาดในการดึงข้อมูลห้องประชุม: ' . $e->getMessage();
}

// --- ดึงข้อมูลห้องสำหรับการแก้ไข (ถ้ามีการคลิกปุ่มแก้ไข) ---
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = :id");
        $stmt->execute([':id' => $edit_id]);
        $edit_room = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$edit_room) {
            $error_message = 'ไม่พบห้องประชุมที่ต้องการแก้ไข';
        }
    } catch (PDOException $e) {
        $error_message = 'เกิดข้อผิดพลาดในการดึงข้อมูลห้องประชุมสำหรับการแก้ไข: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการห้องประชุม - Admin</title>
    
    <!-- Material CDN -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp"
      rel="stylesheet">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="./book_room.css">
    <link rel="stylesheet" href="./bookings.css">

</head>
<body>
    <div class="container">
        <?php include 'sidebar.php'; // ตรวจสอบเส้นทางของ sidebar ของคุณ ?>

        <main>
            <h1>จัดการห้องประชุม</h1>

            <?php if ($error_message): ?>
                <div class="message error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="message success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <div class="booking-form-section">
                <h2><?php echo $edit_room ? 'แก้ไขห้องประชุม' : 'เพิ่มห้องประชุมใหม่'; ?></h2>
                <form id="bookingForm" action="admin_manage_rooms.php" method="POST" class="booking-form">
                    <?php if ($edit_room): ?>
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($edit_room['id']); ?>">
                    <?php else: ?>
                        <input type="hidden" name="action" value="add">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="room_name">ชื่อห้องประชุม:</label>
                        <input type="text" id="room_name" name="room_name" value="<?php echo htmlspecialchars($edit_room['room_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="capacity">ความจุ:</label>
                        <input type="number" id="capacity" name="capacity" value="<?php echo htmlspecialchars($edit_room['capacity'] ?? ''); ?>" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="description">รายละเอียด:</label>
                        <input type="text" id="description" name="description"><?php echo htmlspecialchars($edit_room['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="location">สถานที่:</label>
                        <input type="text" id="location" name="location"><?php echo htmlspecialchars($edit_room['location'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn primary"><?php echo $edit_room ? 'บันทึกการแก้ไข' : 'เพิ่มห้องประชุม'; ?></button>
                    <?php if ($edit_room): ?>
                        <a href="admin_manage_rooms.php" class="btn secondary">ยกเลิกการแก้ไข</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="recent-orders">
                <h2>รายการห้องประชุม</h2>
                <?php if (empty($rooms)): ?>
                    <p style="text-align: center;">ยังไม่มีห้องประชุมในระบบ</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ชื่อห้อง</th>
                                <th>ความจุ</th>
                                <th>รายละเอียด</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($room['id']); ?></td>
                                    <td><?php echo htmlspecialchars($room['room_name']); ?></td>
                                    <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                                    <td><?php echo htmlspecialchars($room['description']); ?></td>
                                    <td class="actions">
                                        <a href="admin_manage_rooms.php?edit_id=<?php echo htmlspecialchars($room['id']); ?>" class="btn btn-warning">แก้ไข</a>
                                        <button class="btn btn-danger delete-room-btn" data-id="<?php echo htmlspecialchars($room['id']); ?>">ลบ</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>

     <?php include 'rights.php'; // ตรวจสอบเส้นทางของ sidebar ของคุณ ?>


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roomListTable = document.querySelector('.room-list table tbody');

            if (roomListTable) {
                roomListTable.addEventListener('click', function(event) {
                    const target = event.target;

                    if (target.classList.contains('delete-room-btn')) {
                        const roomId = target.dataset.id;
                        if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบห้องประชุมนี้? การจองที่เกี่ยวข้องอาจได้รับผลกระทบ')) {
                            // สร้างฟอร์มชั่วคราวเพื่อส่งข้อมูล POST
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = 'admin_manage_rooms.php'; // ส่งไปยังหน้าเดียวกัน
                            
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'delete_id';
                            input.value = roomId;
                            form.appendChild(input);

                            document.body.appendChild(form);
                            form.submit(); // ส่งฟอร์ม
                        }
                    }
                });
            }
        });
    </script>
    <script src="./index.js"></script> 
    <script src="./common.js"></script> 
</body>
</html>