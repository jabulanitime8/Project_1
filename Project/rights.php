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
                <small class="text-muted">
                    <?php 
                        if (isset($_SESSION['role'])) {
                            echo ucfirst($_SESSION['role']); // ทำให้ตัวอักษรแรกเป็นตัวพิมพ์ใหญ่ (Admin, User)
                        } else {
                            echo 'Guest'; // หรือข้อความเริ่มต้นอื่นๆ หากไม่มี role ใน session
                        }
                        ?>
                </small>
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