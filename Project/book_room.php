<?php
session_start();
// ตรวจสอบการ Login เหมือนกับ index.php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html');
    exit;
}
$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจองห้องประชุม หน่วยบัญชาการทหารพัฒนา</title>

    <!-- Material CDN -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp"
      rel="stylesheet">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="./rooms.css">
    <link rel="stylesheet" href="./book_room.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

</head>
<body>
    <div class="container">

        <?php include 'sidebar.php'; // เรียกใช้ sidebar สำหรับทุกหน้า ?> 
<!---- END OF ASIDE ---->
        <main>
            <h1>จองห้องประชุม</h1>

            <div class="booking-form-section">
                <h2>ฟอร์มการจองห้องประชุม</h2>
                <form id="bookingForm" class="booking-form">
                    <div class="form-group">
                        <label for="room_id">เลือกห้องประชุม:</label>
                        <select id="room_id" name="room_id" required>
                            <option value="">-- เลือกห้องประชุม --</option>
                            </select>
                    </div>

                    <div class="form-group">
                        <label for="booking_date">เลือกวันที่:</label>
                        <input type="text" id="booking_date" name="booking_date" placeholder="คลิกเพื่อเลือกวันที่" required>
                    </div>

                    <div class="form-group">
                        <label>เลือกช่วงเวลา:</label>
                        <div class="form-group">
                            <label for="start_time_input">เวลาเริ่มต้น:</label>
                            <input type="time" id="start_time_input" name="start_time" required step="1800">
                        </div>

                        <div class="form-group">
                            <label for="end_time_input">เวลาสิ้นสุด:</label>
                            <input type="time" id="end_time_input" name="end_time" required step="1800">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>เลือกช่วงเวลา:</label>
                        <div class="time-slots-container" id="timeSlotsContainer">
                            <p>โปรดเลือกห้องและวันที่ก่อน</p>
                        </div>
                        <input type="hidden" id="start_time" name="start_time">
                        <input type="hidden" id="end_time" name="end_time">
                    </div>

                    <div class="form-group">
                        <label for="title">หัวข้อการประชุม:</label>
                        <input type="text" id="title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="description">รายละเอียดการประชุม (ถ้ามี):</label>
                        <textarea id="description" name="description"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="officer_id">เจ้าหน้าที่ผู้ดูแล (เลือกได้):</label>
                        <select id="officer_id" name="officer_id">
                            <option value="">-- ไม่ระบุเจ้าหน้าที่ --</option>
                            </select>
                    </div>

                    <div id="bookingMessage" class="message-box" style="display: none;"></div>

                    <button type="submit">ยืนยันการจอง</button>
                </form>
            </div>
        </main>
<!-- End of Main -->
 
        <?php include 'rights.php'; // เรียกใช้ rights สำหรับทุกหน้า ?> 
<!---- END OF Rights ---->    
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script> 
    
    <script src="common.js"></script> 

    <script>
        // ประกาศตัวแปร global JavaScript ที่จะใช้เก็บ user_id
        // ต้องแน่ใจว่า $current_user_id มีค่าอยู่จริงจาก session_start()
        const currentLoggedInUserId = <?php echo json_encode($current_user_id); ?>;
    </script>
    
    <script src="book_room.js"></script>
</html>