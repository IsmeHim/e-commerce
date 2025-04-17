<?php
require '../includes/db.php';
session_start();

// เช็คสิทธิ์
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// ตรวจสอบว่ามี id
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$id = $_GET['id'];

// ดึงข้อมูลสินค้าเดิม
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "ไม่พบสินค้าที่ต้องการแก้ไข";
    exit;
}

// ถ้ามีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $desc  = $_POST['description'];
    $price = $_POST['price'];
    $imageName = $product['image'];

    // อัปโหลดรูปใหม่
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/products/' . $imageName);
    }

    // อัปเดตฐานข้อมูล
    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
    $stmt->execute([$name, $desc, $price, $imageName, $id]);

    header("Location: products.php");
    exit;
}
// ตั้งชื่อหน้า
$pageTitle = "แก้ไขสินค้า";

// เก็บเนื้อหาหลัก หรือ ใช้ Layout
ob_start();
?>

<h1 class="text-2xl font-bold mb-6">แก้ไขสินค้า</h1>
<form method="POST" enctype="multipart/form-data" class="space-y-4">
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="w-full p-2 border rounded">
    <textarea name="description" required class="w-full p-2 border rounded"><?= htmlspecialchars($product['description']) ?></textarea>
    <input type="number" name="price" value="<?= $product['price'] ?>" required class="w-full p-2 border rounded">
    
    <div>
        <label class="block mb-1">เปลี่ยนรูปใหม่:</label>
        <input type="file" name="image">
        <?php if ($product['image']): ?>
            <img src="../uploads/products/<?= htmlspecialchars($product['image']) ?>" width="120" class="mt-2 rounded border">
        <?php endif; ?>
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">บันทึก</button>
</form>
<a href="products.php" class="text-blue-600 hover:underline mt-4 inline-block">← กลับหน้าจัดการสินค้า</a>
<?php
$content = ob_get_clean();
include 'layout.php';