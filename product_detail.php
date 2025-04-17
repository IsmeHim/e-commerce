<?php
require 'includes/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "ไม่พบสินค้าที่คุณค้นหา";
    exit;
}

$pageTitle = $product['name'];
ob_start();
?>

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow-lg mt-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- รูปภาพสินค้า -->
        <div>
            <?php if ($product['image']): ?>
                <img src="uploads/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="rounded w-full max-h-[400px] object-contain border">
            <?php else: ?>
                <div class="bg-gray-100 p-8 text-center rounded">ไม่มีรูปภาพสินค้า</div>
            <?php endif; ?>
        </div>

        <!-- ข้อมูลสินค้า -->
        <div>
            <h2 class="text-3xl font-bold mb-2"><?= htmlspecialchars($product['name']) ?></h2>
            <p class="text-xl text-green-600 font-semibold mb-4">💰 <?= number_format($product['price'], 2) ?> บาท</p>
            <p class="mb-4 text-gray-700 leading-relaxed">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </p>

            <form action="add_to_cart.php" method="POST" class="space-y-4">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <label class="block">
                    <span class="font-medium">จำนวน:</span>
                    <input type="number" name="quantity" value="1" min="1" class="mt-1 block w-24 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-indigo-400">
                </label>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded">
                    🛒 เพิ่มลงตะกร้า
                </button>
            </form>

            <div class="mt-6">
                <a href="index.php"
                    class="inline-block px-4 py-2 rounded bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold shadow-md hover:from-indigo-600 hover:to-purple-600 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                    ← กลับหน้าร้าน
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
