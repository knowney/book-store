<?php
// ไฟล์นี้ใช้สำหรับรวม modal ยืนยันการกระทำด้วย Tailwind CSS
// ใช้ include ทุกหน้าที่ต้องการใช้ modal ยืนยัน
?>

<div id="confirm-modal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 backdrop-blur-sm overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all duration-200 scale-95 opacity-0 flex flex-col">
            <div class="p-4 border-b border-gray-200 shrink-0">
                <h3 id="confirm-title" class="text-lg font-semibold text-gray-900">ยืนยันการกระทำ</h3>
            </div>
            
            <div class="p-4">
                <p id="confirm-message" class="text-sm text-gray-700"></p>
                </div>
            
            <div class="flex justify-end p-4 border-t border-gray-200 space-x-3 shrink-0">
                <button id="confirm-cancel" class="px-4 py-2 rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400">
                    ยกเลิก
                </button>
                <button id="confirm-ok" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                    ยืนยัน
                </button>
            </div>
        </div>
        
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const modal = document.getElementById('confirm-modal');
    const titleEl = document.getElementById('confirm-title');
    const msgEl = document.getElementById('confirm-message');
    const okBtn = document.getElementById('confirm-ok');
    const cancelBtn = document.getElementById('confirm-cancel');
    
    let targetHref = null;
    let callback = null;

    // ลบพารามิเตอร์ showReason ออก
    function openModal(title, message, href, onConfirm) {
        titleEl.textContent = title || 'ยืนยันการกระทำ';
        msgEl.textContent = message || 'คุณแน่ใจหรือไม่?';
        targetHref = href;
        callback = onConfirm || null;
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.querySelector('.bg-white').classList.remove('scale-95', 'opacity-0');
        }, 10);
        
        okBtn.focus();
    }

    function closeModal() {
        const card = modal.querySelector('.bg-white');
        card.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            targetHref = null;
            callback = null;
        }, 200);
    }

    // จัดการลิงก์ที่มี class confirm-delete หรือ confirm-action
    document.querySelectorAll('a.confirm-delete, a.confirm-action').forEach(function(el){
        el.addEventListener('click', function(e){
            e.preventDefault();
            const title = el.getAttribute('data-title') || 'ยืนยันการกระทำ';
            const message = el.getAttribute('data-confirm') || 'คุณแน่ใจหรือไม่?';
            const href = el.getAttribute('href');
            
            openModal(title, message, href);
        });
    });

    // จัดการปุ่ม/ลิงก์ที่มี data-confirm โดยไม่ต้องมี class เฉพาะ
    document.querySelectorAll('a[data-confirm], button[data-confirm]').forEach(function(el){
        if (el.classList.contains('confirm-delete') || el.classList.contains('confirm-action')) return;
        
        el.addEventListener('click', function(e){
            if (el.tagName === 'BUTTON' || el.getAttribute('href') === '#') {
                e.preventDefault();
                const title = el.getAttribute('data-title') || 'ยืนยัน';
                const message = el.getAttribute('data-confirm') || 'คุณแน่ใจหรือไม่?';
                
                openModal(title, message, null, function() {
                    const form = el.closest('form');
                    if (form) {
                        form.submit();
                    } else {
                        const action = el.getAttribute('data-action');
                        if (action) {
                            eval(action);
                        }
                    }
                });
            } else {
                if (!confirm(el.getAttribute('data-confirm'))) {
                    e.preventDefault();
                }
            }
        });
    });

    cancelBtn.addEventListener('click', function(){
        closeModal();
    });

    okBtn.addEventListener('click', function(){
        if (typeof callback === 'function') {
            callback();
        } else if (targetHref) {
            // ไม่ต้องแนบ reason ไปกับ URL อีกต่อไป
            window.location.href = targetHref;
        }
        closeModal();
    });

    // ปิด modal เมื่อกด ESC
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // ปิด modal เมื่อคลิกพื้นหลัง
    modal.addEventListener('click', function(e){
        // ตรวจสอบว่าจุดที่คลิกไม่ใช่ตัวกล่องสีขาว (คลิกโดนพื้นที่สีดำเบลอๆ)
        if (!e.target.closest('.bg-white')) {
            closeModal();
        }
    });
});
</script>