document.addEventListener("DOMContentLoaded", function() {
    const fileSelector = document.querySelector('.upper input[type="file"]');
    const startBtn = document.querySelector('.upper button');
    const img = document.querySelector('.bottom img');
    const progress = document.querySelector('.progress');
    const textarea = document.querySelector('.bottom textarea');

    // แสดงรูปที่อัปโหลด
    fileSelector.onchange = () => {
        const file = fileSelector.files[0];
        const imgUrl = window.URL.createObjectURL(file);
        img.src = imgUrl;
    };

    // เมื่อคลิกที่ปุ่ม "เริ่ม"
    startBtn.onclick = () => {
        const selectedFile = fileSelector.files[0];
        if (!selectedFile) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาใส่รูปภาพก่อน',
                text: "กรุณาใส่รูปภาพก่อน",
                showConfirmButton: false,
                timer: 2000
            });
            return;
        }

        textarea.innerHTML = '';
        progress.innerHTML = 'กำลังแปลงข้อความ...';

        // เริ่มการใช้ Tesseract.js เพื่อจดจำข้อความ
        Tesseract.recognize(
            selectedFile,
            'eng+tha', // ระบุภาษาที่ต้องการให้ Tesseract จดจำ (อังกฤษและไทย)
            { logger: m => console.log(m) }
        ).then(({ data: { text } }) => {
            // ลบช่องว่างที่เกินออกจากข้อความ
            const cleanedText = text.replace(/\s+/g, ''); // ใช้ regular expression เพื่อลบช่องว่างทั้งหมด
            textarea.innerHTML = cleanedText;
            progress.innerHTML = 'เสร็จสิ้น';

            // แสดงข้อความแจ้งเตือน "ประมวลผลสำเร็จ"
            Swal.fire({
                icon: 'success',
                title: 'ประมวลผลสำเร็จ',
                text: "ขอบคุณที่ใช้งาน :)",
                showConfirmButton: false,
                timer: 2000
            });
        }).catch(err => {
            console.error('เกิดข้อผิดพลาดในระหว่างการจดจำ:', err);
            progress.innerHTML = 'เกิดข้อผิดพลาดในระหว่างการจดจำ';
        });
    };
});
