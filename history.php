<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ประวัติการสั่งซื้อ</title>
  <link rel="stylesheet" href="historyStyle.css"> 
</head>
<body>

  <header>
    <div class="header-content">
      <a href="#" class="back-link">←</a>
      <h1>ประวัติการสั่งซื้อ</h1>
    </div>
    <div class="header-icons">
      <a href="#"><img src="images/settings_icon.png" alt="Settings" class="header-icon"></a>
      <a href="#"><img src="images/cart_icon.png" alt="Cart" class="header-icon"></a>
      <a href="#"><img src="images/user_icon.png" alt="User" class="header-icon"></a>
    </div>
  </header>

  <main class="order-history-container">

    <div class="order-card">
      <h2>รหัสคำสั่งซื้อ P0001</h2>
      <p class="status">สำเร็จ</p>

      <div class="order-details">
        <img src="images/wooden_wardrobe.png" alt="สินค้า 1">
        <div class="item-info">
          <h3>ตู้เสื้อผ้าไม้ประดู่ 4 ประตู</h3>
          <p>จำนวน: x2</p>
          <p>สี: มอคค่า</p>
          <p class="price">฿1,500</p>
        </div>
        <div class="actions">
          <button class="action-button">สั่งซื้อสินค้าอีกครั้ง</button>
          <button class="action-button">ใบเสร็จสินค้า</button>
        </div>
      </div>

      <div class="order-summary">
        <p>วันที่สั่งซื้อ 15 ส.ค.</p>
        <p>สินค้ารวม 2 รายการ : ฿3,000</p>
      </div>
    </div>

    <div class="order-card">
      <h2>รหัสคำสั่งซื้อ P0002</h2>
      <p class="status">สำเร็จ</p>

      <div class="order-details">
        <img src="images/wooden_drawer.png" alt="สินค้า 2">
        <div class="item-info">
          <h3>ตู้ลิ้นชักไม้สัก 3 ชั้น</h3>
          <p>จำนวน: x1</p>
          <p>สี: มอคค่า</p>
          <p class="price">฿3,500</p>
        </div>
        <div class="actions">
          <button class="action-button">สั่งซื้อสินค้าอีกครั้ง</button>
          <button class="action-button">ใบเสร็จสินค้า</button>
        </div>
      </div>

      <div class="order-summary">
        <p>วันที่สั่งซื้อ 1 ส.ค.</p>
        <p>สินค้ารวม 1 รายการ : ฿3,500</p>
      </div>
    </div>

  </main>

</body>
</html>