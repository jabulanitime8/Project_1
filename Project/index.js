document.addEventListener('DOMContentLoaded', function() {

const sideMenu = document.querySelector("aside");
const menuBtn = document.querySelector("#menu-btn");
const closeBtn = document.querySelector("#close-btn");
const themeToggler = document.querySelector(".theme-toggler");
const logoutBtn = document.getElementById('logoutBtn');

//show sidebar
menuBtn.addEventListener('click', () => {
    sideMenu.style.display= 'block';
})


//close sidebar
closeBtn.addEventListener('click', () => {
    sideMenu.style.display= 'none';
})

//change theme
themeToggler.addEventListener('click', () => {
    document.body.classList.toggle('dark-theme-variables');

    themeToggler.querySelector('span:nth-child(1)').classList.toggle('active');
    themeToggler.querySelector('span:nth-child(2)').classList.toggle('active');
})

//logout Btn
if (logoutBtn) {
        logoutBtn.addEventListener('click', function(event) {
            event.preventDefault(); // ป้องกันการเปลี่ยนหน้าตาม href="#"

            // URL ของไฟล์ PHP ที่จะจัดการการ Logout
            const logoutUrl = 'http://localhost/Gitclone/Project/logout_process.php'; // <<--- ตรวจสอบ Path ของคุณ

            fetch(logoutUrl, {
                method: 'POST', // ใช้ POST เพื่อส่ง Request Logout
                // ไม่ต้องส่ง Body อะไรไป เพราะ Server ไม่ได้ต้องการข้อมูลจาก Client
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.message || 'Server error on logout'); });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('คุณออกจากระบบเรียบร้อยแล้ว.');
                    // Redirect ผู้ใช้กลับไปหน้า Login หลังจาก Logout สำเร็จ
                    window.location.href = 'login.html'; 
                } else {
                    alert('เกิดข้อผิดพลาดในการออกจากระบบ: ' + (data.message || 'ไม่สามารถออกจากระบบได้'));
                }
            })
            .catch(error => {
                console.error('เกิดข้อผิดพลาดในการออกจากระบบ:', error);
                alert('เกิดข้อผิดพลาดในการออกจากระบบ: ' + error.message);
            });
        });
    }

});