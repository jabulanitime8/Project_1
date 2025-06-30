<?php
session_start(); // เริ่มต้นการทำงานของ PHP Session

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

// คุณสามารถใช้ตัวแปรเหล่านี้ในโค้ด HTML ของคุณได้ เช่น แสดงชื่อผู้ใช้
?>

<!DOCTYPE html>
<html lang="en">
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
            <h1>Dashboard</h1>

            <div class="insights">

                <div class="room-list-section">
                    <h2>ห้องประชุมที่มีให้บริการ</h2>
                    <div class="room-card-container" id="roomListContainer">
                        <p>กำลังโหลดข้อมูลห้องประชุม...</p>
                    </div>
                </div>
                
            <!-- End of Insights -->

             <div class="recent-orders">
                    <h2>Recent Orders</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Product Number</th>
                                <th>Payment</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Foldable Mini Drone</td>
                                <td>85631</td>
                                <td>Due</td>
                                <td class="warning">Pending</td>
                                <td class="primary">Detail</td>
                            </tr>
                            <tr>
                                <td>Foldable Mini Drone</td>
                                <td>85631</td>
                                <td>Due</td>
                                <td class="warning">Pending</td>
                                <td class="primary">Detail</td>
                            </tr>
                            <tr>
                                <td>Foldable Mini Drone</td>
                                <td>85631</td>
                                <td>Due</td>
                                <td class="warning">Pending</td>
                                <td class="primary">Detail</td>
                            </tr>
                            <tr>
                                <td>Foldable Mini Drone</td>
                                <td>85631</td>
                                <td>Due</td>
                                <td class="warning">Pending</td>
                                <td class="primary">Detail</td>
                            </tr>
                        </tbody>
                    </table>
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
                            <small class="text-muted">Last 24 Hours.</small>
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
                            <small class="text-muted">Last 24 Hours.</small>
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
                            <small class="text-muted">Last 24 Hours.</small>
                        </div>
                        <h5 class="success">+25%</h5>
                        <h3>849</h3>
                    </div>
                </div>
                <div class="item add-product">
                    <span class="material-icons-sharp">add</span>
                    <h3>Add Product</h3>
                </div>
            </div>
        </div>
    </div>

    <script src="./common.js"></script><script src="./index.js"></script></script>
    
</body>
</html>