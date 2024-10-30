<?php 
error_reporting(E_ALL); // เปิด error reporting เพื่อดู error ทั้งหมด 
ini_set('display_errors', 1);

require 'database.php';

// ประกาศตัวแปร $predefinedCategories ก่อนใช้งาน
$predefinedCategories = [
    'Bed' => 'เตียง',
    'Cabinet' => 'ตู้เก็บของ',
    'Vanity' => 'โต๊ะเครื่องแป้ง',
    'Wardrobe' => 'ตู้เสื้อผ้า'
];

// ตรวจสอบการเชื่อมต่อ 
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Debug: ดูข้อมูลที่ได้จาก query
if ($result->num_rows > 0) {
    $firstRow = $result->fetch_assoc();
    echo "<!-- Debug: First row data: " . print_r($firstRow, true) . " -->";
    $result->data_seek(0); // reset pointer กลับไปที่แถวแรก
}

// หลังจาก query
echo "<!-- Debug: Query result: -->";
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "<!-- Product row: ";
        print_r($row);
        echo " -->";
    }
    // Reset pointer
    $result->data_seek(0);
}

// เก็บข้อมูลในตัวแปร categories
$categories = [];
while ($row = $result->fetch_assoc()) {
    $productType = $row['Product_type'];
    // Debug: พิมพ์ข้อมูลแต่ละแถว
    echo "<!-- Debug: Row ID = " . $row['Product_ID'] . " -->"; // แก้เป็น Product_ID
    
    if (array_key_exists($productType, $predefinedCategories)) {
        $categories[$productType][] = $row;
    } else {
        $categories['Other'][] = $row;
    }
}
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
        <?php
        // Debug: แสดงจำนวน categories
        echo "<!-- Debug: Categories count: " . count($categories) . " -->"; 
        
        foreach ($categories as $category => $products):
            echo "<!-- Debug: Products in $category: " . count($products) . " -->";

            foreach ($products as $product):
                // Debug
                echo "<!-- Product data in loop: ";
                print_r($product);
                echo " -->";
                
                if (isset($product['Product_ID'])) {
                    $productUrl = 'createOrder.php?product_id=' . htmlspecialchars($product['Product_ID']);
                    ?>
                    <a href="<?php echo $productUrl; ?>" 
                    class="product-card" 
                    data-category="<?php echo htmlspecialchars($category); ?>">
                        <img src="<?php echo htmlspecialchars($product['Product_image']); ?>"
                            alt="<?php echo htmlspecialchars($product['Product_name']); ?>">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['Product_name']); ?></h3>
                            <p>สี: <?php echo htmlspecialchars($product['Product_color']); ?></p>
                            <div class="product-price">
                                <?php echo number_format($product['Product_price'], 2); ?> ฿
                            </div>
                        </div>
                    </a>
                    <?php
                } else {
                    echo "<!-- Missing Product_ID for product: ";
                    print_r($product);
                    echo " -->";
                }
                ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryLinks = document.querySelectorAll('.category-nav a');
            const productCards = document.querySelectorAll('.product-card');
            const searchInput = document.getElementById('searchInput');
            const searchIcon = document.getElementById('searchIcon');

            console.log('Total product cards:', productCards.length); // Debug: จำนวน cards ทั้งหมด

            productCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // ไม่ต้อง prevent default เพราะเราต้องการให้ link ทำงาน
                    console.log('Card clicked, navigating to:', this.href);
                });
            });
            
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
        });
    </script>
</body>
</html>