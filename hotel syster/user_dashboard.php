<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: login.php');
    exit;
}

// 检查是否已登录且角色为普通用户
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: login.php');
    exit;
}

include 'db.php';

// 获取当前用户信息
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM user WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 如果用户信息不存在，退出登录
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>普通用户界面</title>
    <link rel="stylesheet" href="./css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>欢迎，<?= htmlspecialchars($user['username']) ?>！</h1>
    <h2>用户信息</h2>
    <div class="sidebar">
        <ul>
        <br><a href="./room_management.php">房间管理</a></br>
            <br><a href="./view_customers.php">客户管理</a></br>
        
            <br><a href="./log_out.php">登出</a></br>
        </ul>
    </div>
    <table>
        <tr>
            <th>用户名</th>
            <td><?= htmlspecialchars($user['username']) ?></td>
        </tr>
        <tr>
            <th>角色</th>
            <td><?= htmlspecialchars($user['role']) ?></td>
        </tr>
    </table>

    <h2>房间信息</h2>
    <?php
    // 查询房间信息
    $sql = "SELECT * FROM room";
    $stmt = $conn->query($sql);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rooms): ?>
        <table>
            <tr>
                <th>房间编号</th>
                <th>类型</th>
                <th>价格</th>
                <th>状态</th>
            </tr>
            <?php foreach ($rooms as $room): ?>
                <tr>
                    <td><?= htmlspecialchars($room['room_number']) ?></td>
                    <td><?= htmlspecialchars($room['type']) ?></td>
                    <td><?= htmlspecialchars($room['price']) ?></td>
                    <td><?= htmlspecialchars($room['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>暂无房间信息。</p >
    <?php endif; ?>

    <a href="./logout.php">登出</a>
</body>
</html>