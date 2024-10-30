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
            const quantityInput = document.getElementById('quantity');
            const orderButton = document.getElementById('orderButton');
            const addToCartButton = document.getElementById('addToCartButton');
            const quantityMessage = document.getElementById('quantity-message');
            const formQuantity = document.getElementById('form-quantity');
            const orderForm = document.getElementById('orderForm');

            function updateButtons() {
                const quantity = parseInt(quantityInput.value);
                
                // Reset states
                orderButton.disabled = false;
                addToCartButton.disabled = false;
                quantityMessage.style.display = 'none';
                orderButton.classList.remove('active');
                
                if (quantity <= 0) {
                    // จำนวน 0 หรือติดลบ: ปิดปุ่มทั้งหมด
                    orderButton.disabled = true;
                    addToCartButton.disabled = true;
                    quantityMessage.style.display = 'block';
                    quantityMessage.textContent = 'กรุณากรอกจำนวนสินค้ามากกว่า 0';
                }
                else if (quantity < 20) {
                    // น้อยกว่า 20 ชิ้น: กดได้แค่ add to cart
                    orderButton.disabled = true;
                    addToCartButton.disabled = false;
                    quantityMessage.style.display = 'block';
                    quantityMessage.textContent = 'กรุณากรอกจำนวนสินค้าขั้นต่ำ 20 ชิ้น หากต้องการกดสั่งสินค้า';
                } 
                else if (quantity >= 20 && quantity <= 200) {
                    // 20-200 ชิ้น: กดได้ทั้งสองปุ่ม
                    orderButton.disabled = false;
                    addToCartButton.disabled = false;
                    orderButton.classList.add('active');
                    quantityMessage.style.display = 'none';
                } 
                else if (quantity > 200) {
                    // มากกว่า 200 ชิ้น
                    orderButton.disabled = true;
                    addToCartButton.disabled = true;
                    alert("ขออภัย สินค้านี้สั่งได้สูงสุด 200 ชิ้น");
                    quantityInput.value = 200;
                    quantityMessage.style.display = 'block';
                    quantityMessage.textContent = 'ขออภัย จำนวนสินค้าต้องไม่เกิน 200 ชิ้น';
                }

                // Update hidden form quantity
                formQuantity.value = quantity;
            }

            // Event listener สำหรับปุ่ม "เพิ่มไปยังรายการ"
            addToCartButton.addEventListener('click', function() {
                const quantity = parseInt(document.getElementById('quantity').value);
                const productId = '<?php echo htmlspecialchars($product["Product_ID"]); ?>';

                if (quantity > 0 && quantity <= 200) {
                    // ปิดการใช้งานปุ่มชั่วคราว
                    addToCartButton.disabled = true;

                    // สร้าง FormData object
                    const data = new URLSearchParams();
                    data.append('product_id', productId);
                    data.append('quantity', quantity.toString());

                    fetch('cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: data.toString()
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log('Response:', data);
                        if (!data.includes('Error')) {
                            alert("เพิ่มสินค้านี้ลงในรถเข็นของคุณแล้ว");
                            if (confirm("ต้องการไปที่หน้ารถเข็นของคุณหรือไม่?")) {
                                window.location.href = 'cart.php';
                            } else {
                                window.location.href = 'homePage.php';
                            }
                        } else {
                            throw new Error(data);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("เกิดข้อผิดพลาดในการเพิ่มสินค้า: " + error);
                    })
                    .finally(() => {
                        addToCartButton.disabled = false;
                    });
                } else if (quantity <= 0) {
                    alert("กรุณากรอกจำนวนสินค้ามากกว่า 0");
                } else {
                    alert("จำนวนสินค้าต้องไม่เกิน 200 ชิ้น");
                }
            });

            // Event listener สำหรับ form submit (ปุ่ม "สั่งสินค้า")
            orderForm.addEventListener('submit', function(e) {
                const quantity = parseInt(quantityInput.value);
                if (quantity < 20 || quantity > 200) {
                    e.preventDefault();
                    alert('จำนวนสินค้าต้องอยู่ระหว่าง 20-200 ชิ้น สำหรับการสั่งสินค้า');
                    return;
                }
                formQuantity.value = quantity;
            });

            // Input event listeners
            quantityInput.addEventListener('input', updateButtons);
            quantityInput.addEventListener('change', updateButtons);

            // Initial update
            updateButtons();
        });
    </script>
</body>
</html>