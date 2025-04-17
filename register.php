<?php
require 'includes/db.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // เช็คว่ามีข้อมูลครบมั้ย
    if (empty($name) || empty($email) || empty($password)) {
        $errors[] = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    }

    // เช็คว่าอีเมลซ้ำมั้ย
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "อีเมลนี้ถูกใช้ไปแล้ว";
    }

    // ถ้าไม่มี error ให้บันทึกข้อมูล
    if (empty($errors)) {
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashPassword]);
        header("Location: login.php");
        exit;
    }
}
?>

<!-- UI -->
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิก</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center text-indigo-600">สมัครสมาชิก</h2>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-gray-700">ชื่อ - นามสกุล</label>
            <input type="text" name="name" placeholder="ชื่อของคุณ"
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-gray-700">อีเมล</label>
            <input type="email" name="email" placeholder="example@email.com"
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-gray-700">รหัสผ่าน</label>
            <input type="password" name="password" placeholder="รหัสผ่าน"
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg transition">
            สมัครสมาชิก
        </button>
    </form>

    <p class="text-center mt-4 text-gray-600 text-sm">
        มีบัญชีแล้ว? <a href="login.php" class="text-indigo-600 hover:underline">เข้าสู่ระบบ</a>
    </p>
</div>

</body>
</html>
