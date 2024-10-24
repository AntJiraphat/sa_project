<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รถเข็น</title>
    <link rel="stylesheet" href="cartStyle.css"> 
</head>
<body>
    <header>
        <div class="header-title">
            <img src="images/arrow_icon.png" alt="Back">
            <h1>รถเข็น</h1>
        </div>
        <div class="header-icons">
            <img src="images/settings_icon.png" alt="Settings">
            <img src="images/cart_icon.png" alt="Cart">
            <img src="images/user_icon.png" alt="User">
        </div>
    </header>

    <!-- Full-screen cart container -->
    <div class="cart-container">
        <!-- First Product -->
        <div class="product">
            <input type="checkbox">
            <img src="images/wooden_wardrobe.png" alt="ตู้เสื้อผ้าไม้ประตู 4 ประตู" class="product-image">
            <div class="product-details">
                <div class="product-title">ตู้เสื้อผ้าไม้ประดู่ 4 ประตู</div>
                <select class="product-color">
                    <option>สีมอคค่า</option>
                    <option>สีคาปูชิโน่</option>
                    <option>สี Ash brown</option>
                    <option>สี Solic</option>
                </select>
                <div class="product-price">฿6500</div>
            </div>
            <div class="quantity-control">
                <button class="quantity-btn">-</button>
                <input type="text" value="1" class="quantity-input">
                <button class="quantity-btn">+</button>
            </div>
        </div>

        <!-- Second Product -->
        <div class="product">
            <input type="checkbox">
            <img src="images/wooden_drawer.png" alt="ตู้ลิ้นชักไม้สัก" class="product-image">
            <div class="product-details">
                <div class="product-title">ตู้ลิ้นชักไม้สัก</div>
                <select class="product-color">
                <option>สีมอคค่า</option>
                    <option>สีคาปูชิโน่</option>
                    <option>สี Ash brown</option>
                    <option>สี Solic</option>
                </select>
                <div class="product-price">฿3500</div>
            </div>
            <div class="quantity-control">
                <button class="quantity-btn">-</button>
                <input type="text" value="1" class="quantity-input">
                <button class="quantity-btn">+</button>
            </div>
        </div>

        <!-- Total and Checkout -->
        <div class="bottom-container">
            <label><input type="checkbox"> ทั้งหมด</label>
            <div>รวม ฿0</div>
            <button class="checkout-btn">สั่งสินค้า</button>
        </div>
    </div>
</body>
</html>
