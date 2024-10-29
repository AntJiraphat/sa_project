<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการที่ต้องจัดส่ง</title>
    <link rel="stylesheet" href="sentStyle.css">
</head>
<body>
    <header>
        <div class="header-title">
        <img src="images/arrow_icon.png" alt="ย้อนกลับ" role="button">
            <h1>รายการที่ต้องจัดส่ง</h1>
        </div>
        <div class="header-icons">
            <a href="#"><img src="images/settings_icon.png" alt="Settings" class="header-icon"></a>
            <a href="#"><img src="images/cart_icon.png" alt="Cart" class="header-icon"></a>
            <a href="#"><img src="images/user_icon.png" alt="User" class="header-icon"></a>
        </div>
    </header>

    <main>
        <div class="order-details">
            <div class="order-info">
                <p><strong>รหัสคำสั่งซื้อ</strong> P0002</p>
                <p><strong>วันที่สั่งสินค้า</strong> 1 ส.ค.</p>
            </div>

            <section class="delivery-info">
                <h2>ข้อมูลการจัดส่ง</h2>
                <p>ชื่อคนส่งสินค้า : ปัทมาพร กรุณา เบอร์โทร : 089-xxx-xxx</p>
            </section>

            <section class="delivery-address">
                <h2>ที่อยู่ในการจัดส่ง</h2>
                <p>อมลณัฐ ไศลแก้ว เลขที่ xx หมู่บ้าน xx ซอย xx ถนน xx ตำบล xx อำเภอ xx จังหวัด xx 10000</p>
            </section>

            <section class="order-items">
                <h2>คำสั่งซื้อของฉัน</h2>
                <div class="item">
                    <img src="images/wooden_drawer.png" alt="ตู้ลิ้นชักไม้สัก 3 ชั้น" class="item-image">
                    <div class="item-details">
                        <h3>ตู้ลิ้นชักไม้สัก</h3>
                        <p>จำนวน: x1</p>
                        <p>สี: มอคค่า</p>
                        <p class="price">฿1,500</p>
                    </div>
                </div>
                <div class="item">
                    <img src="images/wooden_wardrobe.png" alt="ตู้เสื้อผ้าไม้ประดู่ 4 ประตู" class="item-image">
                    <div class="item-details">
                        <h3>ตู้เสื้อผ้าไม้ประดู่ 4 ประตู</h3>
                        <p>จำนวน: x1</p>
                        <p>สี: คาปูชิโน่</p>
                        <p class="price">฿6,500</p>
                    </div>
                </div>
            </section>

            <div class="order-status">
                <p>สถานะ: จัดส่งสินค้า 2 ส.ค.</p>
            </div>
        </div>
    </main>
</body>
</html>