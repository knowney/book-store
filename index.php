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

$query = "SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Store - ร้านหนังสือออนไลน์</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* small custom styles for carousel and cards */
        .hero-slide { height: 420px; }
        @media (max-width: 640px) { .hero-slide { height: 240px; } }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
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
    <!-- Hero carousel -->
    <section class="mt-6 container mx-auto px-4">
        <div id="hero-carousel" class="relative overflow-hidden rounded-lg">
            <div class="flex transition-transform duration-700" id="hero-slides" style="transform: translateX(0);">
                <!-- Slide 1 -->
                <div class="min-w-full hero-slide bg-cover bg-center" style="background-image: url('uploads/slider/slide1.jpg');">
                    <div class="h-full bg-black bg-opacity-30 flex items-center">
                        <div class="text-white max-w-xl ml-8">
                            <h2 class="text-3xl font-bold">ลดราคา หนังสือแนะนำ</h2>
                            <p class="mt-2">ดีลประจำสัปดาห์ เลือกซื้อก่อนสินค้าหมด</p>
                            <a href="index.php" class="mt-4 inline-block bg-yellow-400 text-black px-4 py-2 rounded">ช้อปเลย</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="min-w-full hero-slide bg-cover bg-center" style="background-image: url('uploads/slider/slide2.jpg');">
                    <div class="h-full bg-black bg-opacity-30 flex items-center">
                        <div class="text-white max-w-xl ml-8">
                            <h2 class="text-3xl font-bold">หนังสือใหม่มาแรง</h2>
                            <p class="mt-2">อัปเดตทุกสัปดาห์</p>
                            <a href="index.php" class="mt-4 inline-block bg-yellow-400 text-black px-4 py-2 rounded">ดูทั้งหมด</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 3 (placeholder if no image) -->
                <div class="min-w-full hero-slide bg-cover bg-center" style="background-image: url('https://via.placeholder.com/1200x420?text=Featured+Book');">
                    <div class="h-full bg-black bg-opacity-30 flex items-center">
                        <div class="text-white max-w-xl ml-8">
                            <h2 class="text-3xl font-bold">ส่งฟรี เมื่อสั่งครบ 500 บาท</h2>
                            <p class="mt-2">เฉพาะลูกค้าออนไลน์</p>
                            <a href="index.php" class="mt-4 inline-block bg-yellow-400 text-black px-4 py-2 rounded">สั่งซื้อเลย</a>
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

    <!-- Product grid -->
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-semibold mb-6">หนังสือทั้งหมด</h2>
        <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="bg-white rounded-lg shadow p-4 text-center product-card transition-transform duration-200">
                <?php if ($row['image']): ?>
                    <img src="<?php echo $row['image']; ?>" class="w-full h-56 object-cover rounded" alt="<?php echo $row['title']; ?>">
                <?php else: ?>
                    <div class="w-full h-56 bg-gray-200 flex items-center justify-center rounded">ไม่มีรูปภาพ</div>
                <?php endif; ?>
                <h3 class="mt-3 font-medium"><?php echo $row['title']; ?></h3>
                <p class="text-sm text-gray-600"><?php echo $row['author']; ?></p>
                <p class="text-sm text-gray-500"><?php echo substr($row['description'], 0, 80); ?>...</p>
                <div class="text-red-600 font-bold mt-2">฿<?php echo number_format($row['price'], 2); ?></div>
                <p class="text-green-600 text-sm">สต็อก: <?php echo $row['stock']; ?></p>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <form action="add_to_cart.php" method="POST" class="add-to-cart-form mt-3 flex items-center justify-center space-x-2">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $row['stock']; ?>" class="w-16 p-1 border rounded">
                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">เพิ่มลงตะกร้า</button>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="mt-3 inline-block bg-blue-600 text-white px-4 py-1 rounded">เข้าสู่ระบบ</a>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

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
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'เพิ่มลงตะกร้าเรียบร้อย', 'success');

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
                showToast('เชื่อมต่อล้มเหลว', 'error');
            });
        });
    });
    </script>
</body>
</html>