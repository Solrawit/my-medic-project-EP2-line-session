document.addEventListener("DOMContentLoaded", function() {
    const fileSelector = document.querySelector('.upper input[type="file"]');
    const startBtn = document.querySelector('.upper button');
    const img = document.querySelector('.bottom img');
    const progress = document.querySelector('.progress');
    const textarea = document.querySelector('.bottom textarea');

    fileSelector.onchange = handleFileSelect;
    startBtn.onclick = handleStartClick;

    function handleFileSelect() {
        const file = fileSelector.files[0];
        if (!file.type.startsWith('image/')) {
            showAlert('error', 'ประเภทไฟล์ไม่ถูกต้อง', "กรุณาอัปโหลดไฟล์รูปภาพ");
            fileSelector.value = '';
            img.src = '';
            return;
        }
        const imgUrl = window.URL.createObjectURL(file);
        img.src = imgUrl;
    }

    function handleStartClick() {
        const selectedFile = fileSelector.files[0];
        if (!selectedFile) {
            showAlert('warning', 'กรุณาใส่รูปภาพก่อน', "กรุณาใส่รูปภาพก่อน");
            return;
        }

        textarea.innerHTML = '';
        progress.innerHTML = 'กำลังแปลงข้อความ...';

        Tesseract.recognize(
            selectedFile,
            'eng+tha',
            { logger: m => updateProgress(m) }
        ).then(({ data: { text } }) => {
            const cleanedText = text.replace(/\s+/g, '');
            textarea.innerHTML = cleanedText;
            progress.innerHTML = 'เสร็จสิ้น';
            showAlert('success', 'ประมวลผลสำเร็จ', "ขอบคุณที่ใช้งาน :)");
        }).catch(err => {
            console.error('เกิดข้อผิดพลาดในระหว่างการจดจำ:', err);
            progress.innerHTML = 'เกิดข้อผิดพลาดในระหว่างการจดจำ';
        });
    }

    function showAlert(icon, title, text) {
        Swal.fire({
            icon,
            title,
            text,
            showConfirmButton: false,
            timer: 2000
        });
    }

    function updateProgress(message) {
        if (message.status === 'recognizing text') {
            progress.innerHTML = `กำลังแปลงข้อความ... ${Math.round(message.progress * 100)}%`;
        }
    }
});
