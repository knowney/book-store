<?php
include 'config.php';

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $cnt_res = mysqli_query($conn, "SELECT SUM(quantity) as cnt FROM cart WHERE user_id='$uid'");
    if ($cnt_res && $cnt_row = mysqli_fetch_assoc($cnt_res)) {
        $cart_count = intval($cnt_row['cnt']);
    }
}

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $query = "SELECT * FROM products WHERE stock > 0 AND (title LIKE '%$search_escaped%' OR author LIKE '%$search_escaped%' OR description LIKE '%$search_escaped%') ORDER BY created_at DESC";
} else {
    $query = "SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC";
}
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Store - ร้านหนังสือออนไลน์</title>
    <meta name="keywords" content="หนังสือ, ร้านหนังสือออนไลน์, หนังสือใหม่, หนังสือลดราคา, หนังสือแนะนำ, หนังสือขายดี, หนังสือไทย, หนังสือต่างประเทศ">
    <meta name="description" content="ร้านหนังสือออนไลน์ที่รวบรวมหนังสือหลากหลายประเภท ทั้งหนังสือใหม่มาแรง หนังสือลดราคา และหนังสือแนะนำ พร้อมบริการจัดส่งทั่วประเทศ">
    <meta name="author" content="Book Store">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="Book Store - ร้านหนังสือออนไลน์">
    <meta property="og:description" content="ร้านหนังสือออนไลน์ที่รวบรวมหนังสือหลากหลายประเภท ทั้งหนังสือใหม่มาแรง หนังสือลดราคา และหนังสือแนะนำ">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://yourwebsite.com">
    <meta property="og:image" content="https://yourwebsite.com/images/og-image.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* small custom styles for carousel and cards */
        .hero-slide { height: 420px; }
        @media (max-width: 640px) { .hero-slide { height: 240px; } }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .product-card img { transition: transform 0.3s ease; }
        .product-card:hover img { transform: scale(1.05) rotate(2deg); }
        .carousel-dot { width: 10px; height: 10px; border-radius: 9999px; }
        :root{
            --primary:#0f172a;      /* new primary (dark slate) */
            --primary-700:#0b1220;
            --accent:#f59e0b;       /* accent (amber) */
            --accent-600:#f59e0b;
            --muted:#6b7280;
            --danger:#ef4444;
            --surface:#0b1220;
            --on-primary:#ffffff;
        }
        /* Tailwind class overrides */
        .bg-blue-700 { background-color: var(--primary) !important; }
        .bg-blue-800 { background-color: var(--primary-700) !important; }
        .bg-blue-600 { background-color: var(--primary) !important; }
        .text-white  { color: var(--on-primary) !important; }
        .bg-green-600 { background-color: var(--accent) !important; color:#000 !important; }
        .bg-yellow-400 { background-color: var(--accent) !important; color:#000 !important; }
        .text-blue-600 { color: var(--primary) !important; }
        .bg-red-600 { background-color: var(--danger) !important; }
        /* product card tweaks */
        .product-card { background: linear-gradient(180deg, #ffffff 0%, #fbfbfd 100%); }
        
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <?php include 'navbar.php'; ?>
    
    <!-- Toast Notification Container -->
    <div id="toast" class="fixed bottom-4 right-4 z-50 opacity-0 translate-y-2 transition-all duration-300 pointer-events-none">
        <div id="toast-inner" class="max-w-sm w-full rounded-lg p-4 shadow-lg border hidden">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p id="toast-message" class="text-sm font-medium text-gray-900"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <section class="container mx-auto px-4 mt-6">
        <form action="index.php" method="GET" class="flex gap-2">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ค้นหาหนังสือ ชื่อเรื่อง หรือผู้แต่ง..." class="flex-1 px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
            <?php if ($search): ?>
                <a href="index.php" class="bg-gray-500 text-white px-4 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                    ล้าง
                </a>
            <?php endif; ?>
        </form>
        <?php if ($search): ?>
            <p class="mt-3 text-gray-600">ผลการค้นหา: "<?php echo htmlspecialchars($search); ?>"</p>
        <?php endif; ?>
    </section>

    <!-- Hero carousel -->
    <section class="mt-6 container mx-auto px-4">
        <div id="hero-carousel" class="relative overflow-hidden rounded-lg">
            <div class="flex transition-transform duration-700" id="hero-slides" style="transform: translateX(0);">
            <!-- Slide 1 -->
                <div class="min-w-full hero-slide bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=1200&q=80');">
                    <div class="h-full bg-gradient-to-r from-black via-black/60 to-transparent flex items-center">
                        <div class="text-white max-w-xl ml-8">
                            <h2 class="text-4xl font-bold mb-2">ลดราคา หนังสือแนะนำ</h2>
                            <p class="text-lg opacity-90 mb-4">ดีลประจำสัปดาห์ เลือกซื้อก่อนสินค้าหมด</p>
                            <a href="index.php" class="inline-block bg-yellow-400 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-300 transition-colors shadow-lg">ช้อปเลย</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="min-w-full hero-slide bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1200&q=80');">
                    <div class="h-full bg-gradient-to-r from-black via-black/60 to-transparent flex items-center">
                        <div class="text-white max-w-xl ml-8">
                            <h2 class="text-4xl font-bold mb-2">หนังสือใหม่มาแรง</h2>
                            <p class="text-lg opacity-90 mb-4">อัปเดตทุกสัปดาห์</p>
                            <a href="index.php" class="inline-block bg-yellow-400 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-300 transition-colors shadow-lg">ดูทั้งหมด</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="min-w-full hero-slide bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=1200&q=80');">
                    <div class="h-full bg-gradient-to-r from-black via-black/60 to-transparent flex items-center">
                        <div class="text-white max-w-xl ml-8">
                            <h2 class="text-4xl font-bold mb-2">ส่งฟรี เมื่อสั่งครบ 500 บาท</h2>
                            <p class="text-lg opacity-90 mb-4">เฉพาะลูกค้าออนไลน์</p>
                            <a href="index.php" class="inline-block bg-yellow-400 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-300 transition-colors shadow-lg">สั่งซื้อเลย</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- controls -->
            <button id="prev-slide" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-60 hover:bg-opacity-100 rounded-full p-2">
                <!-- left arrow -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button id="next-slide" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-60 hover:bg-opacity-100 rounded-full p-2">
                <!-- right arrow -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>

            <!-- dots -->
            <div id="hero-dots" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                <button class="carousel-dot bg-white bg-opacity-60"></button>
                <button class="carousel-dot bg-white bg-opacity-40"></button>
                <button class="carousel-dot bg-white bg-opacity-40"></button>
            </div>
        </div>
    </section>

    <!-- Info cards -->
    <section class="container mx-auto px-4 mt-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded shadow flex items-center space-x-3">
                <div class="p-3 bg-blue-600 text-white rounded"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v6H3z" /></svg></div>
                <div>
                    <div class="font-semibold">บริการจัดส่ง</div>
                    <div class="text-sm text-gray-600">ส่งฟรีเมื่อสั่งครบ 500 บาท</div>
                </div>
            </div>
            <div class="bg-white p-4 rounded shadow flex items-center space-x-3">
                <div class="p-3 bg-green-600 text-white rounded"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
                <div>
                    <div class="font-semibold">สินค้าคุณภาพ</div>
                    <div class="text-sm text-gray-600">คัดสรรโดยทีมงาน</div>
                </div>
            </div>
            <div class="bg-white p-4 rounded shadow flex items-center space-x-3">
                <div class="p-3 bg-yellow-500 text-white rounded"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c.667 0 3 .333 3 3s-2.333 3-3 3-3-.333-3-3 2.333-3 3-3z" /></svg></div>
                <div>
                    <div class="font-semibold">การชำระเงินปลอดภัย</div>
                    <div class="text-sm text-gray-600">รองรับหลายช่องทาง</div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!$search): ?>
    <!-- Recommended Books Section -->
    <section class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-semibold mb-6">หนังสือแนะนำ</h2>
        <?php 
        // Query for recommended books (you can modify this logic based on your criteria)
        $recommended_query = "SELECT * FROM products WHERE stock > 10 ORDER BY created_at DESC LIMIT 12";
        $recommended_result = mysqli_query($conn, $recommended_query);
        ?>
        <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <?php while ($row = mysqli_fetch_assoc($recommended_result)): ?>
            <div class="bg-white rounded-lg shadow p-4 text-center product-card transition-transform duration-200">
                <?php if ($row['image']): ?>
                    <img src="<?php echo $row['image']; ?>" class="w-full h-80 object-cover rounded" alt="<?php echo $row['title']; ?>">
                <?php else: ?>
                    <div class="w-full h-80 bg-gray-200 flex items-center justify-center rounded">ไม่มีรูปภาพ</div>
                <?php endif; ?>
                <h3 class="mt-3 font-medium"><?php echo $row['title']; ?></h3>
                <p class="text-sm text-gray-600"><?php echo $row['author']; ?></p>
                <p class="text-sm text-gray-500"><?php echo substr($row['description'], 0, 80); ?>...</p>
                <div class="text-red-600 font-bold mt-2">฿<?php echo number_format($row['price'], 2); ?></div>
                <p class="text-green-600 text-sm">สต็อก : <?php echo $row['stock']; ?></p>
                <div class="mt-4 flex justify-center space-x-2">
                    <button onclick="openProductModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['title']); ?>', '<?php echo addslashes($row['author']); ?>', '<?php echo addslashes($row['description']); ?>', '<?php echo $row['price']; ?>', '<?php echo $row['stock']; ?>', '<?php echo $row['image']; ?>')" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                        รายละเอียด
                    </button>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <form action="add_to_cart.php" method="POST" class="add-to-cart-form inline-flex items-center">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">
                                เพิ่มลงตะกร้า
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition-colors">
                            เข้าสู่ระบบ
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Product grid -->
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-semibold mb-6">หนังสือทั้งหมด</h2>
        <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <?php 
            // Reset the result pointer for the main products query
            mysqli_data_seek($result, 0);
            while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="bg-white rounded-lg shadow p-4 text-center product-card transition-transform duration-200">
                <?php if ($row['image']): ?>
                    <img src="<?php echo $row['image']; ?>" class="w-full h-80 object-cover rounded" alt="<?php echo $row['title']; ?>">
                <?php else: ?>
                    <div class="w-full h-80 bg-gray-200 flex items-center justify-center rounded">ไม่มีรูปภาพ</div>
                <?php endif; ?>
                <h3 class="mt-3 font-medium"><?php echo $row['title']; ?></h3>
                <p class="text-sm text-gray-600"><?php echo $row['author']; ?></p>
                <p class="text-sm text-gray-500"><?php echo substr($row['description'], 0, 80); ?>...</p>
                <div class="text-red-600 font-bold mt-2">฿<?php echo number_format($row['price'], 2); ?></div>
                <p class="text-green-600 text-sm">สต็อก: <?php echo $row['stock']; ?></p>
                <div class="mt-4 flex justify-center space-x-2">
                    <button onclick="openProductModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['title']); ?>', '<?php echo addslashes($row['author']); ?>', '<?php echo addslashes($row['description']); ?>', '<?php echo $row['price']; ?>', '<?php echo $row['stock']; ?>', '<?php echo $row['image']; ?>')" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                        รายละเอียด
                    </button>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <form action="add_to_cart.php" method="POST" class="add-to-cart-form inline-flex items-center">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">
                                เพิ่มลงตะกร้า
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition-colors">
                            เข้าสู่ระบบ
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Search Results Section (shown when searching) -->
    <?php if ($search): ?>
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-semibold mb-6">ผลการค้นหา</h2>
        <?php 
        $count = mysqli_num_rows($result);
        if ($count > 0): 
        ?>
        <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <?php 
            mysqli_data_seek($result, 0);
            while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="bg-white rounded-lg shadow p-4 text-center product-card transition-transform duration-200">
                <?php if ($row['image']): ?>
                    <img src="<?php echo $row['image']; ?>" class="w-full h-80 object-cover rounded" alt="<?php echo $row['title']; ?>">
                <?php else: ?>
                    <div class="w-full h-80 bg-gray-200 flex items-center justify-center rounded">ไม่มีรูปภาพ</div>
                <?php endif; ?>
                <h3 class="mt-3 font-medium"><?php echo $row['title']; ?></h3>
                <p class="text-sm text-gray-600"><?php echo $row['author']; ?></p>
                <p class="text-sm text-gray-500"><?php echo substr($row['description'], 0, 80); ?>...</p>
                <div class="text-red-600 font-bold mt-2">฿<?php echo number_format($row['price'], 2); ?></div>
                <p class="text-green-600 text-sm">สต็อก: <?php echo $row['stock']; ?></p>
                <div class="mt-4 flex justify-center space-x-2">
                    <button onclick="openProductModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['title']); ?>', '<?php echo addslashes($row['author']); ?>', '<?php echo addslashes($row['description']); ?>', '<?php echo $row['price']; ?>', '<?php echo $row['stock']; ?>', '<?php echo $row['image']; ?>')" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                        รายละเอียด
                    </button>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <form action="add_to_cart.php" method="POST" class="add-to-cart-form inline-flex items-center">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">
                                เพิ่มลงตะกร้า
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition-colors">
                            เข้าสู่ระบบ
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">ไม่พบหนังสือที่ค้นหา</p>
            <a href="index.php" class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition-colors">กลับไปหน้าหลัก</a>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <script>
    // Carousel logic
    (function(){
        const slidesEl = document.getElementById('hero-slides');
        const slides = slidesEl.children;
        const total = slides.length;
        let index = 0;
        const dots = document.querySelectorAll('#hero-dots .carousel-dot');
        function show(i){
            slidesEl.style.transform = 'translateX(' + (-i * 100) + '%)';
            dots.forEach((d, idx) => d.classList.toggle('bg-opacity-100', idx === i));
        }
        document.getElementById('prev-slide').addEventListener('click', () => { index = (index -1 + total) % total; show(index); });
        document.getElementById('next-slide').addEventListener('click', () => { index = (index +1) % total; show(index); });
        dots.forEach((d, idx) => d.addEventListener('click', () => { index = idx; show(index); }));
        show(0);
        setInterval(()=> { index = (index +1) % total; show(index); }, 5000);
    })();

    // Toast helpers & add-to-cart AJAX (keeps existing behavior)
    const toastEl = document.getElementById('toast');
    const toastInner = document.getElementById('toast-inner');
    const toastMsg = document.getElementById('toast-message');

    function showToast(message, type = 'success') {
        toastMsg.textContent = message;
        toastInner.className = 'max-w-sm w-full rounded-lg p-4 shadow-lg border';
        if (type === 'success') {
            toastInner.classList.add('bg-white','border-green-100');
        } else {
            toastInner.classList.add('bg-white','border-red-100');
        }
        toastInner.classList.remove('hidden');
        toastEl.classList.remove('opacity-0','translate-y-2');
        toastEl.classList.add('opacity-100','translate-y-0');
        toastEl.style.pointerEvents = 'auto';
        clearTimeout(window._toastTimeout);
        window._toastTimeout = setTimeout(() => {
            toastEl.classList.remove('opacity-100','translate-y-0');
            toastEl.classList.add('opacity-0','translate-y-2');
            toastInner.classList.add('hidden');
            toastEl.style.pointerEvents = 'none';
        }, 3000);
    }

    document.querySelectorAll('form.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = form.getAttribute('action');
            const formData = new FormData(form);
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                console.log('Add to cart response:', data); // Debug log
                if (data.success) {
                    showToast(data.message || 'เพิ่มเข้าตะกร้าแล้ว', 'success');

                    // Update cart count in navbar (real-time)
                    const cartLink = document.getElementById('cart-link');
                    const existingBadge = document.getElementById('cart-count');
                    const cnt = parseInt(data.cart_count || 0, 10);

                    if (cnt > 0) {
                        if (existingBadge) {
                            existingBadge.textContent = cnt;
                        } else {
                            const badge = document.createElement('span');
                            badge.id = 'cart-count';
                            badge.className = 'absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full';
                            badge.textContent = cnt;
                            cartLink.appendChild(badge);
                        }
                    } else {
                        if (existingBadge) existingBadge.remove();
                    }
                } else {
                    showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
                }
            })
            .catch(err => {
                console.error('Add to cart error:', err); // Debug log
                showToast('เชื่อมต่อล้มเหลว', 'error');
            });
        });
    });
    </script>

    <!-- Product Detail Modal -->
   <div id="product-modal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 backdrop-blur-sm overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl transform transition-all duration-200 scale-95 opacity-0 flex flex-col max-h-[90vh]">
            
            <div class="p-4 border-b flex justify-between items-center shrink-0">
                <h3 id="modal-title" class="text-lg font-semibold text-gray-900">รายละเอียดหนังสือ</h3>
                <button id="close-modal" class="text-gray-400 hover:text-gray-600 text-xl leading-none focus:outline-none">&times;</button>
            </div>
            
            <div class="p-4 overflow-y-auto flex-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <img id="modal-image" src="" class="w-full h-auto object-cover rounded-md shadow-sm border border-gray-100" alt="หนังสือ">
                    </div>
                    <div>
                        <h4 id="modal-book-title" class="text-xl font-bold mb-2 text-gray-900"></h4>
                        <p id="modal-author" class="text-sm text-gray-600 mb-2"></p>
                        <p id="modal-price" class="text-red-600 font-bold mb-2 text-xl"></p>
                        <p id="modal-stock" class="text-green-600 text-sm mb-4 font-medium bg-green-50 inline-block px-2 py-1 rounded"></p>
                        <p id="modal-description" class="text-sm text-gray-700 leading-relaxed"></p>
                    </div>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-100 flex justify-end shrink-0">
                <button id="close-modal-bottom" class="px-5 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors font-medium focus:outline-none focus:ring-2 focus:ring-gray-300">
                    ปิด
                </button>
            </div>
            
        </div>
    </div>
