<?php
require '../includes/db.php'; // ใช้ไฟล์เชื่อมต่อฐานข้อมูล
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// ดึงข้อมูลสถิติจากฐานข้อมูล
// จำนวนผู้ใช้ทั้งหมด
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmt->fetchColumn();

// จำนวนแอดมินทั้งหมด
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
$totalAdmins = $stmt->fetchColumn();

// จำนวนสินค้าทั้งหมด
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$totalProducts = $stmt->fetchColumn();

// จำนวนคำสั่งซื้อทั้งหมด
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$totalOrders = $stmt->fetchColumn();

$pageTitle = "Admin Dashboard";
ob_start();
?>

<h2 class="text-2xl font-semibold mb-4">แผงควบคุมผู้ดูแลระบบ</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded shadow hover:shadow-lg">
        <h3 class="text-lg font-bold mb-2">📦 สินค้าทั้งหมด</h3>
        <p class="text-gray-600"><?= $totalProducts ?> รายการ</p>
    </div>
    <div class="bg-white p-6 rounded shadow hover:shadow-lg">
        <h3 class="text-lg font-bold mb-2">🧾 คำสั่งซื้อ</h3>
        <p class="text-gray-600"><?= $totalOrders ?> คำสั่งซื้อ</p>
    </div>
    <div class="bg-white p-6 rounded shadow hover:shadow-lg">
        <h3 class="text-lg font-bold mb-2">👥 ผู้ใช้งานทั้งหมด</h3>
        <p class="text-gray-600"><?= $totalUsers ?> คน</p>
    </div>
    <div class="bg-white p-6 rounded shadow hover:shadow-lg">
        <h3 class="text-lg font-bold mb-2">👨‍💼 แอดมินทั้งหมด</h3>
        <p class="text-gray-600"><?= $totalAdmins ?> คน</p>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
