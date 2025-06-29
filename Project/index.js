// index.js (หลังย้ายโค้ดทั่วไปไป common.js)

document.addEventListener('DOMContentLoaded', function() {
    // ไม่มีโค้ด Sidebar, Theme, Logout ที่นี่แล้ว

    // ------------- ส่วนสำหรับดึงและแสดงข้อมูลห้องประชุม -------------
    const roomListContainer = document.getElementById('roomListContainer');
    const apiRoomsUrl = 'http://localhost/Gitclone/Project/api_get_rooms.php'; // <<--- ตรวจสอบ Path ของคุณ

    async function fetchRooms() {
        if (!roomListContainer) return;

        try {
            roomListContainer.innerHTML = '<p>กำลังโหลดข้อมูลห้องประชุม...</p>';
            const response = await fetch(apiRoomsUrl);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('API Error Response:', errorText);
                throw new Error(`HTTP error! Status: ${response.status} - ${errorText}`);
            }

            const data = await response.json();

            if (data.success) {
                if (data.rooms && data.rooms.length > 0) {
                    roomListContainer.innerHTML = '';
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

    fetchRooms();
});