<?php
require '../includes/db.php';
session_start();

// เช็คสิทธิ์
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// ลบสินค้า
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: products.php");
    exit;
}

// ดึงสินค้าทั้งหมด
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();

// ตั้งชื่อหน้า
$pageTitle = "จัดการสินค้า";

// เก็บเนื้อหาหลัก
ob_start();
?>

<h2 class="text-2xl font-semibold mb-4">📦 จัดการสินค้า</h2>
<a href="add_product.php" class="inline-block bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded mb-4">+ เพิ่มสินค้า</a>

<div class="overflow-x-auto">
    <table class="w-full table-auto bg-white shadow-md rounded">
        <thead class="bg-gray-200 text-left">
            <tr>
                <th class="p-3">ชื่อสินค้า</th>
                <th class="p-3">ราคา</th>
                <th class="p-3">รูป</th>
                <th class="p-3">จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr class="border-t">
                    <td class="p-3"><?= htmlspecialchars($product['name']) ?></td>
                    <td class="p-3"><?= number_format($product['price'], 2) ?> บาท</td>
                    <td class="p-3">
                        <?php if ($product['image']): ?>
                            <img src="../uploads/products/<?= $product['image'] ?>" class="w-16 h-16 object-cover rounded">
                        <?php endif; ?>
                    </td>
                    <td class="p-3">
                        <div class="flex space-x-2">
                            <a href="edit_product.php?id=<?= $product['id'] ?>"
                            class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded shadow text-sm transition">
                                ✏️ แก้ไข
                            </a>
                            <a href="products.php?delete=<?= $product['id'] ?>"
                            onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?')"
                            class="inline-block bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow text-sm transition">
                                🗑️ ลบ
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
