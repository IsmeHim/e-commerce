<?php
require 'includes/db.php';
require 'includes/functions.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

$pageTitle = "ประวัติการสั่งซื้อของฉัน";
ob_start();
?>

<div class="max-w-5xl mx-auto mt-6 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">🧾 ประวัติการสั่งซื้อของฉัน</h2>

    <?php if (empty($orders)): ?>
        <p class="text-gray-600">คุณยังไม่เคยสั่งซื้อสินค้า</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="px-4 py-2 border-b">หมายเลขคำสั่งซื้อ</th>
                        <th class="px-4 py-2 border-b">ยอดรวม</th>
                        <th class="px-4 py-2 border-b">วันที่</th>
                        <th class="px-4 py-2 border-b">ดูรายละเอียด</th>
                        <th class="px-4 py-2 border-b">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border-b">#<?= $order['id'] ?></td>
                            <td class="px-4 py-2 border-b"><?= number_format($order['total'], 2) ?> บาท</td>
                            <td class="px-4 py-2 border-b"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                            <td class="px-4 py-2 border-b">
                                <a href="user_order_detail.php?id=<?= $order['id'] ?>"
                                    class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-medium shadow-inner hover:bg-blue-200 transition duration-200">
                                    🔍 ดู
                                </a>
                            </td>
                            <td class="px-4 py-2 border-b"><?= translateStatus($order['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="mt-6">
        <a href="index.php"
            class="inline-block px-4 py-2 rounded bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold shadow-md hover:from-indigo-600 hover:to-purple-600 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
             ← กลับหน้าร้าน
         </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
