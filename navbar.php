<?php
// Get cart count for navbar
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    $cnt_res = mysqli_query($conn, "SELECT SUM(quantity) as cnt FROM cart WHERE user_id='$user_id'");
    if ($cnt_res && $cnt_row = mysqli_fetch_assoc($cnt_res)) {
        $cart_count = intval($cnt_row['cnt']);
    }
}

// Get user info for dropdown
$user_info = null;
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    $user_query = mysqli_query($conn, "SELECT username, role FROM users WHERE id='$user_id'");
    if ($user_query && $user_row = mysqli_fetch_assoc($user_query)) {
        $user_info = $user_row;
    }
}
?>

<!-- Modern Responsive Navbar -->
<nav class="bg-gradient-to-r from-blue-800 via-blue-700 to-blue-800 shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center">
                <a href="index.php" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 bg-white rounded-lg shadow-md flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-bold text-white">Book Store</span>
                        <p class="text-xs text-blue-200 hidden sm:block">ร้านหนังสือออนไลน์</p>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <!-- Main Navigation Links -->
                <div class="flex items-center space-x-6">
                    <a href="index.php" class="text-white hover:text-yellow-300 transition-colors font-medium flex items-center space-x-2 group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>หน้าหลัก</span>
                    </a>
                    
                    <a href="cart.php" id="cart-link" class="text-white hover:text-yellow-300 transition-colors font-medium flex items-center space-x-2 group relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>ตะกร้า</span>
                        <?php if ($cart_count > 0): ?>
                            <span id="cart-count" class="absolute -top-2 -right-6 bg-red-500 text-white text-xs rounded-full px-2 py-1 font-bold min-w-[24px] text-center">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    
                    <a href="orders_history.php" class="text-white hover:text-yellow-300 transition-colors font-medium flex items-center space-x-2 group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>ออเดอร์ของฉัน</span>
                    </a>
                </div>

                <!-- User Dropdown -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="relative group">
                        <button class="flex items-center space-x-3 text-white hover:text-yellow-300 transition-colors font-medium group">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span><?php echo htmlspecialchars($user_info['username']); ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:rotate-180 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform group-hover:translate-y-0 -translate-y-2 z-50">
                            <div class="p-4 border-b border-gray-100">
                                <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($user_info['username']); ?></p>
                                <p class="text-sm text-gray-600"><?php echo $user_info['role'] == 'admin' ? 'ผู้ดูแลระบบ' : 'สมาชิก'; ?></p>
                            </div>
                            
                            <div class="py-2">
                                <a href="profile.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    ตั้งค่าโปรไฟล์
                                </a>
                                
                                <?php if ($user_info['role'] == 'admin'): ?>
                                    <a href="admin/index.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        จัดการระบบ
                                    </a>
                                <?php endif; ?>
                                
                                <hr class="my-2">
                                
                                    <form method="POST" action="logout.php" class="px-4 py-2">
                                        <button type="submit" class="w-full flex items-center px-2 py-2 text-red-600 hover:bg-red-50 rounded transition-colors confirm-action" data-confirm="คุณแน่ใจหรือไม่ว่าต้องการออกจากระบบ?">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            ออกจากระบบ
                                        </button>
                                    </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex items-center space-x-4">
                        <a href="login.php" class="text-white hover:text-yellow-300 transition-colors font-medium">เข้าสู่ระบบ</a>
                        <a href="register.php" class="bg-white text-blue-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors font-medium shadow-md">
                            สมัครสมาชิก
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="text-white hover:text-yellow-300 transition-colors focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div id="mobile-menu" class="md:hidden hidden bg-white border-t border-gray-200 shadow-lg">
        <div class="px-4 py-4 space-y-4">
            <a href="index.php" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition-colors py-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>หน้าหลัก</span>
            </a>
            
            <a href="cart.php" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition-colors py-2 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span>ตะกร้า</span>
                <?php if ($cart_count > 0): ?>
                    <span class="absolute right-0 bg-red-500 text-white text-xs rounded-full px-2 py-1 font-bold min-w-[24px] text-center">
                        <?php echo $cart_count; ?>
                    </span>
                <?php endif; ?>
            </a>
            
            <a href="orders_history.php" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition-colors py-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>ออเดอร์ของฉัน</span>
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <hr class="border-gray-200">
                
                <div class="space-y-2">
                    <a href="profile.php" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition-colors py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>ตั้งค่าโปรไฟล์</span>
                    </a>
                    
                    <?php if ($user_info['role'] == 'admin'): ?>
                        <a href="admin/index.php" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition-colors py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>จัดการระบบ</span>
                        </a>
                    <?php endif; ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
    // หาปุ่มทั้งหมดที่มีคลาส confirm-action
    const confirmButtons = document.querySelectorAll('.confirm-action');
    
    confirmButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            // ดึงข้อความจาก data-confirm
            const message = this.getAttribute('data-confirm');
            
            // แสดงหน้าต่าง Confirm
            if (!confirm(message)) {
                // ถ้าผู้ใช้กด Cancel ให้ยกเลิกการ submit
                event.preventDefault(); 
            }
        });
    });
});
                    </script>
                  <form method="POST" action="logout.php" class="pt-2">
    <button type="submit" class="w-full flex items-center space-x-3 text-red-600 hover:text-red-700 transition-colors py-2" data-title="ยืนยันการออกจากระบบ" data-confirm="คุณแน่ใจหรือไม่ว่าต้องการออกจากระบบ?">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        <span>ออกจากระบบ</span>
    </button>
</form>
                </div>
            <?php else: ?>
                <hr class="border-gray-200">
                <div class="space-y-2">
                    <a href="login.php" class="block text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        เข้าสู่ระบบ
                    </a>
                    <a href="register.php" class="block text-center border border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors">
                        สมัครสมาชิก
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            const icon = this.querySelector('svg');
            if (mobileMenu.classList.contains('hidden')) {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
            } else {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
            }
        });
    }
});
</script>

<!-- Include Tailwind Confirm Modal -->
<?php include 'includes/confirm_modal.php'; ?>
