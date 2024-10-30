<?php
// Connect to the database
require 'database.php';

// Debug: แสดงค่าที่ได้จาก URL
echo "<!-- Debug: Received product_id from URL = " . (isset($_GET['product_id']) ? $_GET['product_id'] : 'not set') . " -->";

// Get the product_id from the URL
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : '';

if ($product_id === '') {
    // Debug: แจ้งว่าไม่มี product_id
    echo "<!-- Debug: No product_id provided -->";
    header('Location: homePage.php');
    exit();
}

// Debug: แสดงค่า SQL ที่จะ query
echo "<!-- Debug: SQL Query = SELECT * FROM products WHERE Product_ID = " . $product_id . " -->";

$sql = "SELECT * FROM products WHERE Product_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// Debug: แสดงข้อมูลที่ได้จาก query
echo "<!-- Debug: Query result = ";
print_r($product);
echo " -->";
echo "<!-- Debug: Product_ID = " . (isset($product['Product_ID']) ? $product['Product_ID'] : 'not set') . " -->";


// ถ้าไม่พบข้อมูลสินค้า
if (!$product) {
    // Debug: แจ้งว่าไม่พบข้อมูล
    echo "<!-- Debug: No product found with ID = " . $product_id . " -->";
    header('Location: homePage.php');
    exit();
}
?>

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
        <div class="header-title">
            <img src="images/arrow_icon.png" alt="ย้อนกลับ" role="button" onclick="window.history.back();">
            <h1>รายละเอียดสินค้า</h1>
        </div>
        <div class="header-icons">
            <a href="#"><img src="images/settings_icon.png" alt="Settings" class="header-icon"></a>
            <a href="#"><img src="images/cart_icon.png" alt="Cart" class="header-icon"></a>
            <a href="#"><img src="images/user_icon.png" alt="User" class="header-icon"></a>
        </div>
    </header>

    <main class="product-container">
        <div class="product-info">
            <img src="<?= htmlspecialchars($product['Product_image']); ?>" alt="<?= htmlspecialchars($product['Product_name']); ?>" class="product-image">
            <div class="product-card">
                <h2 class="Product_name"><?= htmlspecialchars($product['Product_name']); ?></h2>
                <p class="Product_detail"><?= htmlspecialchars($product['Product_detail']); ?></p>
                <div class="product-specs">
                    <p><strong>ประเภท:</strong> <span class="Product_type"><?= htmlspecialchars($product['Product_type']); ?></span></p>
                    <p><strong>ขนาดสินค้า:</strong> <span class="Product_size"><?= htmlspecialchars($product['Product_size']); ?></span></p>
                    <p><strong>สี:</strong> <span class="Product_color"><?= htmlspecialchars($product['Product_color']); ?></span></p>
                </div>
                <p><strong>ราคา:</strong> <span class="Product_price"><?= number_format($product['Product_price'], 2); ?> ฿</span></p>
                <div class="quantity">
                    <label for="quantity">จำนวนสินค้า:</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1">
                </div>
                <p id="quantity-message" style="margin-top: 5px; display: none;"></p>
            </div>
        </div>
    </main>

    <div class="bottom-container">
        
        <div class="button-group">
        <button type="button" class="add-to-cart" id="addToCartButton">เพิ่มไปยังรายการ</button>

        <form action="myOrder.php" method="POST" id="orderForm">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['Product_ID']); ?>">
            <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['Product_name']); ?>">
            <input type="hidden" name="product_color" value="<?= htmlspecialchars($product['Product_color']); ?>">
            <input type="hidden" name="product_price" value="<?= htmlspecialchars($product['Product_price']); ?>">
            <input type="hidden" name="product_image" value="<?= htmlspecialchars($product['Product_image']); ?>">
            <input type="hidden" name="product_type" value="<?= htmlspecialchars($product['Product_type']); ?>">
            <input type="hidden" name="product_size" value="<?= htmlspecialchars($product['Product_size']); ?>">
            <input type="hidden" name="quantity" id="form-quantity" value="1">
            <button type="submit" class="order-btn" id="orderButton">สั่งสินค้า</button>
        </form>
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
                
                if (quantity < 20) {
                    // น้อยกว่า 20 ชิ้น: กดได้แค่ add to cart
                    orderButton.disabled = true;
                    addToCartButton.disabled = false;
                    quantityMessage.style.display = 'block';
                    quantityMessage.textContent = 'จำนวนสินค้าน้อยกว่า 20 ชิ้น กรุณาเพิ่มสินค้าลงตะกร้า';
                } 
                else if (quantity >= 20 && quantity <= 200) {
                    // 20-200 ชิ้น: กดได้ทั้งสองปุ่ม
                    orderButton.disabled = false;
                    orderButton.classList.add('active');
                    addToCartButton.disabled = false;
                    quantityMessage.style.display = 'none';
                } 
                else if (quantity > 200) {
                    // มากกว่า 200 ชิ้น
                    orderButton.disabled = true;
                    addToCartButton.disabled = true;
                    alert("ขออภัย สินค้านี้กดสั่งได้สูงสุด 200 ชิ้น");
                    quantityInput.value = 200;
                    quantityMessage.style.display = 'block';
                    quantityMessage.textContent = 'ขออภัย จำนวนสินค้าต้องไม่เกิน 200 ชิ้น';
                }

                // Update hidden form quantity
                formQuantity.value = quantity;
                console.log("Quantity:", quantity);
                console.log("Order Button Disabled:", orderButton.disabled);

            }

            // Event listener สำหรับปุ่ม "เพิ่มไปยังรายการ"
            addToCartButton.addEventListener('click', function() {
                const quantity = document.getElementById('quantity').value;
                const productId = '<?php echo htmlspecialchars($product["Product_ID"]); ?>';

                // สร้าง FormData object
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('quantity', quantity);

                if (quantity <= 200) {
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
                        // เปิดการใช้งานปุ่มอีกครั้งหลังจากได้รับการตอบกลับ
                        addToCartButton.disabled = false;
                    });
                } else {
                    alert("จำนวนสินค้าต้องไม่เกิน 200 ชิ้น");
                }
            });

            // Event listener สำหรับ form submit (ปุ่ม "สั่งสินค้า")
            orderForm.addEventListener('submit', function(e) {
                const quantity = parseInt(quantityInput.value);
                // alert('Quantity: ' + quantity);
                if (quantity < 20 || quantity > 200) {
                    e.preventDefault();
                    alert('จำนวนสินค้าต้องอยู่ระหว่าง 20-200 ชิ้น');
                    return;
                }
                // อัพเดทค่า quantity ใน form ก่อน submit
                formQuantity.value = quantity;
            });

            // Input event listeners
            quantityInput.addEventListener('input', updateButtons);
            quantityInput.addEventListener('change', updateButtons);

            // เรียกใช้ฟังก์ชัน updateButtons ครั้งแรก
            updateButtons();
        });
    </script>
</body>
</html>
