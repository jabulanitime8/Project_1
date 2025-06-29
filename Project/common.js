// common.js

document.addEventListener('DOMContentLoaded', function() {
    // --------------------------------------------------------
    // โค้ดสำหรับ Sidebar
    // --------------------------------------------------------
    const sideMenu = document.querySelector("aside");
    const menuBtn = document.querySelector("#menu-btn");
    const closeBtn = document.querySelector("#close-btn");

    if (menuBtn) {
        menuBtn.addEventListener('click', () => {
            sideMenu.style.display = 'block';
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            sideMenu.style.display = 'none';
        });
    }

    // --------------------------------------------------------
    // โค้ดสำหรับ Theme Toggler
    // --------------------------------------------------------
    const themeToggler = document.querySelector(".theme-toggler");

    if (themeToggler) {
        themeToggler.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme-variables');

            // ตรวจสอบสถานะและบันทึกลง localStorage เพื่อให้ Theme ถูกจดจำ
            if (document.body.classList.contains('dark-theme-variables')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }

            // สลับ active class บนไอคอน
            themeToggler.querySelector('span:nth-child(1)').classList.toggle('active');
            themeToggler.querySelector('span:nth-child(2)').classList.toggle('active');
        });

        // โหลด Theme ที่บันทึกไว้เมื่อหน้าเว็บโหลด
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme-variables');
            themeToggler.querySelector('span:nth-child(1)').classList.remove('active');
            themeToggler.querySelector('span:nth-child(2)').classList.add('active');
        } else {
            // ถ้าเป็น 'light' หรือไม่มีการตั้งค่า (default)
            themeToggler.querySelector('span:nth-child(1)').classList.add('active');
            themeToggler.querySelector('span:nth-child(2)').classList.remove('active');
        }
    }


    // --------------------------------------------------------
    // โค้ดสำหรับ Logout Button
    // --------------------------------------------------------
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(event) {
            event.preventDefault();
            const logoutUrl = 'http://localhost/Gitclone/Project/logout_process.php'; // <<--- ตรวจสอบ Path ของคุณ
            try {
                const response = await fetch(logoutUrl, { method: 'POST' });
                const data = await response.json();
                if (data.success) {
                    alert('คุณออกจากระบบเรียบร้อยแล้ว.');
                    // ลบ theme ที่บันทึกไว้เมื่อ Logout
                    localStorage.removeItem('theme'); 
                    window.location.href = 'login.html';
                } else {
                    alert('เกิดข้อผิดพลาดในการออกจากระบบ: ' + (data.message || 'ไม่สามารถออกจากระบบได้'));
                }
            } catch (error) {
                console.error('Error during logout:', error);
                alert('เกิดข้อผิดพลาดในการออกจากระบบ: ' + error.message);
            }
        });
    }

    // --------------------------------------------------------
    // ฟังก์ชันช่วยแสดงข้อความ (สามารถนำไปใช้ในหน้าอื่นได้)
    // --------------------------------------------------------
    // ต้องมี div ใน HTML ที่มี id เป็น bookingMessage หรืออื่นๆ ที่ต้องการ
    // ตัวอย่าง: <div id="myMessageBox" class="message-box" style="display: none;"></div>
    window.showMessage = function(message, type, targetId = 'bookingMessage') {
        const messageBox = document.getElementById(targetId);
        if (messageBox) {
            messageBox.textContent = message;
            messageBox.className = `message-box ${type}`; // เพิ่ม class 'success' หรือ 'error'
            messageBox.style.display = 'block';
            setTimeout(() => {
                messageBox.style.display = 'none';
            }, 5000); // ซ่อนข้อความหลังจาก 5 วินาที
        } else {
            console.warn(`MessageBox with ID '${targetId}' not found. Displaying alert instead.`);
            alert(message); // fallback หากไม่พบ div
        }
    };
});