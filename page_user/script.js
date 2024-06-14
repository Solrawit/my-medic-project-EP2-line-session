document.addEventListener("DOMContentLoaded", function() {
    const fileSelector = document.querySelector('.upper input[type="file"]');
    const startBtn = document.querySelector('.upper button');
    const img = document.querySelector('.bottom img');
    const progress = document.querySelector('.progress');
    const textarea = document.querySelector('.bottom textarea');

    // แสดงรูปที่อัปโหล
    fileSelector.onchange = () => {
        const file = fileSelector.files[0];
        const imgUrl = window.URL.createObjectURL(file);
        img.src = imgUrl;
    };

    // เริ่มการยอมรับข้อความ
    startBtn.onclick = () => {
        const selectedFile = fileSelector.files[0];
        if (!selectedFile) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาใส่รูปภาพก่อน',
                text: "Please enter a picture first. !!",
                showConfirmButton: false,
                timer: 2000
            });
            return;
        }

        textarea.innerHTML = '';
        progress.innerHTML = 'Recognizing text...';

        Tesseract.recognize(
            selectedFile,
            'eng+tha',
            { logger: m => console.log(m) }
        ).then(({ data: { text } }) => {
            // ลบช่องว่างที่เกินออกจากข้อความ
            const cleanedText = text.replace(/\s+/g, ''); // ใช้ regular expression เพื่อลบช่องว่างทั้งหมด
            textarea.innerHTML = cleanedText;
            progress.innerHTML = 'Done';
            
            // แสดงข้อความแจ้งเตือน "ประมวลผลสำเร็จ"
            Swal.fire({
                icon: 'success',
                title: 'ประมวลผลสำเร็จ',
                text: "ขอบคุณที่ใช้งาน :)",
                showConfirmButton: false,
                timer: 2000
            });
        }).catch(err => {
            console.error('Error during recognition:', err);
            progress.innerHTML = 'Error during recognition';
        });
    };
});