<?php
require 'includes/db.php';
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: login.php");
    exit;
}
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();

// นับจำนวนสินค้าตะกร้า
$_SESSION['cart_count'] = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

$pageTitle = "หน้าร้าน";
ob_start();
?>

<h2 class="text-2xl font-bold mb-4">🎉 สินค้าแนะนำ</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <?php foreach ($products as $p): ?>
        <div class="bg-white rounded shadow p-4">
            <?php if ($p['image']): ?>
                <img src="uploads/products/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="h-40 w-full object-cover mb-2">
            <?php endif; ?>
            <h4 class="font-semibold text-lg mb-1"><?= htmlspecialchars($p['name']) ?></h4>
            <p class="text-gray-700 mb-2"><?= number_format($p['price'], 2) ?> บาท</p>
            <a href="product_detail.php?id=<?= $p['id'] ?>" 
                class="inline-block px-4 py-2 rounded bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold shadow-md hover:from-indigo-600 hover:to-purple-600 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                สั่งซื้อสินค้า
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
