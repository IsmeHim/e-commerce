<?php
require '../includes/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: users.php");
    exit;
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: users.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // เช็คว่าอีเมลเป็นรูปแบบที่ถูกต้อง
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("รูปแบบอีเมลไม่ถูกต้อง");
    }

    // เช็คว่าอีเมลมีคนใช้แล้วหรือไม่
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        die("อีเมลนี้ถูกใช้แล้ว");
    }

    // ถ้ารหัสผ่านใหม่ไม่ว่าง ให้เปลี่ยนรหัสผ่าน
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
        $stmt->execute([$name, $email, $hashedPassword, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $id]);
    }

    header("Location: users.php");
    exit;
}

$pageTitle = "แก้ไขผู้ใช้";
// เก็บเนื้อหาหลัก
ob_start();
?>

<h2 class="text-2xl font-bold mb-6">✏️ แก้ไขข้อมูลผู้ใช้งาน</h2>

<form method="post" class="bg-white p-6 rounded-lg shadow-md max-w-lg">
    <div class="mb-4">
        <label class="block mb-1 font-medium">ชื่อ:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
    </div>

    <div class="mb-4">
        <label class="block mb-1 font-medium">อีเมล:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
    </div>

    <div class="mb-4">
        <label class="block mb-1 font-medium">รหัสผ่านใหม่ (ถ้าไม่เปลี่ยนให้เว้นว่าง):</label>
        <input type="password" name="password" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">💾 บันทึก</button>
</form>

<p class="mt-6">
    <a href="users.php" class="text-blue-600 hover:underline">← กลับ</a>
</p>

<?php
$content = ob_get_clean();
include 'layout.php'; // หรือชื่อ layout ของคุณ
?>
