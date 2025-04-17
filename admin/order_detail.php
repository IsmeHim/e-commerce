<?php
require '../includes/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "ไม่พบคำสั่งซื้อ";
    exit;
}

$order_id = $_GET['id'];

// ดึงข้อมูลคำสั่งซื้อ
$stmt = $pdo->prepare("
    SELECT orders.*, users.name
    FROM orders
    JOIN users ON orders.user_id = users.id
    WHERE orders.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "ไม่พบคำสั่งซื้อ";
    exit;
}

// ดึงสินค้าในคำสั่งซื้อ
$stmt = $pdo->prepare("
    SELECT order_items.*, products.name
    FROM order_items
    JOIN products ON order_items.product_id = products.id
    WHERE order_items.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

// ตั้งชื่อหน้า
$pageTitle = "รายละเอียดคำสั่งซื้อ";

// เก็บเนื้อหาหลัก หรือ ใช้ Layout
ob_start();
?>

<div class="max-w-4xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">🔍 รายละเอียดคำสั่งซื้อ #<?= $order['id'] ?></h2>

    <div class="mb-6 space-y-2">
        <p><strong>ลูกค้า:</strong> <?= htmlspecialchars($order['name']) ?></p>
        <p><strong>วันที่สั่งซื้อ:</strong> <?= $order['created_at'] ?></p>
        <p><strong>ยอดรวม:</strong> <span class="text-green-600 font-semibold"><?= number_format($order['total'], 2) ?> บาท</span></p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left border border-gray-200">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="p-3 border">สินค้า</th>
                    <th class="p-3 border">จำนวน</th>
                    <th class="p-3 border">ราคาต่อชิ้น</th>
                    <th class="p-3 border">รวม</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border"><?= htmlspecialchars($item['name']) ?></td>
                    <td class="p-3 border"><?= $item['quantity'] ?></td>
                    <td class="p-3 border"><?= number_format($item['price'], 2) ?> บาท</td>
                    <td class="p-3 border text-green-600"><?= number_format($item['price'] * $item['quantity'], 2) ?> บาท</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a href="orders.php" class="text-blue-600 hover:underline">← กลับคำสั่งซื้อ</a>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'layout.php';
