<?php
// ไฟล์นี้ใช้สำหรับรวม modal ยืนยันการกระทำด้วย Tailwind CSS
// ใช้ include ทุกหน้าที่ต้องการใช้ modal ยืนยัน
?>

<!-- Tailwind Confirm Modal -->
<div id="confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 transform transition-all duration-200 scale-95 opacity-0">
        <div class="p-4 border-b border-gray-200">
            <h3 id="confirm-title" class="text-lg font-semibold text-gray-900">ยืนยันการกระทำ</h3>
        </div>
        <div class="p-4">
            <p id="confirm-message" class="text-sm text-gray-700 mb-4"></p>
            <div id="confirm-reason-container" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">เหตุผล (ถ้ามี)</label>
                <input id="confirm-reason" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="กรุณากรอกเหตุผลการกระทำนี้">
            </div>
        </div>
        <div class="flex justify-end p-4 border-t border-gray-200 space-x-3">
            <button id="confirm-cancel" class="px-4 py-2 rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400">
                ยกเลิก
            </button>
            <button id="confirm-ok" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                ยืนยัน
            </button>
        </div>
    </div>
</div>

<!-- Tailwind Confirm Handler -->
<script>
document.addEventListener('DOMContentLoaded', function(){
    const modal = document.getElementById('confirm-modal');
    const titleEl = document.getElementById('confirm-title');
    const msgEl = document.getElementById('confirm-message');
    const reasonContainer = document.getElementById('confirm-reason-container');
    const reasonEl = document.getElementById('confirm-reason');
    const okBtn = document.getElementById('confirm-ok');
    const cancelBtn = document.getElementById('confirm-cancel');
    
    let targetHref = null;
    let callback = null;

    function openModal(title, message, href, showReason, onConfirm) {
        titleEl.textContent = title || 'ยืนยันการกระทำ';
        msgEl.textContent = message || 'คุณแน่ใจหรือไม่?';
        reasonContainer.classList.toggle('hidden', !showReason);
        reasonEl.value = '';
        targetHref = href;
        callback = onConfirm || null;
        
        modal.classList.remove('hidden');
        // ใช้ setTimeout เพื่อให้ animation เริ่มทำงาน
        setTimeout(() => {
            modal.querySelector('.bg-white').classList.remove('scale-95', 'opacity-0');
        }, 10);
        
        if (showReason) {
            reasonEl.focus();
        } else {
            okBtn.focus();
        }
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
            const title = el.getAttribute('data-title') || 'ยืนยันการลบ';
            const message = el.getAttribute('data-confirm') || 'คุณแน่ใจหรือไม่?';
            const href = el.getAttribute('href');
            const showReason = el.classList.contains('confirm-delete'); // แสดงช่องเหตุผลเฉพาะการลบ
            
            openModal(title, message, href, showReason);
        });
    });

    // จัดการปุ่ม/ลิงก์ที่มี data-confirm โดยไม่ต้องมี class เฉพาะ
    document.querySelectorAll('a[data-confirm], button[data-confirm]').forEach(function(el){
        // หลีกเลี่ยงการซ้ำซ้อนกับ confirm-delete/confirm-action
        if (el.classList.contains('confirm-delete') || el.classList.contains('confirm-action')) return;
        
        el.addEventListener('click', function(e){
            // สำหรับ form หรือการส่งข้อมูลอื่น ๆ ที่ไม่ใช่ลิงก์ไปหน้าอื่น
            if (el.tagName === 'BUTTON' || el.getAttribute('href') === '#') {
                e.preventDefault();
                const title = el.getAttribute('data-title') || 'ยืนยัน';
                const message = el.getAttribute('data-confirm') || 'คุณแน่ใจหรือไม่?';
                const showReason = false;
                
                openModal(title, message, null, showReason, function() {
                    // ถ้ามี form ให้ submit
                    const form = el.closest('form');
                    if (form) {
                        form.submit();
                    } else {
                        // ถ้ามี data-action ให้ eval (ใช้ด้วยความระมัดระวัง)
                        const action = el.getAttribute('data-action');
                        if (action) {
                            eval(action);
                        }
                    }
                });
            } else {
                // สำหรับลิงก์ธรรมดา ใช้ confirm แบบเดิมเพื่อความเข้ากันได้
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
            const reason = reasonEl.value.trim();
            callback(reason);
        } else if (targetHref) {
            const reason = reasonEl.value.trim();
            let url = new URL(targetHref, window.location.origin);
            if (reason) url.searchParams.set('reason', reason);
            window.location.href = url.toString();
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
        if (e.target === modal) {
            closeModal();
        }
    });
});
</script>