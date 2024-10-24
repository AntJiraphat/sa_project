<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการที่ต้องได้รับ</title>
    <link rel="stylesheet" href="receiveOrderStyle.css">
</head>
<body>
<header>
        <div class="header-content">
            <a href="#" class="back-link">←</a>
            <h1>รายการที่ต้องได้รับ</h1>
        </div>
        <div class="header-icons">
            <a href="#"><img src="images/settings_icon.png" alt="Settings" class="header-icon"></a>
            <a href="#"><img src="images/cart_icon.png" alt="Cart" class="header-icon"></a>
            <a href="#"><img src="images/user_icon.png" alt="User" class="header-icon"></a>
        </div>
    </header>

    <main class="order-container">
        <!-- First Order -->
        <div class="order-card">
            <div class="order-content">
                <div class="product-image">
                    <img src="images/wooden_wardrobe.png" alt="ตู้ไม้">
                    <span class="image-count">+2</span>
                </div>
                <div class="order-details">
                    <h2 class="order-id">รหัสคำสั่งซื้อ P0001</h2>
                    <div class="price-section">
                        <span class="price-label">ราคา</span>
                        <span class="price-amount">฿3,000</span>
                    </div>
                    <div class="order-date">
                        <span class="date">วันที่สั่งซื้อ 15 ส.ค.</span>
                        <span class="item-count">สินค้ารวม 2 รายการ</span>
                    </div>
                </div>
                <div class="order-actions">
                    <button class="action-button track">เคลมสินค้า</button>
                    <button class="action-button received">ฉันได้รับสินค้าแล้ว</button>
                </div>
            </div>
        </div>

        <!-- Second Order -->
        <div class="order-card">
            <div class="order-content">
                <div class="product-image">
                    <img src="images/wooden_drawer.png" alt="ตู้ลิ้นชัก">
                </div>
                <div class="order-details">
                    <h2 class="order-id">รหัสคำสั่งซื้อ P0002</h2>
                    <div class="price-section">
                        <span class="price-label">ราคา</span>
                        <span class="price-amount">฿3,500</span>
                    </div>
                    <div class="order-date">
                        <span class="date">วันที่สั่งซื้อ 1 ส.ค.</span>
                        <span class="item-count">สินค้ารวม 1 รายการ</span>
                    </div>
                </div>
                <div class="order-actions">
                    <button class="action-button track">เคลมสินค้า</button>
                    <button class="action-button received">ฉันได้รับสินค้าแล้ว</button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>