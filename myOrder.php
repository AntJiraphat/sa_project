<!DOCTYPE html>
<?php
require 'database.php'; 
session_start(); 

function generateOrderID($conn) {
    // ฟังก์ชันสำหรับหา ID ถัดไปที่ว่าง
    function getNextAvailableID($conn, $startID) {
        $currentID = $startID;
        do {
            // เช็คว่า ID นี้มีอยู่แล้วหรือไม่
            $sql = "SELECT Order_ID FROM orders WHERE Order_ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $currentID);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // ถ้าไม่มี ID นี้ในระบบ ให้ใช้ ID นี้ได้
            if ($result->num_rows == 0) {
                return $currentID;
            }
            
            // ถ้ามีแล้ว ให้เพิ่มเลขต่อไป
            $num = intval(substr($currentID, 2));
            $num++;
            $currentID = 'OD' . str_pad($num, 8, '0', STR_PAD_LEFT);
            
            $stmt->close();
        } while(true);
    }

    // หา Order ID ล่าสุด
    $sql = "SELECT Order_ID FROM orders ORDER BY Order_ID DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastID = intval(substr($row['Order_ID'], 2));
        $nextID = 'OD' . str_pad($lastID + 1, 8, '0', STR_PAD_LEFT);
    } else {
        $nextID = 'OD00000001';
    }

    // หา ID ที่ว่างถัดไป
    return getNextAvailableID($conn, $nextID);
}

// รับค่าจาก POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source = $_POST['source'] ?? null;
    $product_id = $_POST['product_id'] ?? null;
    $productName = $_POST['product_name'] ?? null;
    $productColor = $_POST['product_color'] ?? null;
    $productPrice = $_POST['product_price'] ?? null;
    $productImage = $_POST['product_image'] ?? null;
    $productType = $_POST['product_type'] ?? null;
    $productSize = $_POST['product_size'] ?? null;
    $quantity = $_POST['quantity'] ?? 1;
    $totalPrice = $productPrice * $quantity;
}

?>

