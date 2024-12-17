<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'db.php';
require 'log_action.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM customer WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    $room_sql = "SELECT id, room_number FROM room WHERE status = '空闲'";
    $room_stmt = $conn->query($room_sql);
    $available_rooms = $room_stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $room_id = $_POST['room_id'];

    $sql = "UPDATE customer SET name = :name, phone = :phone, email = :email, room_id = :room_id WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'name' => $name,
        'phone' => $phone,
        'email' => $email,
        'room_id' => $room_id,
        'id' => $id
    ]);

    // 更新房间状态
    $update_room_sql = "UPDATE room SET status = '入住' WHERE id = :room_id";
    $stmt = $conn->prepare($update_room_sql);
    $stmt->execute(['room_id' => $room_id]);
// 获取当前登录用户的ID（从会话中获取，你可根据实际存储情况调整）
$user_id = $_SESSION['user_id'];

// 定义操作描述内容
$action = "更新了客户ID为{$id}的信息，并将其分配到房间ID为{$room_id}";
// 获取当前时间戳
$timestamp = time();
// 插入日志记录到logs表的SQL语句

$log_sql = "INSERT INTO logs (action, user_id, timestamp) VALUES (:action, :user_id, :timestamp)";
$log_stmt = $conn->prepare($log_sql);
$log_stmt->bindParam(':action', $action, PDO::PARAM_STR);
$log_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$log_stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
log_action($_SESSION['user_id'], '更改房间，房间号为：$room_number', '用户ID: ' . $user_id );
try {
    $log_stmt->execute();
} catch (PDOException $e) {
    // 如果日志插入出现错误，简单输出错误信息（实际中可考虑更完善的错误处理）
    echo "记录日志时出错: ". $e->getMessage();
}

    header('Location: view_customers.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>更新客户信息</title>
    <link rel="stylesheet" href="./css/style3.css">
</head>
<body>
    <h1>更新客户信息</h1>
    <div class="sidebar">
        <ul>
        <br><a href="./room_management.php">房间管理</a></br>
            <br><a href="./view_customers.php">客户管理</a></br>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <br><a href="./user-management.php">用户管理</a></br>
                <br><a href="./logs.php">查看日志</a></br>
                <br><a href ="./qureey.php">SQL 查询</a><br>
            <?php endif; ?>
            <br><a href="./log_out.php">登出</a></br>
        </ul>
    </div>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $customer['id'] ?>">
        <label>姓名:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required><br>
        <label>电话:</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>"><br>
        <label>邮箱:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>"><br>
        <label>房间:</label>
        <select name="room_id" required>
            <option value="">请选择房间</option>
            <?php foreach ($available_rooms as $room): ?>
                <option value="<?= $room['id'] ?>" <?= $customer['room_id'] == $room['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($room['room_number']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        <button type="submit">更新</button>
    </form>
</body>
</html>