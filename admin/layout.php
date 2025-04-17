
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'Admin Dashboard' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 fixed left-0 top-0 h-screen bg-indigo-700 text-white p-6 space-y-4">
        <h1 class="text-2xl font-bold mb-4">Admin Panel</h1>
        <p class="mb-4">👋 <?= htmlspecialchars($_SESSION['user']['name']) ?></p>
        <nav class="space-y-2">
            <a href="index.php" class="block hover:bg-indigo-600 rounded px-4 py-2">🏠 หน้าหลัก</a>
            <a href="products.php" class="block hover:bg-indigo-600 rounded px-4 py-2">📦 จัดการสินค้า</a>
            <a href="orders.php" class="block hover:bg-indigo-600 rounded px-4 py-2">🧾 จัดการคำสั่งซื้อ</a>
            <a href="users.php" class="block hover:bg-indigo-600 rounded px-4 py-2">👥 จัดการผู้ใช้งาน</a>
            <a href="../logout.php" class="block mt-6 bg-red-500 hover:bg-red-600 text-white rounded px-4 py-2">🚪 ออกจากระบบ</a>
        </nav>
    </aside>

    <!-- Main content -->
    <main class="ml-64 p-6">
        <?php if (isset($content)) echo $content; ?>
    </main>

</body>

</html>
