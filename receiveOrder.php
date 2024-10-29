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
    <div class="header-title">
      <img src="images/arrow_icon.png" alt="ย้อนกลับ" role="button">
      <h1>รายการที่ต้องได้รับ</h1>
    </div>
    <div class="header-icons">
      <a href="#"><img src="images/settings_icon.png" alt="Settings" class="header-icon"></a>
      <a href="#"><img src="images/cart_icon.png" alt="Cart" class="header-icon"></a>
      <a href="#"><img src="images/user_icon.png" alt="User" class="header-icon"></a>
    </div>
  </header>

  <main class="order-container">
    <div class="order-card">
      <h2>รหัสคำสั่งซื้อ P0001</h2>
      <p class="status">กำลังจัดส่งสินค้า</p>

      <div class="order-details">
        <img src="images/wooden_wardrobe.png" alt="ตู้ไม้">
        <div class="item-info">
          <h3>ตู้เสื้อผ้าไม้ประดู่ 4 ประตู</h3>
          <p>จำนวน: x2</p>
          <p>สี: มอคค่า</p>
          <p>฿1,500</p>
        </div>
        <div class="actions">
          <button class="action-button">เคลมสินค้า</button>
          <button class="action-button">ฉันได้รับสินค้าแล้ว</button>
        </div>
      </div>
      <div class="order-summary">
        <p>วันที่สั่งซื้อ 15 ส.ค.</p>
        <p class="price">สินค้ารวม 2 รายการ : ฿3,000</p>
      </div>
    </div>

    <!-- Second Order -->
    <div class="order-card">
      <h2>รหัสคำสั่งซื้อ P0002</h2>
      <p class="status">กำลังจัดส่งสินค้า</p>

      <div class="order-details">
        <img src="images/wooden_drawer.png" alt="ตู้ลิ้นชัก">
        <div class="item-info">
          <h3>ตู้ลิ้นชักไม้สัก 3 ชั้น</h3>
          <p>จำนวน: x1</p>
          <p>สี: มอคค่า</p>
          <p>฿3,500</p>
        </div>
        <div class="actions">
          <button class="action-button">เคลมสินค้า</button>
          <button class="action-button">ฉันได้รับสินค้าแล้ว</button>
        </div>
      </div>
      <div class="order-summary">
        <p>วันที่สั่งซื้อ 1 ส.ค.</p>
        <p class="price">สินค้ารวม 1 รายการ : ฿3,500</p>
      </div>
    </div>
  </main>
</body>
</html>