// ใน book_room.js

document.addEventListener('DOMContentLoaded', function() {
    // กำหนด URL ของ API
    const apiRoomsUrl = 'http://localhost/Gitclone/Project/api_get_rooms.php';
    const apiOfficersUrl = 'http://localhost/Gitclone/Project/api_get_officers.php';
    // const apiAvailableSlotsUrl = 'http://localhost/Gitclone/Project/api_get_available_slots.php'; // ไม่จำเป็นต้องใช้แล้ว
    const apiBookRoomUrl = 'http://localhost/Gitclone/Project/api_book_room.php';

    // อ้างอิงถึง Element ใน DOM
    const roomSelect = document.getElementById('room_id');
    const officerSelect = document.getElementById('officer_id');
    const bookingDateInput = document.getElementById('booking_date');
    
    // อ้างอิงถึง input เวลาใหม่
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    
    const bookingForm = document.getElementById('bookingForm');
    // ไม่ต้องอ้างถึง timeSlotsContainer แล้ว

    // --------------------------------------------------------
    // 1. โหลดข้อมูลห้องประชุมและเจ้าหน้าที่เมื่อหน้าเว็บโหลดเสร็จ (เหมือนเดิม)
    // --------------------------------------------------------
    async function loadRoomsAndOfficers() {
        try {
            const roomsResponse = await fetch(apiRoomsUrl);
            const roomsData = await roomsResponse.json();
            if (roomsData.success && roomsData.rooms) {
                roomsData.rooms.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.id;
                    option.textContent = room.room_name;
                    roomSelect.appendChild(option);
                });
            } else {
                console.error('Failed to load rooms:', roomsData.message);
                window.showMessage('ไม่สามารถโหลดรายการห้องประชุมได้', 'error');
            }

            const officersResponse = await fetch(apiOfficersUrl);
            const officersData = await officersResponse.json();
            if (officersData.success && officersData.officers) {
                officersData.officers.forEach(officer => {
                    const option = document.createElement('option');
                    option.value = officer.id;
                    option.textContent = officer.officer_name;
                    officerSelect.appendChild(option);
                });
            } else {
                console.error('Failed to load officers:', officersData.message);
                window.showMessage('ไม่สามารถโหลดรายชื่อเจ้าหน้าที่ได้', 'error');
            }
        } catch (error) {
            console.error('Error loading initial data:', error);
            window.showMessage('เกิดข้อผิดพลาดในการโหลดข้อมูลเริ่มต้น', 'error');
        }
    }
    loadRoomsAndOfficers();

    // --------------------------------------------------------
    // 2. ตั้งค่า Flatpickr สำหรับเลือกวันที่ (เหมือนเดิม)
    // --------------------------------------------------------
    flatpickr(bookingDateInput, {
        dateFormat: "Y-m-d", // รูปแบบวันที่ YYYY-MM-DD
        minDate: "today",    // เลือกได้ตั้งแต่วันนี้เป็นต้นไป
        locale: "th",        // ใช้ภาษาไทย
        // ไม่ต้องมี onChange เพื่อโหลด slot เวลาแล้ว
    });

    // ไม่ต้องมี event listener สำหรับ roomSelect.addEventListener('change') แล้ว
    // เพราะผู้ใช้จะกรอกเวลาเอง

    // --------------------------------------------------------
    // 3. จัดการการส่งฟอร์มจอง (ปรับปรุงการรับค่าเวลา)
    // --------------------------------------------------------

    bookingForm.addEventListener('submit', async function(event) {
        event.preventDefault(); // ป้องกันการ reload หน้า

        // ตรวจสอบว่าผู้ใช้กรอกเวลาแล้วหรือไม่
        if (!startTimeInput.value || !endTimeInput.value) {
            window.showMessage('โปรดกรอกเวลาเริ่มต้นและเวลาสิ้นสุด', 'error');
            return;
        }

        const formData = new FormData(bookingForm);
        
        // ใช้ค่าจาก input type="time" โดยตรง
        formData.set('start_time', bookingDateInput.value + ' ' + startTimeInput.value + ':00'); // เพิ่ม :00 สำหรับวินาที
        formData.set('end_time', bookingDateInput.value + ' ' + endTimeInput.value + ':00');   // เพิ่ม :00 สำหรับวินาที
        formData.set('user_id', currentLoggedInUserId); // ส่ง user_id จาก PHP session

        // Officer ID อาจจะเป็นค่าว่างได้
        if (formData.get('officer_id') === '') {
            formData.delete('officer_id'); // ลบออกจาก FormData ถ้าไม่ได้เลือก
        }

        try {
            const response = await fetch(apiBookRoomUrl, {
                method: 'POST',
                body: formData // ส่ง FormData โดยตรง
            });

            const data = await response.json();

            if (data.success) {
                window.showMessage(data.message || 'การจองสำเร็จ!', 'success');
                bookingForm.reset(); // ล้างฟอร์ม
                // ไม่ต้องรีเซ็ต selectedStartTime/selectedEndTime หรือ timeSlotsContainer แล้ว
                // อาจจะโหลดข้อมูลห้องประชุมใหม่ใน index.php หรือ redirect
            } else {
                window.showMessage(data.message || 'เกิดข้อผิดพลาดในการจอง', 'error');
            }
        } catch (error) {
            window.showMessage('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message, 'error');
            console.error('Error submitting booking:', error);
        }
    });

    // --------------------------------------------------------
    // ไม่มีฟังก์ชัน fetchAvailableTimeSlots และ displayTimeSlots อีกต่อไป
    // --------------------------------------------------------
    // ลบโค้ดส่วนนี้ออกทั้งหมด:
    /*
    async function fetchAvailableTimeSlots(roomId, date) { ... }
    function displayTimeSlots(availableSlots, bookedSlots) { ... }
    */

    // --------------------------------------------------------
    // โค้ดสำหรับ common.js ไม่ต้องอยู่ในนี้แล้ว (ตามที่เราย้ายไป common.js)
    // --------------------------------------------------------
    // ลบโค้ด Sidebar, Theme Toggler, Logout ที่เคยคัดลอกมาออกไปทั้งหมด
});