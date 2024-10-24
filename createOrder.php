<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสินค้า</title>
    <link rel="stylesheet" href="createOrderStyle.css"> 
</head>
<body>
    <header>
        <div class="header-content">
            <a href="#" class="back-link">←</a>
            <h1>รายละเอียดสินค้า</h1>
        </div>
        <div class="header-icons">
            <a href="#"><img src="images/settings_icon.png" alt="Settings" class="header-icon"></a>
            <a href="#"><img src="images/cart_icon.png" alt="Cart" class="header-icon"></a>
            <a href="#"><img src="images/user_icon.png" alt="User" class="header-icon"></a>
        </div>
    </header>

    <main class="product-detail-container">
        <div class="product-info">
            <img src="images/wooden_wardrobe.png" alt="ตู้เสื้อผ้าไม้ประดู่ 4 ประตู" class="product-image">

            <div class="product-text">
                <h2>ตู้เสื้อผ้าไม้ประดู่ 4 ประตู</h2>
                <p>ตู้เสื้อผ้าทำจากไม้ประดู่ มี 4 ประตู ทำจากไม้ประดู่ มีให้เลือก 5 สี โซลิค, หินอ่อน, คาปูชิโน่, แอชบราวน์, มอคค่า แขวนได้ทั้งเสื้อผ้าตัวยาว ตัวสั้น และเสื้อผ้าที่พับเก็บ ชั้นวางปรับระดับได้ตามต้องการ เพื่อประโยชน์การใช้งานสูงสุด</p>

                <div class="product-specs">
                    <p><strong>ประเภท:</strong> ตู้เสื้อผ้า</p>
                    <p><strong>ขนาดสินค้า:</strong> 90 × 150 × 180 cm.</p>
                    <p><strong>สี:</strong> 
                        <select>
                            <option value="คาปูชิโน่">คาปูชิโน่</option>
                            <option value="มอคค่า">มอคค่า</option>
                            <option value="หินอ่อน">หินอ่อน</option>
                            <option value="แอชบราวน์">แอชบราวน์</option>
                            
                            </select>
                    </p>
                </div>
                
                <div class="quantity">
                    <label for="quantity">จำนวนสินค้า:</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1">
                </div>
            </div>
        </div>

        <div class="bottom-container">
            <button class="order-btn">สั่งสินค้า</button>
        </div>
    </main>
</body>
</html>