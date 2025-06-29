document.addEventListener('DOMContentLoaded', function() {

const sideMenu = document.querySelector("aside");
const menuBtn = document.querySelector("#menu-btn");
const closeBtn = document.querySelector("#close-btn");
const themeToggler = document.querySelector(".theme-toggler");
const logoutBtn = document.getElementById('logoutBtn');
// ------------- ส่วนสำหรับดึงและแสดงข้อมูลห้องประชุม -------------
const roomListContainer = document.getElementById('roomListContainer');
const apiRoomsUrl = 'http://localhost/Gitclone/Project/api_get_rooms.php'; // <<--- ตรวจสอบ Path ของคุณ

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

// ------------- ส่วนสำหรับดึงและแสดงข้อมูลห้องประชุม -------------
async function fetchRooms() {
    if (!roomListContainer) return; // ถ้าไม่มี container ก็ไม่ต้องทำอะไร

    try {
        roomListContainer.innerHTML = '<p>กำลังโหลดข้อมูลห้องประชุม...</p>'; // แสดงสถานะโหลด
        const response = await fetch(apiRoomsUrl);
        
        if (!response.ok) {
            const errorText = await response.text(); // อ่าน error response เป็น text
            console.error('API Error Response:', errorText);
            throw new Error(`HTTP error! Status: ${response.status} - ${errorText}`);
        }

        const data = await response.json();

        if (data.success) {
            if (data.rooms && data.rooms.length > 0) {
                roomListContainer.innerHTML = ''; // เคลียร์ข้อความ "กำลังโหลด"
                data.rooms.forEach(room => {
                    const roomCard = `
                        <div class="room-card">
                            <h3>${room.room_name}</h3>
                            <p class="capacity">ความจุ: ${room.capacity} คน</p>
                            <p>ที่ตั้ง: ${room.location || '-'}</p>
                            <p class="description">${room.description || 'ไม่มีรายละเอียด'}</p>
                            <button class="button primary mt-3" data-room-id="${room.id}">จองห้องนี้</button>
                        </div>
                    `;
                    roomListContainer.innerHTML += roomCard;
                });
                // สามารถเพิ่ม event listener ให้กับปุ่ม 'จองห้องนี้' ตรงนี้ได้ในอนาคต
            } else {
                roomListContainer.innerHTML = '<p>ไม่พบข้อมูลห้องประชุม.</p>';
            }
        } else {
            roomListContainer.innerHTML = `<p class="error-message">เกิดข้อผิดพลาด: ${data.message || 'ไม่สามารถโหลดข้อมูลห้องประชุมได้'}</p>`;
            console.error('Server returned an error:', data.message);
        }

    } catch (error) {
        roomListContainer.innerHTML = `<p class="error-message">ไม่สามารถโหลดข้อมูลห้องประชุมได้: ${error.message}</p>`;
        console.error('Error fetching rooms:', error);
    }
}

// เรียกฟังก์ชันเมื่อ DOM โหลดเสร็จ เพื่อแสดงข้อมูลห้องประชุม
fetchRooms();
});