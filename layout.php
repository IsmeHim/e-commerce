<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? 'ร้านค้าออนไลน์' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

<!-- Navbar -->
<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center space-x-4">
                <a href="index.php" class="text-xl font-bold text-indigo-700">🛍 ร้านค้า</a>
            </div>

            <div class="flex items-center space-x-4">
                <a href="cart.php" class="relative text-gray-700 hover:text-indigo-700">
                    🛒
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-1 rounded-full">
                        <?= $_SESSION['cart_count'] ?? 0 ?>
                    </span>
                </a>

                <!-- Dropdown -->
                <div class="relative group">
                    <button class="flex items-center space-x-2 focus:outline-none">
                        <span class="font-medium"><?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5.5 7l4.5 4.5L14.5 7H5.5z"/>
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded shadow-md opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all duration-200 z-50">
                        <a href="user_orders.php" class="block px-4 py-2 hover:bg-gray-100">🧾 ประวัติการสั่งซื้อ</a>
                        <a href="logout.php" class="block px-4 py-2 hover:bg-gray-100 text-red-600">🚪 ออกจากระบบ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Main content -->
<main class="flex-1 p-6">
    <?= $content ?>
</main>

<!-- Footer -->
<footer class="bg-white shadow-inner mt-auto text-center p-4 text-gray-600">
    © <?= date("Y") ?> ร้านค้าออนไลน์ | พัฒนาโดยคุณ
</footer>

</body>
</html>
