// my_bookings.js

document.addEventListener('DOMContentLoaded', function() {
    const apiGetMyBookingsUrl = 'http://localhost/Gitclone/Project/api_get_my_bookings.php';
    const bookingListContainer = document.getElementById('bookingListContainer');
    const bookingMessage = document.getElementById('bookingMessage');

    // ฟังก์ชันสำหรับโหลดและแสดงการจองของผู้ใช้
    async function loadMyBookings() {
        bookingListContainer.innerHTML = '<p>กำลังโหลดการจองของคุณ...</p>';
        bookingMessage.innerHTML = ''; // ล้างข้อความเก่า

        try {
            // currentLoggedInUserId มาจาก my_bookings.php
            const response = await fetch(apiGetMyBookingsUrl + '?user_id=' + currentLoggedInUserId); 
            const data = await response.json();

            if (data.success) {
                if (data.bookings && data.bookings.length > 0) {
                    displayBookings(data.bookings);
                } else {
                    bookingListContainer.innerHTML = '<p>คุณยังไม่มีการจองห้องประชุมในขณะนี้</p>';
                }
            } else {
                window.showMessage(data.message || 'ไม่สามารถโหลดการจองได้', 'error');
                bookingListContainer.innerHTML = '<p>เกิดข้อผิดพลาดในการโหลดข้อมูลการจอง</p>';
            }
        } catch (error) {
            window.showMessage('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message, 'error');
            bookingListContainer.innerHTML = '<p>ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์เพื่อโหลดการจองได้</p>';
            console.error('Error loading my bookings:', error);
        }
    }

    // ฟังก์ชันสำหรับแสดงผลการจองในตาราง
    function displayBookings(bookings) {
        let html = `
            <table>
                <thead>
                    <tr>
                        <th>หัวข้อ</th>
                        <th>ห้องประชุม</th>
                        <th>เจ้าหน้าที่</th>
                        <th>เวลาเริ่มต้น</th>
                        <th>เวลาสิ้นสุด</th>
                        <th>สถานะ</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
        `;

        bookings.forEach(booking => {
            // จัดรูปแบบวันที่และเวลา
            const startDate = new Date(booking.start_time);
            const endDate = new Date(booking.end_time);
            const formattedStartTime = startDate.toLocaleString('th-TH', { 
                year: 'numeric', month: 'numeric', day: 'numeric', 
                hour: '2-digit', minute: '2-digit', hour12: false 
            });
            const formattedEndTime = endDate.toLocaleString('th-TH', { 
                year: 'numeric', month: 'numeric', day: 'numeric', 
                hour: '2-digit', minute: '2-digit', hour12: false 
            });

            // กำหนดสีสถานะ
            let statusClass = '';
            let statusText = '';
            switch (booking.status) {
                case 'pending':
                    statusClass = 'status-pending';
                    statusText = 'รอดำเนินการ';
                    break;
                case 'approved':
                    statusClass = 'status-approved';
                    statusText = 'อนุมัติแล้ว';
                    break;
                case 'rejected':
                    statusClass = 'status-rejected';
                    statusText = 'ถูกปฏิเสธ';
                    break;
                case 'completed':
                    statusClass = 'status-completed';
                    statusText = 'เสร็จสิ้น';
                    break;
                default:
                    statusClass = 'status-unknown';
                    statusText = 'ไม่ทราบสถานะ';
            }

            // ปุ่มยกเลิกจะแสดงเฉพาะสถานะ 'pending' และ 'approved' และถ้าเวลายังไม่ผ่านไป
            const now = new Date();
            const canCancel = (booking.status === 'pending' || booking.status === 'approved') && startDate > now;
            const actionButton = canCancel ? 
                `<button class="cancel-btn" data-booking-id="${booking.booking_id}">ยกเลิก</button>` : 
                '';

            html += `
                <tr>
                    <td>${booking.title}</td>
                    <td>${booking.room_name}</td>
                    <td>${booking.officer_name || '-'}</td>
                    <td>${formattedStartTime}</td>
                    <td>${formattedEndTime}</td>
                    <td><span class="status ${statusClass}">${statusText}</span></td>
                    <td>${actionButton}</td>
                </tr>
            `;
        });

        html += `
                </tbody>
            </table>
        `;
        bookingListContainer.innerHTML = html;

        // เพิ่ม Event Listener ให้กับปุ่มยกเลิก
        document.querySelectorAll('.cancel-btn').forEach(button => {
            button.addEventListener('click', async function() {
                const bookingId = this.dataset.bookingId;
                if (confirm('คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการจองนี้?')) {
                    await cancelBooking(bookingId);
                }
            });
        });
    }

    // ฟังก์ชันสำหรับยกเลิกการจอง (จะสร้าง API ใหม่ในขั้นตอนถัดไป)
    async function cancelBooking(bookingId) {
        // ในตอนนี้ยังไม่มี API สำหรับยกเลิก เราจะสร้างมันในขั้นตอนต่อไป
        // สำหรับตอนนี้แค่แสดงข้อความ
        window.showMessage(`ฟังก์ชันยกเลิกการจอง ID: ${bookingId} จะถูกเพิ่มในภายหลัง`, 'info');
        // คุณสามารถเพิ่มโค้ดเรียก API สำหรับยกเลิกที่นี่ได้
        // เช่น:
        // const response = await fetch('api_cancel_booking.php', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ booking_id: bookingId })
        // });
        // const data = await response.json();
        // if (data.success) {
        //     window.showMessage(data.message, 'success');
        //     loadMyBookings(); // โหลดรายการใหม่หลังจากยกเลิก
        // } else {
        //     window.showMessage(data.message, 'error');
        // }
    }

    // โหลดการจองเมื่อหน้าเว็บโหลดเสร็จ
    loadMyBookings();
});