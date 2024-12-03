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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <header>
        <div class="header-title">
            <i class="fas fa-arrow-left" onclick="window.history.back();"></i>
            <h1>รายละเอียดสินค้า</h1>
        </div>
        <div class="header-icons">
            <a href="homePage.php">
                <i class="fas fa-home"></i>
            </a>
            <a href="settingUser.php">
                <i class="fas fa-cog"></i>
            </a> 
            <a href="cart.php">
                <i class="fas fa-shopping-cart"></i>
            </a>
            <a href="profileUser.php">
                <i class="fas fa-user"></i>
            </a>
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
                <p><strong>ราคาต่อชิ้น:</strong> <span class="Product_price"><?= number_format($product['Product_price'], 2); ?> ฿</span></p>
                <div class="quantity">
                    <label for="quantity">จำนวนสินค้า:</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1">
                </div>
                <p id="quantity-message" style="margin-top: 5px; display: none;"></p>
                <div class="total-price">
                    <p>ราคารวม: <span id="total">฿<?= number_format($product['Product_price'], 2) ?></span></p>
                </div>
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
            <button type="submit" class="order-btn" id="orderButton" disabled>สั่งสินค้า</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInput = document.getElementById('quantity');
            const addToCartButton = document.getElementById('addToCartButton');
            const orderButton = document.querySelector('.order-btn');

            function updateButtons() {
                const quantity = parseInt(quantityInput.value);
                const pricePerUnit = <?= $product['Product_price'] ?>;

                console.log('Current quantity:', quantity); // เพิ่ม log

                if (quantity <= 0) {
                    quantityInput.value = 1;
                    updateButtons();
                    return;
                }

                // Update order button state
                if (quantity >= 20 && quantity <= 200) {
                    console.log('Enabling order button'); // เพิ่ม log
                    orderButton.classList.add('active');
                    orderButton.disabled = false;
                } else {
                    console.log('Disabling order button'); // เพิ่ม log
                    orderButton.classList.remove('active');
                    orderButton.disabled = true;
                }

                const totalPrice = quantity * pricePerUnit;
                document.getElementById('total').textContent = '฿' + totalPrice.toLocaleString('th-TH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
            
            quantityInput.addEventListener('input', updateButtons);
            quantityInput.addEventListener('change', updateButtons);
            quantityInput.addEventListener('keyup', updateButtons);

            // Event listener สำหรับปุ่ม "เพิ่มไปยังรายการ"
            addToCartButton.addEventListener('click', function() {
                const quantity = document.getElementById('quantity').value;
                const productId = '<?php echo htmlspecialchars($product["Product_ID"]); ?>';

                if (quantity <= 200) {
                    // ปิดการใช้งานปุ่มชั่วคราว
                    addToCartButton.disabled = true;

                    // สร้าง FormData object
                    const formData = new FormData();
                    formData.append('product_id', productId);
                    formData.append('quantity', quantity.toString());
                    formData.append('product_name', '<?php echo htmlspecialchars($product["Product_name"]); ?>');
                    formData.append('product_color', '<?php echo htmlspecialchars($product["Product_color"]); ?>');
                    formData.append('product_price', '<?php echo htmlspecialchars($product["Product_price"]); ?>');
                    formData.append('product_image', '<?php echo htmlspecialchars($product["Product_image"]); ?>');
                    formData.append('product_type', '<?php echo htmlspecialchars($product["Product_type"]); ?>');
                    formData.append('product_size', '<?php echo htmlspecialchars($product["Product_size"]); ?>');

                    fetch('cart.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {

                        if (!data.includes('Error')) {
                            alert("เพิ่มสินค้านี้ลงในรถเข็นของคุณแล้ว");
                            if (confirm("ต้องการไปที่หน้ารถเข็นของคุณหรือไม่?")) {
                                // ส่งข้อมูลไปยัง cart.php และเปิดหน้า cart.php
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

            // Event listener for the order button
            orderButton.addEventListener('click', function(event) {
                    const quantity = parseInt(quantityInput.value);
                    if (quantity < 20 || quantity > 200) {
                        event.preventDefault();
                        alert("จำนวนสินค้าต้องอยู่ระหว่าง 20 ถึง 200 ชิ้น");
                        return;
                    }
                    document.getElementById('form-quantity').value = quantity;
                });

                // Initial call to set button states
                updateButtons();
            });
    </script>
</body>
</html>