<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการคำสั่งซื้อของฉัน</title>
    <link rel="stylesheet" href="myOrderStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <header>
        <div class="header-title">
            <i class="fas fa-arrow-left" onclick="window.history.back();"></i>
            <h1>รายการคำสั่งซื้อของฉัน</h1>
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

    <main class="order-container">
        <h2>คำสั่งซื้อของฉัน</h2>
        <?php if (!empty($productName)): ?>
            <div class="order-card" id="order-<?= htmlspecialchars($product_id); ?>">
                <img src="<?= htmlspecialchars($productImage); ?>" 
                    alt="<?= htmlspecialchars($productName); ?>">
                <div class="item-info">
                    <h3><?= htmlspecialchars($productName); ?></h3>
                    <p>ประเภท: <?= htmlspecialchars($productType); ?></p>
                    <p>ขนาด: <?= htmlspecialchars($productSize); ?></p>
                    <p>สี: <?= htmlspecialchars($productColor); ?></p>
                    <p>จำนวน: <?= htmlspecialchars($quantity); ?> ชิ้น</p>
                    <p class="price">฿<?= number_format($productPrice, 2); ?> / ชิ้น</p>
                    <p class="total-price">รวม: ฿<?= number_format($totalPrice, 2); ?></p>
                </div>
                <button class="delete-btn" onclick="removeItem('order-<?= htmlspecialchars($product_id); ?>')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        <?php endif; ?>
    </main>

    <div class="footer-container">
    <div id="total-display">รวมทั้งหมด: ฿<?= number_format($totalPrice ?? 0, 2); ?></div>
        <form action="sent.php" method="POST" id="orderForm">
            <?php $order_id = generateOrderID($conn); ?>
            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id); ?>">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_ID); ?>">
            <input type="hidden" name="product_name" value="<?= htmlspecialchars($productName); ?>">
            <input type="hidden" name="product_color" value="<?= htmlspecialchars($productColor); ?>">
            <input type="hidden" name="product_price" value="<?= htmlspecialchars($productPrice); ?>">
            <input type="hidden" name="quantity" value="<?= htmlspecialchars($quantity); ?>">
            <input type="hidden" name="total_price" value="<?= htmlspecialchars($totalPrice); ?>">
            <input type="hidden" name="order_date" value="<?= date('Y-m-d H:i:s'); ?>">
            <button type="submit" class="order-button" onclick="return confirmOrder();">สั่งผลิตสินค้า</button>
        </form>
    </div>

    <script>

        // Function to parse price string to number
        function parsePrice(priceString) {
            // ลบสัญลักษณ์เงินบาท คอมม่า และช่องว่าง แล้วแปลงเป็นตัวเลข
            return parseFloat(priceString.replace(/[รวม:\s฿,]/g, '')) || 0;
        }

        // Function to format price in Thai Baht
        function formatPrice(price) {
            // ตรวจสอบว่า price เป็นตัวเลขและไม่ใช่ค่า NaN
            if (typeof price !== 'number' || isNaN(price)) {
                price = 0;
            }
            return new Intl.NumberFormat('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(price);
        }

        // Function to calculate total price of all items
        function calculateTotalPrice() {
            const orderCards = document.querySelectorAll('.order-card');
            let total = 0;
            
            orderCards.forEach(card => {
                const priceText = card.querySelector('.total-price').textContent;
                const price = parsePrice(priceText);
                total += price;
            });
            
            return total;
        }

        // Function to update the total price display
        function updateTotalDisplay() {
            const totalDisplay = document.querySelector('#total-display');
            const total = calculateTotalPrice();
            totalDisplay.textContent = `รวมทั้งหมด: ฿${formatPrice(total)}`;

            // อัพเดท hidden input สำหรับส่งค่าไปยัง server
            const totalPriceInput = document.querySelector('input[name="total_price"]');
            if (totalPriceInput) {
                totalPriceInput.value = total;
            }
        }

        // Function to check if there are any items in the order
        function hasItems() {
            return document.querySelectorAll('.order-card').length > 0;
        }

        // Function to handle form submission
        function handleFormSubmit(e) {
            if (!hasItems()) {
                e.preventDefault();
                alert('คุณไม่มีสินค้าเหลืออยู่ในคำสั่งซื้อแล้ว กรุณากลับไปเลือกสินค้าใหม่');
            }
        }

        // Function to update order button state
        function updateOrderButton() {
            const orderForm = document.querySelector('form');
            const orderButton = orderForm.querySelector('.order-button');
            
            if (!hasItems()) {
                orderButton.disabled = true;
                orderButton.style.opacity = '0.5';
                orderButton.style.cursor = 'not-allowed';
                // เพิ่ม event listener สำหรับ form
                orderForm.addEventListener('submit', handleFormSubmit);
            } else {
                orderButton.disabled = false;
                orderButton.style.opacity = '1';
                orderButton.style.cursor = 'pointer';
                // ลบ event listener เมื่อมีสินค้า
                orderForm.removeEventListener('submit', handleFormSubmit);
            }
        }

        // Function to remove an item by its id
        function removeItem(itemId) {
            const item = document.getElementById(itemId);
            if (item) {
                item.remove();

                // Update the total price
                updateTotalDisplay();
                    
                // Update order button state
                updateOrderButton();
            }
        }

        // Function to initialize total price on page load
        function initializeTotalPrice() {
            const orderCards = document.querySelectorAll('.order-card');
            if (orderCards.length > 0) {
                updateTotalDisplay();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializeTotalPrice();
            updateOrderButton();
        });

        function confirmOrder() {
            if (!hasItems()) {
                alert('คุณไม่มีสินค้าเหลืออยู่ในคำสั่งซื้อแล้ว กรุณากลับไปเลือกสินค้าใหม่');
                return false;
            }
            
            if (confirm('ยืนยันการสั่งผลิตสินค้า?')) {
                return true;
            }
            return false;
        }
    </script>
</body>
</html>
