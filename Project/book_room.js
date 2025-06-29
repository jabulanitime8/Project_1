// book_room.js (หลังย้ายโค้ดทั่วไปไป common.js)

document.addEventListener('DOMContentLoaded', function() {
    // กำหนด URL ของ API
    const apiRoomsUrl = 'http://localhost/Gitclone/Project/api_get_rooms.php';
    const apiOfficersUrl = 'http://localhost/Gitclone/Project/api_get_officers.php';
    const apiAvailableSlotsUrl = 'http://localhost/Gitclone/Project/api_get_available_slots.php';
    const apiBookRoomUrl = 'http://localhost/Gitclone/Project/api_book_room.php';

    // อ้างอิงถึง Element ใน DOM
    const roomSelect = document.getElementById('room_id');
    const officerSelect = document.getElementById('officer_id');
    const bookingDateInput = document.getElementById('booking_date');
    const timeSlotsContainer = document.getElementById('timeSlotsContainer');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const bookingForm = document.getElementById('bookingForm');
    // ไม่ต้องอ้างถึง bookingMessageDiv โดยตรงแล้ว เพราะจะใช้ window.showMessage

    let selectedStartTime = null;
    let selectedEndTime = null;

    // --------------------------------------------------------
    // 1. โหลดข้อมูลห้องประชุมและเจ้าหน้าที่เมื่อหน้าเว็บโหลดเสร็จ
    // --------------------------------------------------------

    async function loadRoomsAndOfficers() {
        try {
            // โหลดห้องประชุม
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

            // โหลดเจ้าหน้าที่
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
    // 2. ตั้งค่า Flatpickr สำหรับเลือกวันที่
    // --------------------------------------------------------
    flatpickr(bookingDateInput, {
        dateFormat: "Y-m-d",
        minDate: "today",
        locale: "th",
        onChange: function(selectedDates, dateStr, instance) {
            if (roomSelect.value && dateStr) {
                fetchAvailableTimeSlots(roomSelect.value, dateStr);
            } else {
                timeSlotsContainer.innerHTML = '<p>โปรดเลือกห้องและวันที่ก่อน</p>';
                selectedStartTime = null;
                selectedEndTime = null;
                startTimeInput.value = '';
                endTimeInput.value = '';
            }
        }
    });

    roomSelect.addEventListener('change', function() {
        if (roomSelect.value && bookingDateInput.value) {
            fetchAvailableTimeSlots(roomSelect.value, bookingDateInput.value);
        } else {
            timeSlotsContainer.innerHTML = '<p>โปรดเลือกห้องและวันที่ก่อน</p>';
            selectedStartTime = null;
            selectedEndTime = null;
            startTimeInput.value = '';
            endTimeInput.value = '';
        }
    });

    // --------------------------------------------------------
    // 3. ดึงช่วงเวลาที่ว่างจาก API
    // --------------------------------------------------------

    async function fetchAvailableTimeSlots(roomId, date) {
        timeSlotsContainer.innerHTML = '<p>กำลังโหลดช่วงเวลาที่ว่าง...</p>';
        selectedStartTime = null;
        selectedEndTime = null;
        startTimeInput.value = '';
        endTimeInput.value = '';

        try {
            const response = await fetch(`${apiAvailableSlotsUrl}?room_id=${roomId}&date=${date}`);
            const data = await response.json();

            if (data.success && data.available_slots) {
                displayTimeSlots(data.available_slots, data.booked_slots);
            } else {
                timeSlotsContainer.innerHTML = `<p class="error-message">ไม่สามารถโหลดช่วงเวลาได้: ${data.message || 'เกิดข้อผิดพลาด'}</p>`;
                console.error('Server returned an error for available slots:', data.message);
            }
        } catch (error) {
            timeSlotsContainer.innerHTML = `<p class="error-message">ไม่สามารถโหลดช่วงเวลาได้: ${error.message}</p>`;
            console.error('Error fetching available slots:', error);
        }
    }

    // --------------------------------------------------------
    // 4. แสดงช่วงเวลาที่ว่างและจัดการการเลือก
    // --------------------------------------------------------

    function displayTimeSlots(availableSlots, bookedSlots) {
        timeSlotsContainer.innerHTML = '';
        if (availableSlots.length === 0 && bookedSlots.length === 0) {
            timeSlotsContainer.innerHTML = '<p>ไม่มีช่วงเวลาที่สามารถจองได้ในวันนี้.</p>';
            return;
        }

        const bookedMap = new Map();
        bookedSlots.forEach(slot => {
            bookedMap.set(slot.start_time + '-' + slot.end_time, true);
        });

        availableSlots.forEach(slot => {
            const slotElement = document.createElement('div');
            slotElement.classList.add('time-slot-item');
            slotElement.textContent = `${slot.start_time.substring(0, 5)} - ${slot.end_time.substring(0, 5)}`;

            const slotKey = slot.start_time + '-' + slot.end_time;
            if (bookedMap.has(slotKey)) {
                slotElement.classList.add('booked');
                slotElement.title = 'ช่วงเวลานี้ไม่ว่าง';
            } else {
                slotElement.dataset.startTime = slot.start_time;
                slotElement.dataset.endTime = slot.end_time;
                slotElement.addEventListener('click', function() {
                    if (this.classList.contains('booked')) return;

                    const currentSelected = timeSlotsContainer.querySelector('.time-slot-item.selected');
                    if (currentSelected) {
                        currentSelected.classList.remove('selected');
                    }
                    this.classList.add('selected');
                    selectedStartTime = this.dataset.startTime;
                    selectedEndTime = this.dataset.endTime;
                    startTimeInput.value = selectedStartTime;
                    endTimeInput.value = selectedEndTime;
                });
            }
            timeSlotsContainer.appendChild(slotElement);
        });
    }

    // --------------------------------------------------------
    // 5. จัดการการส่งฟอร์มจอง
    // --------------------------------------------------------

    bookingForm.addEventListener('submit', async function(event) {
        event.preventDefault();

        if (!selectedStartTime || !selectedEndTime) {
            window.showMessage('โปรดเลือกช่วงเวลาที่ต้องการจอง', 'error');
            return;
        }

        const formData = new FormData(bookingForm);
        formData.set('start_time', bookingDateInput.value + ' ' + selectedStartTime);
        formData.set('end_time', bookingDateInput.value + ' ' + selectedEndTime);
        formData.set('user_id', <?php echo json_encode($current_user_id); ?>);

        if (formData.get('officer_id') === '') {
            formData.delete('officer_id');
        }

        try {
            const response = await fetch(apiBookRoomUrl, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                window.showMessage(data.message || 'การจองสำเร็จ!', 'success');
                bookingForm.reset();
                selectedStartTime = null;
                selectedEndTime = null;
                startTimeInput.value = '';
                endTimeInput.value = '';
                timeSlotsContainer.innerHTML = '<p>โปรดเลือกห้องและวันที่ก่อน</p>';
                // อัปเดตช่วงเวลาที่ว่างอีกครั้งหลังจากจองสำเร็จ เพื่อให้แสดงสถานะที่ถูกต้อง
                if (roomSelect.value && bookingDateInput.value) {
                     fetchAvailableTimeSlots(roomSelect.value, bookingDateInput.value);
                }
            } else {
                window.showMessage(data.message || 'เกิดข้อผิดพลาดในการจอง', 'error');
            }
        } catch (error) {
            window.showMessage('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message, 'error');
            console.error('Error submitting booking:', error);
        }
    });
});