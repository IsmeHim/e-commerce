<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$products = [];

if (!empty($cart)) {
    $ids = implode(',', array_keys($cart));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll(PDO::FETCH_UNIQUE);
}

$pageTitle = "ตะกร้าสินค้า";
ob_start();
?>

<h2 class="text-2xl font-bold mb-6">🛒 ตะกร้าสินค้า</h2>

<?php if (empty($cart)): ?>
    <p class="text-gray-600">ยังไม่มีสินค้าในตะกร้า</p>
<?php else: ?>
    <form action="update_cart.php" method="POST">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow rounded-lg">
                <thead>
                    <tr class="bg-indigo-600 text-white text-left">
                        <th class="px-6 py-3">สินค้า</th>
                        <th class="px-6 py-3">ราคา</th>
                        <th class="px-6 py-3">จำนวน</th>
                        <th class="px-6 py-3">รวม</th>
                        <th class="px-6 py-3">ลบ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($cart as $id => $item):
                        $product = $products[$id];
                        $subtotal = $product['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4"><?= htmlspecialchars($product['name']) ?></td>
                        <td class="px-6 py-4"><?= number_format($product['price'], 2) ?> บาท</td>
                        <td class="px-6 py-4">
                            <input type="number" name="quantities[<?= $id ?>]" value="<?= $item['quantity'] ?>" min="1"
                                   class="w-20 border border-gray-300 rounded px-2 py-1">
                        </td>
                        <td class="px-6 py-4"><?= number_format($subtotal, 2) ?> บาท</td>
                        <td class="px-6 py-4">
                            <a href="remove_from_cart.php?id=<?= $id ?>"
                                class="inline-block px-3 py-1 rounded bg-gradient-to-r from-red-500 to-pink-500 text-white text-sm font-semibold shadow-md hover:from-red-600 hover:to-pink-600 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                ลบ
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="bg-gray-100 font-semibold">
                        <td colspan="3" class="px-6 py-4 text-right">รวมทั้งสิ้น:</td>
                        <td colspan="2" class="px-6 py-4"><?= number_format($total, 2) ?> บาท</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center mt-6">
        <a href="index.php"
            class="inline-block px-4 py-2 rounded bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold shadow-md hover:from-indigo-600 hover:to-purple-600 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
            ← กลับหน้าร้าน
        </a>
            <div class="flex gap-4">
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                    🔄 อัปเดตจำนวน
                </button>
                <a href="checkout.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    ✅ สั่งซื้อสินค้า
                </a>
            </div>
        </div>
    </form>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'layout.php'; // ใช้ layout เดิมที่เราสร้างไว้
?>
