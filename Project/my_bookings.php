<?php
session_start();

// ตรวจสอบว่าผู้ใช้ Login อยู่หรือไม่
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // ถ้ายังไม่ Login ให้ redirect ไปหน้า Login
    exit;
}

// ดึง user_id จาก session
$current_user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองของฉัน - ระบบจองห้องประชุม</title>
    <!-- Material CDN -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp"
      rel="stylesheet">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="./bookings.css">
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
            <h1>การจองของฉัน</h1>

                <div class="recent-orders" >
                    <h2>รายการการจองของคุณ</h2>
                    <div id="bookingListContainer">
                        <p>กำลังโหลดการจองของคุณ...</p>
                        </div>
                    <div id="bookingMessage" class="message-box"></div>
                    <a href="#">Show All</a>
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
                        <p>Hey ,<b><?php echo htmlspecialchars($_SESSION['username']); ?></b></p>
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

    <div id="messageBox" class="message-box-popup"></div>

    <script src="common.js"></script>
    <script>
        const currentLoggedInUserId = <?php echo json_encode($current_user_id); ?>;
    </script>
    <script src="my_bookings.js"></script>
</body>
</html>