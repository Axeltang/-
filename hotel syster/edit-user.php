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
$user_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // 加密密码
    $role = $_POST['role'];

    try {
        // 更新用户
        $stmt = $conn->prepare("UPDATE user SET username = :username, password = :password, role = :role WHERE id = :user_id");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        // 记录日志
        log_action($_SESSION['user_id'], '编辑用户', '用户ID: ' . $user_id);

        echo "用户信息更新成功！";
    } catch (PDOException $e) {
        die("更新失败: " . $e->getMessage());
    }
} else {
    // 获取现有用户数据
    $stmt = $conn->prepare("SELECT * FROM user WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑用户</title>
    <link rel="stylesheet" href="css\style3.css">
</head>
<body>
    <div class="container">
        <h1>编辑用户</h1>
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
        
<form method="post">
    用户名: <input type="text" name="username" value="<?php echo $user['username']; ?>" required><br>
    密码: <input type="password" name="password" value="" required><br>
    角色: 
    <select name="role" required>
        <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>管理员</option>
        <option value="staff" <?php echo ($user['role'] == 'staff') ? 'selected' : ''; ?>>普通用户</option>
    </select><br>
    <button type="submit">更新用户</button>
</form>
    </div>
</body>
</html>