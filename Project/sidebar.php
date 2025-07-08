<div class="contianer">
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
                
                    <a href="admin_manage_bookings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_manage_bookings.php' ? 'active' : ''; ?>">
                        <span class="material-icons-sharp">pending_actions</span> <h3>จัดการการจอง (Admin)</h3>
                    </a>
                
                    <a href="admin_manage_rooms.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_manage_rooms.php' ? 'active' : ''; ?>">
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
 <!------- End of Aside -------->
</div>
