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
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="./images/logo-jabulani.png">
                    <h2 class="text-muted">Jabulani</h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>

            <div class="sidebar">
            
                    <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        <span class="material-icons-sharp">grid_view</span> <h3>Dashboard</h3>
                    </a>
                
                    <a href="book_room.php" id="bookRoomLink" class="<?php echo basename($_SERVER['PHP_SELF']) == 'book_room.php' ? 'active' : ''; ?>">
                        <span class="material-icons-sharp">event</span> <h3>จองห้องประชุม</h3>
                    </a>
                
                    <a href="my_bookings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'my_bookings.php' ? 'active' : ''; ?>">
                        <span class="material-icons-sharp">event_available</span> <h3>การจองของฉัน</h3>
                    </a>
                
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    
                        <a href="admin_bookings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_bookings.php' ? 'active' : ''; ?>">
                            <span class="material-icons-sharp">admin_panel_settings</span> <h3>จัดการการจอง (Admin)</h3>
                        </a>
                    
                        <a href="manage_rooms.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_rooms.php' ? 'active' : ''; ?>">
                            <span class="material-icons-sharp">meeting_room</span> <h3>จัดการห้อง (Admin)</h3>
                        </a>
                    
                        <a href="manage_officers.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_officers.php' ? 'active' : ''; ?>">
                            <span class="material-icons-sharp">groups</span> <h3>จัดการเจ้าหน้าที่ (Admin)</h3>
                        </a>
                    
                <?php endif; ?>
                
                    <a href="#" id="logoutBtn">
                        <span class="material-icons-sharp">logout</span> <h3>Logout</h3>
                    </a>
             
        </div>
        </aside>
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
                            <input type="time" id="start_time_input" name="start_time_input" required>
                        </div>

                        <div class="form-group">
                            <label for="end_time_input">เวลาสิ้นสุด:</label>
                            <input type="time" id="end_time_input" name="end_time_input" required>
                        </div>
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
        <div class="right">
            <div class="top">
                <button id="menu-btn">
                    <span class="material-icons-sharp">menu</span>
                </button>
                <div class="theme-toggler">
                    <span class="material-icons-sharp active" >light_mode</span>
                    <span class="material-icons-sharp">dark_mode</span>
                </div>
                <div class="profile">
                    <div class="info">
                        <p>Hey ,<b><?php echo htmlspecialchars($current_username); ?></b></p>
                        <small class="text-muted">Admin</small>
                    </div>
                    <div class="profile-photo">
                        <img src="./images/user_icon1.jpg" alt="">
                    </div>
                </div>
            </div>
<!------- End of Top -------->
            <div class="recent-updates">
                <h2>Recent Updates</h2>
                <div class="updates">
                    <div class="update">
                        <div class="profile-photo">
                            <img src="./images/logo_jabul.jpg" alt="">
                        </div>
                        <div class="message">
                            <p><b>Mike Tyson</b> recived his  order of Night lion tech GPS drone.</p>
                            <small class="text-muted">2 Minutes Ago</small>
                        </div>
                    </div>
                    <div class="update">
                        <div class="profile-photo">
                            <img src="./images/user_icon2.jpg" alt="">
                        </div>
                        <div class="message">
                            <p><b>Mike Tyson</b> recived his  order of Night lion tech GPS drone.</p>
                            <small class="text-muted">6 Minutes Ago</small>
                        </div>
                    </div>
                    <div class="update">
                        <div class="profile-photo">
                            <img src="./images/user_icon5.jpg" alt="">
                        </div>
                        <div class="message">
                            <p><b>Mike Tyson</b> recived his  order of Night lion tech GPS drone.</p>
                            <small class="text-muted">13 Minutes Ago</small>
                        </div>
                    </div>
                </div>
            </div>
<!------------- End of Recent Updates ---------------->
            <div class="sales-analytics">
                <h2>Sales Analytics</h2>
                <div class="item online">
                    <div class="icon">
                        <span class="material-icons-sharp">shopping_cart</span>
                    </div>
                    <div class="right">
                        <div class="info">
                            <h3>ONLINE ORDERS</h3>
                            <small class="text-muted">Last 24 Hours</small>
                        </div>
                        <h5 class="success">+39%</h5>
                        <h3>3849</h3>
                    </div>
                </div>
                <div class="item offline">
                    <div class="icon">
                        <span class="material-icons-sharp">local_mall</span>
                    </div>
                    <div class="right">
                        <div class="info">
                            <h3>OFFLINE ORDERS</h3>
                            <small class="text-muted">Last 24 Hours</small>
                        </div>
                        <h5 class="danger">-17%</h5>
                        <h3>1100</h3>
                    </div>
                </div>
                <div class="item customers">
                    <div class="icon">
                        <span class="material-icons-sharp">person</span>
                    </div>
                    <div class="right">
                        <div class="info">
                            <h3>NEW CUSTOMERS</h3>
                            <small class="text-muted">Last 24 Hours</small>
                        </div>
                        <h5 class="success">+25%</h5>
                        <h3>849</h3>
                    </div>
                </div>
                <div class="item add-product">
                    <div>
                        <span class="material-icons-sharp">add</span>
                        <h3>Add Product</h3>
                    </div>
                </div>
            </div>
        </div>
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