// ตรวจสอบว่า DOM โหลดเสร็จเรียบร้อยแล้ว
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');

    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            // ป้องกันการ submit ฟอร์มโดยตรง (เราจะจัดการการส่งข้อมูลเอง)
            event.preventDefault();

            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');

            const username = usernameInput.value.trim(); // .trim() ใช้ลบช่องว่างหน้าหลัง
            const password = passwordInput.value.trim();

            // ============ Basic Client-Side Validation ============
            let isValid = true;
            let errorMessage = '';

            if (username === '' || password === '') {
                isValid = false;
                errorMessage = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่านให้ครบถ้วน';
            }
            // สามารถเพิ่มการตรวจสอบอื่นๆ ได้ เช่น:
            // if (username.length < 3) {
            //     isValid = false;
            //     errorMessage = 'ชื่อผู้ใช้ต้องมีความยาวอย่างน้อย 3 ตัวอักษร';
            // }
            // if (password.length < 6) {
            //     isValid = false;
            //     errorMessage = 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
            // }

            if (!isValid) {
                // แสดงข้อความ Error ให้ผู้ใช้เห็น
                alert(errorMessage); // ใช้ alert ง่ายๆ ไปก่อน หรือจะแสดงใน DOM ก็ได้
                return; // หยุดการทำงานของฟังก์ชัน
            }

            // ============ ถ้า Validation ผ่าน ให้ส่งข้อมูลไป Server ============
            // ส่วนนี้จะส่งข้อมูลไปยัง Server โดยใช้ Fetch API
            // คุณต้องกำหนด URL ของไฟล์/API ที่จะประมวลผลการ Login บน Server ของคุณ
            const loginUrl = 'login.html'; // หรือ .js, .py, .java, etc.

            // สร้าง FormData object เพื่อส่งข้อมูลฟอร์ม
            const formData = new FormData();
            formData.append('username', username);
            formData.append('password', password);

            // ส่งข้อมูลด้วย Fetch API (เป็น Asynchronous Operation)
            fetch(loginUrl, {
                method: 'POST', // ใช้ POST สำหรับการส่งข้อมูล Login
                body: formData // ส่ง FormData object
            })
            .then(response => {
                // ตรวจสอบสถานะ HTTP response
                if (!response.ok) {
                    // ถ้า Server ตอบกลับมาด้วย Error (เช่น 400, 401, 500)
                    // ให้โยน Error เพื่อให้ถูกจับที่ .catch()
                    return response.json().then(err => { throw new Error(err.message || 'Server error'); });
                }
                return response.json(); // แปลง response เป็น JSON
            })
            .then(data => {
                // ตรวจสอบผลลัพธ์จาก Server
                if (data.success) {
                    alert('เข้าสู่ระบบสำเร็จ!');
                    // Redirect ไปยังหน้า Dashboard หรือหน้าหลัก
                    window.location.href = 'index.html'; // หรือ dashboard.html
                } else {
                    alert('เข้าสู่ระบบไม่สำเร็จ: ' + (data.message || 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'));
                }
            })
            .catch(error => {
                // จัดการ Error ที่เกิดขึ้นระหว่างการ Fetch หรือจาก Server
                console.error('เกิดข้อผิดพลาดในการเข้าสู่ระบบ:', error);
                alert('เกิดข้อผิดพลาดในการเข้าสู่ระบบ: ' + error.message);
            });
        });
    }
});