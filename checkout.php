<?php
require 'includes/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "ตะกร้าสินค้าว่างเปล่า";
    exit;
}

// ดึงข้อมูลสินค้า
$ids = implode(',', array_keys($cart));
$stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
$products = $stmt->fetchAll(PDO::FETCH_UNIQUE);

// คำนวณยอดรวม
$total = 0;
foreach ($cart as $id => $item) {
    $product = $products[$id];
    $total += $product['price'] * $item['quantity'];
}

// ถ้ามีการยืนยันสั่งซื้อ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // บันทึกออเดอร์
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user']['id'], $total]);
    $order_id = $pdo->lastInsertId();

    // บันทึกรายการสินค้า
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart as $id => $item) {
        $product = $products[$id];
        $stmt->execute([$order_id, $id, $item['quantity'], $product['price']]);
    }

    unset($_SESSION['cart']); // เคลียร์ตะกร้า

    echo "<p>✅ สั่งซื้อเรียบร้อย! หมายเลขคำสั่งซื้อ: #$order_id</p>";
    echo '<p><a href="index.php">กลับหน้าร้าน</a></p>';
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ยืนยันการสั่งซื้อ</title>
</head>
<body>

<h2>🧾 ยืนยันคำสั่งซื้อ</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>สินค้า</th>
        <th>ราคา</th>
        <th>จำนวน</th>
        <th>รวม</th>
    </tr>
    <?php foreach ($cart as $id => $item): 
        $product = $products[$id];
        $subtotal = $product['price'] * $item['quantity'];
    ?>
    <tr>
        <td><?= htmlspecialchars($product['name']) ?></td>
        <td><?= number_format($product['price'], 2) ?></td>
        <td><?= $item['quantity'] ?></td>
        <td><?= number_format($subtotal, 2) ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="3" align="right"><strong>รวมทั้งสิ้น:</strong></td>
        <td><strong><?= number_format($total, 2) ?> บาท</strong></td>
    </tr>
</table>

<form method="POST">
    <p><button type="submit">✅ ยืนยันการสั่งซื้อ</button></p>
</form>

<p><a href="cart.php">← กลับตะกร้า</a></p>

</body>
</html>
