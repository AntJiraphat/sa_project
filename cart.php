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
            <img src="images/arrow_icon.png" alt="Back">
            <h1>รถเข็น</h1>
        </div>
        <div class="header-icons">
            <img src="images/settings_icon.png" alt="Settings">
            <img src="images/cart_icon.png" alt="Cart">
            <img src="images/user_icon.png" alt="User">
        </div>
    </header>

    <div class="cart-container">
        <div class="product">
            <input type="checkbox" class="product-checkbox">
            <img src="images/wooden_wardrobe.png" alt="ตู้เสื้อผ้าไม้ประตู 4 ประตู" class="product-image">
            <div class="product-details">
                <div class="product-title">ตู้เสื้อผ้าไม้ประดู่ 4 ประตู</div>
                <div class="product-color">
                    <option>สีมอคค่า</option>
                </div>
                <div class="product-price">฿6500</div>
            </div>
            <div class="quantity-control">
                <button class="quantity-btn decrease">-</button>
                <input type="text" value="1" class="quantity-input">
                <button class="quantity-btn increase">+</button>
            </div>
        </div>

        <div class="product">
            <input type="checkbox" class="product-checkbox">
            <img src="images/wooden_drawer.png" alt="ตู้ลิ้นชักไม้สัก" class="product-image">
            <div class="product-details">
                <div class="product-title">ตู้ลิ้นชักไม้สัก</div>
                <div class="product-color">
                    <option>สีมอคค่า</option>
                </div>
                <div class="product-price">฿3500</div>
            </div>
            <div class="quantity-control">
                <button class="quantity-btn decrease">-</button>
                <input type="text" value="1" class="quantity-input">
                <button class="quantity-btn increase">+</button>
            </div>
        </div>

        <div class="bottom-container">
            <label><input type="checkbox" id="select-all"> ทั้งหมด</label>
            <div class="product-price">รวม ฿0</div>
            <button class="checkout-btn">สั่งสินค้า</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const productCheckboxes = document.querySelectorAll('.product-checkbox');
            const totalPriceElement = document.querySelector('.bottom-container .product-price');
            const decreaseButtons = document.querySelectorAll('.decrease');
            const increaseButtons = document.querySelectorAll('.increase');
            const checkoutButton = document.querySelector('.checkout-btn');

            function updateTotalPrice() {
                let total = 0;
                document.querySelectorAll('.product').forEach(product => {
                    const checkbox = product.querySelector('.product-checkbox');
                    if (checkbox.checked) {
                        const priceText = product.querySelector('.product-price').textContent;
                        const price = parseInt(priceText.replace('฿', ''));
                        const quantity = parseInt(product.querySelector('.quantity-input').value);
                        total += price * quantity;
                    }
                });
                totalPriceElement.textContent = `รวม ฿${total}`;
            }

            // เพิ่มฟังก์ชันตรวจสอบการเลือกสินค้า
            function validateOrder() {
                const anyChecked = Array.from(productCheckboxes).some(checkbox => checkbox.checked);
                if (!anyChecked) {
                    Swal.fire({
                        title: 'กรุณาเลือกสินค้า',
                        text: 'กรุณาเลือกสินค้าก่อนยืนยันคำสั่งซื้อ',
                        icon: 'warning',
                        confirmButtonText: 'ตกลง'
                    });
                    return false;
                }
                return true;
            }

            selectAllCheckbox.addEventListener('change', function() {
                productCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateTotalPrice();
            });

            productCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(productCheckboxes).every(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                    updateTotalPrice();
                });
            });

            increaseButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('.quantity-input');
                    input.value = parseInt(input.value) + 1;
                    if (this.closest('.product').querySelector('.product-checkbox').checked) {
                        updateTotalPrice();
                    }
                });
            });

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
                            productElement.remove();
                            updateTotalPrice();
                        }
                    }
                });
            });

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

            // เพิ่ม event listener สำหรับปุ่มสั่งสินค้า
            checkoutButton.addEventListener('click', function() {
                if (validateOrder()) {
                    // ดำเนินการสั่งสินค้าต่อไป
                    Swal.fire({
                        title: 'ยืนยันการสั่งซื้อ',
                        text: 'คำสั่งซื้อของคุณได้รับการยืนยันเรียบร้อยแล้ว',
                        icon: 'success',
                        confirmButtonText: 'ตกลง'
                    });
                }
            });
        });
    </script>
</body>
</html>