</div>

    <script>
        const productModal = document.getElementById('product-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalImage = document.getElementById('modal-image');
        const modalBookTitle = document.getElementById('modal-book-title');
        const modalAuthor = document.getElementById('modal-author');
        const modalPrice = document.getElementById('modal-price');
        const modalStock = document.getElementById('modal-stock');
        const modalDescription = document.getElementById('modal-description');
        const closeModalBtn = document.getElementById('close-modal');
        const closeModalBottomBtn = document.getElementById('close-modal-bottom');

        function openProductModal(id, title, author, description, price, stock, image) {
            modalTitle.textContent = 'รายละเอียดหนังสือ';
            modalBookTitle.textContent = title;
            modalAuthor.textContent = 'ผู้แต่ง : ' + author;
            modalPrice.textContent = 'ราคา : ฿' + parseFloat(price).toFixed(2);
            modalStock.textContent = 'สต็อก : ' + stock;
            modalDescription.textContent = description;
            modalImage.src = image || 'https://via.placeholder.com/300x400?text=No+Image';
            
            productModal.classList.remove('hidden');
            setTimeout(() => {
                productModal.querySelector('.bg-white').classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function closeProductModal() {
            const card = productModal.querySelector('.bg-white');
            card.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                productModal.classList.add('hidden');
            }, 200);
        }

        closeModalBtn.addEventListener('click', closeProductModal);
        closeModalBottomBtn.addEventListener('click', closeProductModal);

        productModal.addEventListener('click', function(e) {
            if (e.target === productModal) {
                closeProductModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !productModal.classList.contains('hidden')) {
                closeProductModal();
            }
        });
    </script>
</body>
</html>
