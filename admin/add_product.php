<?php
require '../includes/db.php';
session_start();

// เช็คสิทธิ์
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $desc  = $_POST['description'];
    $price = $_POST['price'];
    $imageName = null;

    // อัปโหลดรูป
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/products/' . $imageName);
    }

    // บันทึกลง DB
    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $desc, $price, $imageName]);

    header("Location: products.php");
    exit;
}
// ตั้งชื่อหน้า
$pageTitle = "เพิ่มสินค้า";

// เก็บเนื้อหาหลัก หรือ ใช้ Layout
ob_start();
?>

<h2 class="text-2xl font-bold mb-6">เพิ่มสินค้า</h2>

<form method="POST" enctype="multipart/form-data" class="space-y-4 max-w-lg bg-white p-6 rounded shadow">
    <!-- ชื่อสินค้า -->
    <div>
        <label class="block mb-1 font-medium">ชื่อสินค้า</label>
        <input type="text" name="name" placeholder="ชื่อสินค้า"
               class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
    </div>

    <!-- รายละเอียดสินค้า -->
    <div>
        <label class="block mb-1 font-medium">รายละเอียดสินค้า</label>
        <textarea name="description" placeholder="รายละเอียดสินค้า"
                  class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required></textarea>
    </div>

    <!-- ราคาสินค้า -->
    <div>
        <label class="block mb-1 font-medium">ราคาสินค้า (บาท)</label>
        <input type="number" name="price" placeholder="ราคาสินค้า"
               class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
    </div>

    <!-- รูปภาพ -->
    <div>
        <label class="block mb-1 font-medium">รูปสินค้า</label>
        <input type="file" name="image" class="w-full">
    </div>

    <!-- ปุ่มเพิ่ม -->
    <div>
        <button type="submit"
                class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded shadow transition">
            ➕ เพิ่มสินค้า
        </button>
    </div>
</form>

<!-- ลิงก์กลับ -->
<p class="mt-4">
    <a href="products.php" class="text-blue-600 hover:underline">← กลับไปหน้าจัดการสินค้า</a>
</p>
<?php
$content = ob_get_clean();
include 'layout.php';