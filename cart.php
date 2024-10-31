<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    var_dump($_POST); // ดูข้อมูลที่ถูกส่งมา
    exit; // หยุดการประมวลผล
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รถเข็น</title>
    <link rel="stylesheet" href="cartStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header>
            <div class="header-title">
                <i class="fas fa-arrow-left" onclick="window.history.back();"></i>
                <h1>รถเข็น</h1>
            </div>
            <div class="header-icons">
                <a href="homePage.php">
                    <i class="fas fa-home"></i>
                </a>
                <a href="settingUser.php">
                    <i class="fas fa-cog"></i>
                </a> 
                <a href="profileUser.php">
                    <i class="fas fa-user"></i>
                </a>
            </div>
    </header>

    <div class="cart-container">
        <?php if (!empty($_SESSION['cart'])): ?>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <div class="product">
                    <input type="checkbox" class="product-checkbox">
                    <img src="<?= htmlspecialchars($item['product_image']); ?>" 
                        alt="<?= htmlspecialchars($item['product_name']); ?>" 
                        class="product-image">
                    <div class="product-details">
                        <div class="product-title"><?= htmlspecialchars($item['product_name']); ?></div>
                        <div class="product-color"><?= htmlspecialchars($item['product_color']); ?></div>
                        <div class="product-price">
                            <span class="price-label">รวม</span>
                            <span class="price-amount" data-unit-price="<?= htmlspecialchars($item['product_price']); ?>">
                                ฿<?= number_format($item['product_price'], 2); ?>
                            </span>
                        </div>
                    </div>
                    <div class="quantity-control">
                        <button class="quantity-btn decrease">-</button>
                        <input type="text" value="<?= htmlspecialchars($item['quantity']); ?>" class="quantity-input">
                        <button class="quantity-btn increase">+</button>
                        <button class="edit-quantity-btn">แก้ไขจำนวนสินค้า</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>ไม่มีสินค้าในตะกร้า</p>
        <?php endif; ?>
    </div>

    <div class="footer-container">
        <label class="select-all-label">
            <input type="checkbox" id="select-all"> 
            <span>ทั้งหมด</span>
        </label>
        <div class="total-price">
            <span>รวมราคาทั้งหมด</span>
            <span class="price-amount">฿</span>
        </div>
        <button class="checkout-btn" onclick="orderItems()">สั่งสินค้า</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const productCheckboxes = document.querySelectorAll('.product-checkbox');
            const totalPriceElement = document.querySelector('.footer-container .price-amount');
            const decreaseButtons = document.querySelectorAll('.decrease');
            const increaseButtons = document.querySelectorAll('.increase');
            const quantityInputs = document.querySelectorAll('.quantity-input');

            function updateItemPrice(product) {
                const quantity = parseInt(product.querySelector('.quantity-input').value) || 0;
                const priceElement = product.querySelector('.price-amount');
                const unitPrice = parseFloat(priceElement.getAttribute('data-unit-price')) || 0;
                
                const total = quantity * unitPrice;
                priceElement.textContent = `฿${total.toLocaleString('th-TH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}`;
            }

            function updateTotalPrice() {
                let total = 0;
                document.querySelectorAll('.product').forEach(product => {
                    if (product.querySelector('.product-checkbox').checked) {
                        const quantity = parseInt(product.querySelector('.quantity-input').value) || 0;
                        const priceElement = product.querySelector('.price-amount');
                        const unitPrice = parseFloat(priceElement.getAttribute('data-unit-price')) || 0;
                        total += quantity * unitPrice;
                    }
                });

                totalPriceElement.textContent = `฿${total.toLocaleString('th-TH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}`;
            }

            selectAllCheckbox.addEventListener('change', function() {
                productCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateTotalPrice();
            });

            productCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = [...productCheckboxes].every(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                    updateTotalPrice();
                });
            });

            decreaseButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const product = this.closest('.product');
                    const input = product.querySelector('.quantity-input');
                    let value = parseInt(input.value);
                    if (value > 1) {
                        input.value = value - 1;
                        updateItemPrice(product);
                        updateTotalPrice();
                    }
                });
            });

            increaseButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const product = this.closest('.product');
                    const input = product.querySelector('.quantity-input');
                    let value = parseInt(input.value);
                    if (value < 200) {
                        input.value = value + 1;
                        updateItemPrice(product);
                        updateTotalPrice();
                    }
                });
            });

            quantityInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const product = this.closest('.product');
                    let value = parseInt(this.value);
                    if (value < 1) {
                        this.value = 1;
                        value = 1;
                    } else if (value > 200) {
                        this.value = 200;
                        value = 200;
                    }
                    updateItemPrice(product);
                    updateTotalPrice();
                });
            });

            document.querySelectorAll('.product').forEach(product => {
                updateItemPrice(product);
            });
            updateTotalPrice();
        });

        function orderItems() {
            const form = document.createElement("form");
            form.method = "POST";
            form.action = "myOrder.php"; // ตรวจสอบว่า URL นี้ถูกต้อง

            // เพิ่ม input สำหรับ source และ product_id
            const sourceInput = document.createElement("input");
            sourceInput.type = "hidden";
            sourceInput.name = "source";
            sourceInput.value = "cart"; // กำหนดค่าที่จะส่งไป

            form.appendChild(sourceInput);

            const productIDs = document.querySelectorAll('.product-checkbox:checked');
            productIDs.forEach((checkbox) => {
                const productID = checkbox.value; // หรือดึงค่าจาก attribute อื่น ๆ
                const productInput = document.createElement("input");
                productInput.type = "hidden";
                productInput.name = "product_id[]"; // หรือชื่อที่คุณต้องการ
                productInput.value = productID; // ค่าที่คุณต้องการส่ง

                form.appendChild(productInput);
            });

            if (productIDs.length === 0) {
                alert("กรุณาเลือกสินค้าก่อนสั่งซื้อ");
                return;
            }

            document.body.appendChild(form);
            form.submit();
        }


    </script>
</body>
</html>