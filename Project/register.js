// ตรวจสอบว่า DOM โหลดเสร็จเรียบร้อยแล้ว
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.querySelector('.register-form');

    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const usernameInput = document.getElementById('reg_username');
            const emailInput = document.getElementById('reg_email');
            const passwordInput = document.getElementById('reg_password');
            const confirmPasswordInput = document.getElementById('reg_confirm_password');

            const username = usernameInput.value.trim();
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();
            const confirmPassword = confirmPasswordInput.value.trim();

            // ============ Client-Side Validation สำหรับลงทะเบียน ============
            let isValid = true;
            let errorMessage = '';

            if (username === '' || email === '' || password === '' || confirmPassword === '') {
                isValid = false;
                errorMessage = 'กรุณากรอกข้อมูลให้ครบถ้วน';
            } else if (username.length < 3) {
                isValid = false;
                errorMessage = 'ชื่อผู้ใช้ต้องมีความยาวอย่างน้อย 3 ตัวอักษร';
            } else if (!/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(email)) {
                isValid = false;
                errorMessage = 'รูปแบบอีเมลไม่ถูกต้อง';
            } else if (password.length < 6) {
                isValid = false;
                errorMessage = 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
            } else if (password !== confirmPassword) {
                isValid = false;
                errorMessage = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
            }

            if (!isValid) {
                alert(errorMessage);
                return;
            }

            // ============ ถ้า Validation ผ่าน ให้ส่งข้อมูลไป Server ============
            const registerUrl = 'http://localhost/Project_1/Project/register_process.php'; // URL ของไฟล์/API ลงทะเบียนบน Server

            const formData = new FormData();
            formData.append('username', username);
            formData.append('email', email);
            formData.append('password', password); // ส่งรหัสผ่านไปเพื่อทำการ Hash ที่ Server-Side

            fetch(registerUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.message || 'Server error'); });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('ลงทะเบียนสำเร็จ! กรุณาเข้าสู่ระบบด้วยชื่อผู้ใช้และรหัสผ่านที่คุณตั้งไว้');
                    window.location.href = 'login.html'; // Redirect ไปหน้า Login
                } else {
                    alert('ลงทะเบียนไม่สำเร็จ: ' + (data.message || 'เกิดข้อผิดพลาดในการลงทะเบียน'));
                }
            })
            .catch(error => {
                console.error('เกิดข้อผิดพลาดในการลงทะเบียน:', error);
                alert('เกิดข้อผิดพลาดในการลงทะเบียน: ' + error.message);
            });
        });
    }
});