<?php
require '../includes/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// ดึงคำสั่งซื้อทั้งหมด
$stmt = $pdo->query("
    SELECT orders.*, users.name
    FROM orders
    JOIN users ON orders.user_id = users.id
    ORDER BY orders.created_at DESC
");
$orders = $stmt->fetchAll();

// ตั้งชื่อหน้า
$pageTitle = "คำสั่งซื้อทั้งหมด";

// เก็บเนื้อหาหลัก หรือ ใช้ Layout
ob_start();
?>
<h2 class="text-2xl font-bold mb-6">📦 คำสั่งซื้อทั้งหมด</h2>

<div class="overflow-x-auto">
    <table class="w-full border border-gray-200 rounded shadow text-sm">
        <thead class="bg-gray-100">
            <tr class="text-left">
                <th class="p-3 border">หมายเลข</th>
                <th class="p-3 border">ลูกค้า</th>
                <th class="p-3 border">ยอดรวม</th>
                <th class="p-3 border">วันที่</th>
                <th class="p-3 border">ดูรายละเอียด</th>
                <th class="p-3 border">สถานะ</th>
                <th class="p-3 border">อัปเดตสถานะ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr class="hover:bg-gray-50">
                <td class="p-3 border"><?= $order['id'] ?></td>
                <td class="p-3 border"><?= htmlspecialchars($order['name']) ?></td>
                <td class="p-3 border"><?= number_format($order['total'], 2) ?> บาท</td>
                <td class="p-3 border"><?= $order['created_at'] ?></td>
                <td class="p-3 border">
                    <a href="order_detail.php?id=<?= $order['id'] ?>" class="text-blue-600 hover:underline">🔍 ดู</a>
                </td>
                <td class="p-3 border"><?= htmlspecialchars($order['status']) ?></td>
                <td class="p-3 border">
                    <form method="post" action="update_status.php" class="flex items-center gap-2">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <select name="status" class="px-2 py-1 border rounded focus:outline-none focus:ring">
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>รอดำเนินการ</option>
                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>กำลังจัดส่ง</option>
                            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>จัดส่งแล้ว</option>
                        </select>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">
                            บันทึก
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<p class="mt-4">
    <a href="index.php" class="text-blue-600 hover:underline">← กลับแอดมิน</a>
</p>
<?php
$content = ob_get_clean();
include 'layout.php';