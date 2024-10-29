<?php
require 'database.php';

$predefinedCategories = [
    'Bed' => 'เตียง',
    'Cabinet' => 'ตู้เก็บของ',
    'Vanity' => 'โต๊ะเครื่องแป้ง',
    'Wardrobe' => 'ตู้เสื้อผ้า'
];

// สร้างตัวแปรเก็บหมวดหมู่ของสินค้า
$categories = [];

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$sql = "SELECT * FROM products";
if (!$conn) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว");
}

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $productType = $row['Product_type'];
        if (array_key_exists($productType, $predefinedCategories)) {
            $categories[$productType][] = $row;
        } else {
            $categories['Other'][] = $row;
        }
    }
    $result->close();
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="homePageStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>CHAINARONG FURNITURE</title>
</head>
<body>
    <header>
        <div class="header-title">
            <h1>CHAINARONG FURNITURE</h1>
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="ค้นหาสินค้า" id="searchInput">
                <i class="fas fa-search search-icon" id="searchIcon"></i>
            </div>
        </div>
        <div class="header-icons">
            <img src="images/settings_icon.png" alt="Settings">
            <img src="images/cart_icon.png" alt="Cart">
            <img src="images/user_icon.png" alt="User">
        </div>
    </header>

    <div class="hero-banner">
        <img src="images/hero_banner.png" alt="allFunitureOnHomePage">
    </div>

    <nav class="category-nav">
        <ul>
            <li><a href="?category=all" class="active">สินค้าทั้งหมด</a></li>
            <li class="separator">|</li>
            <?php foreach ($predefinedCategories as $categoryKey => $categoryName): ?>
                <li>
                    <a href="?category=<?php echo urlencode($categoryKey); ?>">
                        <?php echo htmlspecialchars($categoryName); ?>
                    </a>
                </li>
                <?php if ($categoryKey !== array_key_last($predefinedCategories)): ?>
                    <li class="separator">|</li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </nav>

    <section class="product-grid">
        <?php foreach ($categories as $category => $products): ?>
            <?php foreach ($products as $product): ?>

                <div class="product-card" data-category="<?php echo htmlspecialchars($category); ?>">
                    <a href="createOrder.php?product_id=<?php echo urlencode($product['Product_id']); ?>">
                        <img src="<?php echo htmlspecialchars($product['Product_image']); ?>" alt="<?php echo htmlspecialchars($product['Product_name']); ?>">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['Product_name']); ?></h3>
                            <p>สี: <?php echo htmlspecialchars($product['Product_color']); ?></p>
                            <div class="product-price"><?php echo number_format($product['Product_price'], 2); ?> ฿</div>
                        </div>
                    </a>
                </div>

            <?php endforeach; ?>
        <?php endforeach; ?>
    </section>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryLinks = document.querySelectorAll('.category-nav a');
            const productCards = document.querySelectorAll('.product-card');
            const searchInput = document.getElementById('searchInput');
            const searchIcon = document.getElementById('searchIcon');

            function filterProducts(category) {
                productCards.forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            categoryLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    categoryLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                    const category = new URLSearchParams(this.href.split('?')[1]).get('category');
                    filterProducts(category);
                });
            });

            function searchProducts() {
                const query = searchInput.value.toLowerCase();
                productCards.forEach(card => {
                    const productName = card.querySelector('h3').textContent.toLowerCase();
                    if (productName.includes(query)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            searchIcon.addEventListener('click', searchProducts);
            searchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    searchProducts();
                }
            });

            // ป้องกันการคลิกซ้อนเมื่อกดที่การ์ด
            productCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // ถ้ามีการเลือกข้อความ ไม่ต้องนำทางไปยังหน้า createOrder
                    if (window.getSelection().toString()) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>