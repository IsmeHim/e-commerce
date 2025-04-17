<?php
session_start();
require '../includes/db.php';

// ตรวจสอบสิทธิ์
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
// ดักค่าการค้นหา
$search = $_GET['search'] ?? '';

// ดึงข้อมูลผู้ใช้
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "จัดการผู้ใช้";
// เก็บเนื้อหาหลัก
ob_start();
?>

<div class="max-w-7xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-6">👤 จัดการผู้ใช้งาน</h2>

    <form method="get" action="" class="mb-4 flex gap-2">
        <input type="text" name="search" placeholder="ค้นหาชื่อหรืออีเมล"
               value="<?= htmlspecialchars($search) ?>"
               class="border px-3 py-2 rounded w-full max-w-md">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">ค้นหา</button>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left border border-gray-200">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="p-3 border">ID</th>
                    <th class="p-3 border">ชื่อ</th>
                    <th class="p-3 border">อีเมล</th>
                    <th class="p-3 border">Role</th>
                    <th class="p-3 border">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border"><?= htmlspecialchars($user['id']) ?></td>
                    <td class="p-3 border"><?= htmlspecialchars($user['name']) ?></td>
                    <td class="p-3 border"><?= htmlspecialchars($user['email']) ?></td>
                    <td class="p-3 border"><?= htmlspecialchars($user['role']) ?></td>
                    <td class="p-3 border space-y-1">
                        <?php if ($user['role'] !== 'admin'): ?>
                            <form method="post" action="make_admin.php">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button class="bg-indigo-600 text-white px-3 py-1 mb-2 rounded hover:bg-indigo-700 w-full">ตั้งเป็นแอดมิน</button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="make_user.php">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button class="bg-gray-600 text-white px-3 py-1 mb-2 rounded hover:bg-gray-700 w-full">เปลี่ยนเป็นผู้ใช้</button>
                            </form>
                        <?php endif; ?>

                        <form method="get" action="edit_user.php">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <button class="bg-yellow-400 text-black px-3 py-1 mb-2 rounded hover:bg-yellow-500 w-full">แก้ไข</button>
                        </form>

                        <?php if ($user['is_active']): ?>
                            <form action="toggle_user_status.php" method="post">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <input type="hidden" name="action" value="lock">
                                <button class="bg-red-500 text-white px-3 py-1 mb-2 rounded hover:bg-red-600 w-full">ล็อก</button>
                            </form>
                        <?php else: ?>
                            <form action="toggle_user_status.php" method="post">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <input type="hidden" name="action" value="unlock">
                                <button class="bg-green-500 text-white px-3 py-1 mb-2 rounded hover:bg-green-600 w-full">ปลดล็อก</button>
                            </form>
                        <?php endif; ?>

                        <form method="post" action="delete_user.php" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าจะลบผู้ใช้นี้?');">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <button class="bg-red-700 text-white px-3 py-1 mb-2 rounded hover:bg-red-800 w-full">ลบ</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a href="index.php" class="text-blue-600 hover:underline">← กลับไป Dashboard</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
