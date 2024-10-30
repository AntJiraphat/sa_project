<?php
    session_start();
    require 'database.php';
    var_dump($_POST);

    // Debug: แสดงข้อมูลที่ได้รับ
    error_log('Request Method: ' . $_SERVER['REQUEST_METHOD']);
    error_log('POST data received: ' . print_r($_POST, true));

    // รับค่าจาก POST method เท่านั้น
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_STRING);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    // ตรวจสอบค่าที่ได้รับจาก POST
    if (empty($product_id) || $quantity === false) {
        $error = "Error: Invalid product_id or quantity";
        error_log($error);
        echo $error;
        exit();
    }

    
    // ตรวจสอบช่วงของจำนวนสินค้า
    if ($quantity < 1 || $quantity > 200) {
        $error = "Error: Quantity must be between 1 and 200";
        error_log($error);
        echo $error;
        exit();
    }

    // ถ้าข้อมูลถูกต้อง ดำเนินการต่อ
    try {

        $stmt = $conn->prepare("SELECT * FROM products WHERE Product_ID = ?");
        $stmt->bind_param("s", $product_id);

            // Execute query
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if (!$product) {
            throw new Exception("Product not found");
        }
    
        // สร้างหรืออัปเดตตะกร้าสินค้า
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // เพิ่มสินค้าลงในตะกร้า
        $_SESSION['cart'][$product_id] = [
            'product_id' => $product['Product_ID'],
            'product_name' => $product['Product_name'],
            'product_price' => $product['Product_price'],
            'product_image' => $product['Product_image'],
            'product_color' => $product['Product_color'],
            'quantity' => $quantity
        ];

        // Debug
        error_log("Cart updated successfully: " . print_r($_SESSION['cart'], true));
        
        echo "Success: Product added to cart";

    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        echo "Error: " . $e->getMessage();;
    }
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รถเข็น</title>
    <link rel="stylesheet" href="cartStyle.css"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header>
        <div class="header-title">
            <img src="images/arrow_icon.png" alt="Back" onclick="window.history.back();">
            <h1>รถเข็น</h1>
        </div>
        <div class="header-icons">
            <img src="images/settings_icon.png" alt="Settings">
            <img src="images/cart_icon.png" alt="Cart">
            <img src="images/user_icon.png" alt="User">
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
                        <div class="product-price">฿<?= number_format($item['product_price'], 2); ?></div>
                    </div>
                    <div class="quantity-control">
                        <button class="quantity-btn decrease">-</button>
                        <input type="text" value="<?= htmlspecialchars($item['quantity']); ?>" class="quantity-input">
                        <button class="quantity-btn increase">+</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>ไม่มีสินค้าในตะกร้า</p>
        <?php endif; ?>
    </div>

    <div class="bottom-container">
        <label><input type="checkbox" id="select-all"> ทั้งหมด</label>
        <div class="product-price">รวม ฿0</div>
        <button class="checkout-btn">สั่งสินค้า</button>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        const totalPriceElement = document.querySelector('.bottom-container .product-price');
        const decreaseButtons = document.querySelectorAll('.decrease');
        const increaseButtons = document.querySelectorAll('.increase');
        const checkoutButton = document.querySelector('.checkout-btn');

        // ฟังก์ชันคำนวณราคารวม
        function updateTotalPrice() {
            let total = 0;
            document.querySelectorAll('.product').forEach(product => {
                const checkbox = product.querySelector('.product-checkbox');
                if (checkbox.checked) {
                    const priceText = product.querySelector('.product-price').textContent;
                    // แปลงข้อความราคาจาก "฿1,234.56" เป็นตัวเลข
                    const price = parseFloat(priceText.replace('฿', '').replace(',', ''));
                    const quantity = parseInt(product.querySelector('.quantity-input').value);
                    total += price * quantity;
                }
            });
            // แสดงราคารวมในรูปแบบสกุลเงินไทย
            totalPriceElement.textContent = `รวม ฿${total.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })}`;
        }

        // Event Listener สำหรับ checkbox เลือกทั้งหมด
        selectAllCheckbox.addEventListener('change', function() {
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateTotalPrice();
        });

        // Event Listener สำหรับแต่ละ checkbox
        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // ตรวจสอบว่า checkbox ทั้งหมดถูกเลือกหรือไม่
                const allChecked = Array.from(productCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                updateTotalPrice();
            });
        });

        // Event Listener สำหรับปุ่มเพิ่มจำนวน
        increaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.quantity-input');
                const productId = this.closest('.product').dataset.productId; // ใช้ data attribute เพื่อเก็บ product ID
                input.value = parseInt(input.value) + 1;
                
                // ส่ง AJAX request เพื่ออัปเดต session
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "updateCart.php", true); // คุณอาจต้องสร้างไฟล์ updateCart.php
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send(`product_id=${productId}&quantity=${input.value}`);
                
                if (this.closest('.product').querySelector('.product-checkbox').checked) {
                    updateTotalPrice();
                }
            });
        });


        // Event Listener สำหรับปุ่มลดจำนวน
        decreaseButtons.forEach(button => {
            button.addEventListener('click', async function() {
                const input = this.parentElement.querySelector('.quantity-input');
                const currentValue = parseInt(input.value);
                
                if (currentValue > 1) {
                    input.value = currentValue - 1;
                    if (this.closest('.product').querySelector('.product-checkbox').checked) {
                        updateTotalPrice();
                    }
                } else {
                    const result = await Swal.fire({
                        title: 'คุณแน่ใจว่าต้องการลบสินค้านี้หรือไม่?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'ยืนยัน',
                        cancelButtonText: 'ยกเลิก'
                    });

                    if (result.isConfirmed) {
                        const productElement = this.closest('.product');
                        // ลบสินค้าจาก session ด้วย AJAX
                        const productId = productElement.querySelector('.quantity-input').dataset.productId; // ใช้ data attribute เพื่อเก็บ product ID
                        // ส่ง AJAX request เพื่ออัปเดต session
                        const xhr = new XMLHttpRequest();
                        xhr.open("POST", "removeFromCart.php", true); // คุณอาจต้องสร้างไฟล์ removeFromCart.php
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.send(`product_id=${productId}`);
                        
                        // ลบสินค้าออกจาก DOM
                        productElement.remove();
                        updateTotalPrice();
                    }
                }
            });
        });

        // Event Listener สำหรับการแก้ไขจำนวนโดยตรง
        const quantityInputs = document.querySelectorAll('.quantity-input');
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                let value = parseInt(this.value) || 1;
                if (value < 1) value = 1;
                this.value = value;
                
                if (this.closest('.product').querySelector('.product-checkbox').checked) {
                    updateTotalPrice();
                }
            });
        });

        // Event Listener สำหรับปุ่มสั่งสินค้า
        checkoutButton.addEventListener('click', function() {
            if (validateOrder()) {
                Swal.fire({
                    title: 'ยืนยันการสั่งซื้อ',
                    text: 'คำสั่งซื้อของคุณได้รับการยืนยันเรียบร้อยแล้ว',
                    icon: 'success',
                    confirmButtonText: 'ตกลง'
                });
            }
        });

        // เรียกใช้ฟังก์ชันคำนวณราคาครั้งแรก
        updateTotalPrice();
    });
    </script>
</body>
</html>