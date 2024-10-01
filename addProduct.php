<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้า</title>
    <link rel="stylesheet" href="addProductStyle.css">
</head>
<body>

<div class="container">
    <div class="header">
        <h1>เพิ่มสินค้า - CHAINARONG FURNITURE</h1>
    </div>
    <form action="add_product.php" method="POST">
        <div class="form-group">
            <label for="product_name">ชื่อสินค้า</label>
            <input type="text" id="product_name" name="product_name" required>
        </div>

        <div class="form-group">
            <label for="product_description">รายละเอียดสินค้า</label>
            <textarea id="product_description" name="product_description" rows="4" required></textarea>
        </div>

        <div class="form-group dimensions">
            <div>
                <label for="product_height">ความสูง (CM)</label>
                <input type="number" id="product_height" name="product_height" step="0.01" required>
            </div>
            <div>
                <label for="product_length">ความยาว (CM)</label>
                <input type="number" id="product_length" name="product_length" step="0.01" required>
            </div>
        </div>

        <div class="form-group">
            <label for="product_color">สี</label>
            <select id="product_color" name="product_color">
                <option value="คาปูชิโน">คาปูชิโน</option>
                <option value="ขาว">ขาว</option>
                <option value="ดำ">ดำ</option>
                <option value="น้ำตาล">น้ำตาล</option>
            </select>
        </div>

        <div class="form-group">
            <label for="product_type">ประเภท</label>
            <select id="product_type" name="product_type">
                <option value="ตู้เสื้อผ้า">ตู้เสื้อผ้า</option>
                <option value="โต๊ะ">โต๊ะ</option>
                <option value="เตียง">เตียง</option>
                <option value="โซฟา">โซฟา</option>
            </select>
        </div>

        <div class="form-group">
            <label for="product_price">ราคา (฿)</label>
            <input type="number" id="product_price" name="product_price" required>
        </div>

        <button type="submit" class="btn-submit">เพิ่มสินค้า</button>
    </form>
</div>

</body>
</html>
