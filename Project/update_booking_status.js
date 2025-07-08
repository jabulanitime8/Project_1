
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('.recent-orders tbody');

    if (tableBody) {
        tableBody.addEventListener('click', function(event) {
            let target = event.target;

            // ตรวจสอบว่าคลิกที่ปุ่ม approve-btn หรือ reject-btn
            if (target.classList.contains('approve-btn') || target.classList.contains('reject-btn')) {
                const bookingId = target.dataset.id; // ดึง booking_id จาก data-id
                let newStatus;

                if (target.classList.contains('approve-btn')) {
                    newStatus = 'approved';
                } else if (target.classList.contains('reject-btn')) {
                    newStatus = 'rejected';
                }

                if (confirm(`คุณต้องการ ${newStatus === 'approved' ? 'อนุมัติ' : 'ปฏิเสธ'} การจอง ID ${bookingId} นี้หรือไม่?`)) {
                    // ส่ง AJAX Request
                    fetch('update_booking_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `booking_id=${bookingId}&status=${newStatus}`
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            // อัปเดต UI: ลบแถวที่ถูกจัดการออกไปจากตาราง
                            const row = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
                            if (row) {
                                row.remove(); // ลบแถวออก
                                // ตรวจสอบว่ายังมีแถวเหลืออยู่หรือไม่ ถ้าไม่มี ให้แสดงข้อความว่า "ไม่มีการจองที่รออนุมัติ"
                                const remainingRows = tableBody.querySelectorAll('tr[data-booking-id]');
                                if (remainingRows.length === 0) {
                                    tableBody.innerHTML = '<tr><td colspan="9" style="text-align: center;">ไม่มีการจองที่รออนุมัติ</td></tr>';
                                }
                            }
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message);
                    });
                }
            }
        });
    }
});