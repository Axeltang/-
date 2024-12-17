<?php
ob_start(); // 开启输出缓冲
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
ob_end_flush(); // 输出缓冲结束
require 'db.php';
require 'log_action.php';

// 初始化消息变量
$success = '';
$error = '';

// 获取用户信息
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $sql = "SELECT * FROM user WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = "用户不存在！";
    }
} else {
    $error = "未提供用户 ID！";
}

// 更新用户信息
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($username)) {
        $error = "用户名不能为空！";
    } else {
        try {
            // 检查是否需要更新密码
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT); // 加密密码
                $sql = "UPDATE user SET username = :username, password = :password, role = :role WHERE id = :id";
                $params = [
                    'username' => $username,
                    'password' => $hashed_password,
                    'role' => $role,
                    'id' => $user_id
                ];
            } else {
                $sql = "UPDATE user SET username = :username, role = :role WHERE id = :id";
                $params = [
                    'username' => $username,
                    'role' => $role,
                    'id' => $user_id
                ];
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            $success = "用户信息更新成功！";
        } catch (PDOException $e) {
            $error = "更新失败：" . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>更新用户信息</title>
    <link rel="stylesheet" href="./css/room.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
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
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>更新用户信息</h1>
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
    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p >
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p >
    <?php endif; ?>

    <?php if ($user): ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">用户名</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">密码（留空则不修改）</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="role">角色</label>
                <select id="role" name="role" required>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>管理员</option>
                    <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>普通用户</option>
                </select>
            </div>
            <button type="submit" name="update_user">更新用户信息</button>
        </form>
        <a href="./manage_users.php">返回用户管理</a>
    <?php endif; ?>
</body>
</html>