<?php
require 'includes/db.php';
require 'includes/functions.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "ไม่พบคำสั่งซื้อ";
    exit;
}

$order_id = $_GET['id'];
$user_id = $_SESSION['user']['id'];

// ตรวจสอบว่า order นี้เป็นของ user จริงหรือไม่
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "ไม่พบคำสั่งซื้อ หรือคุณไม่มีสิทธิ์ดูรายการนี้";
    exit;
}

// ดึงรายการสินค้าในคำสั่งซื้อ
$stmt = $pdo->prepare("
    SELECT order_items.*, products.name
    FROM order_items
    JOIN products ON order_items.product_id = products.id
    WHERE order_items.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

$pageTitle = "รายละเอียดคำสั่งซื้อ #" . $order['id'];
ob_start();
?>

<div class="max-w-4xl mx-auto mt-6 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">รายละเอียดคำสั่งซื้อ #<?= $order['id'] ?></h2>

    <div class="mb-4 text-sm text-gray-700">
        <p><strong>วันที่สั่งซื้อ:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        <p><strong>ยอดรวม:</strong> <?= number_format($order['total'], 2) ?> บาท</p>
        <p><strong>สถานะ:</strong> <?= translateStatus($order['status']) ?></p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border border-gray-300">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-2 border-b">สินค้า</th>
                    <th class="px-4 py-2 border-b">จำนวน</th>
                    <th class="px-4 py-2 border-b">ราคาต่อชิ้น</th>
                    <th class="px-4 py-2 border-b">รวม</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border-b"><?= htmlspecialchars($item['name']) ?></td>
                        <td class="px-4 py-2 border-b"><?= $item['quantity'] ?></td>
                        <td class="px-4 py-2 border-b"><?= number_format($item['price'], 2) ?> บาท</td>
                        <td class="px-4 py-2 border-b"><?= number_format($item['price'] * $item['quantity'], 2) ?> บาท</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a href="user_orders.php"  
            class="inline-block px-4 py-2 rounded bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold shadow-md hover:from-indigo-600 hover:to-purple-600 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
            ← ย้อนกลับไปยังประวัติการสั่งซื้อ
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
