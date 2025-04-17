<?php
require 'includes/db.php';
session_start();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        // 🔒 เช็กก่อนว่าบัญชีถูกล็อกไหม
        if (!$user['is_active']) {
            $errors[] = "บัญชีของคุณถูกล็อก กรุณาติดต่อผู้ดูแลระบบ";
        } else {
            $_SESSION['user'] = [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
                'role'  => $user['role'],
            ];
    
            // ถ้าเป็น admin ไปหน้า dashboard
            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }
    
    } else {
        $errors[] = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
    }
     
}
?>

<!-- UI -->
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center text-indigo-600">เข้าสู่ระบบ</h2>

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
            เข้าสู่ระบบ
        </button>
    </form>

    <p class="text-center mt-4 text-gray-600 text-sm">
        ยังไม่มีบัญชี? <a href="register.php" class="text-indigo-600 hover:underline">สมัครเลย</a>
    </p>
</div>

</body>
</html>
