<?php
session_start();
require 'db.php';
require 'log_action.php';
// 如果表单提交
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
  $role = $_POST['role'];

  // 插入数据
  $stmt = $conn->prepare("INSERT INTO user (username, password, role) VALUES (:username, :password, :role)");
  $stmt->bindParam(':username', $username);
  $stmt->bindParam(':password', $password);
  $stmt->bindParam(':role', $role);

  if ($stmt->execute()) {
      $_SESSION['success'] = "用户添加成功！";
      
      // 获取当前用户ID（假设是管理员）
      $admin_user_id = $_SESSION['user_id']; // 假设session中存有当前用户ID
      
      // 记录日志
      log_action($admin_user_id, '添加用户', "添加了用户名为 $username 的新用户");

      header("Location: user-management.php");
      exit();
  } else {
      $_SESSION['error'] = "添加用户失败，请重试。";
  }
}

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加用户</title>
    <link rel="stylesheet" href="css\styles.css">
</head>
<body>
    <div class="container">
        <h1>添加新用户</h1>
        <div class="sidebar">
        <ul>
        <br><a href="./room_management.php">房间管理</a></br>
            <br><a href="./view_customers.php">客户管理</a></br>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <br><a href="./user-management.php">用户管理</a></br>
                <br><a href="./logs.php">查看日志</a></br>
                <br><a href ="./qureey.php">SQL 查询</a><br>
            <?php endif; ?>>
            <br><a href="./log_out.php">登出</a></br>
        </ul>
    </div>
        <form action="add-user.php" method="POST">
            <label for="username">用户名：</label>
            <input type="text" name="username" required><br>
            <label for="password">密码：</label>
            <input type="password" name="password" required><br>
            <label for="role">角色：</label>
            <select name="role">
                <option value="admin">管理员</option>
                <option value="staff">员工</option>
            </select><br>
            <button type="submit">添加用户</button>
        </form>
    </div>
</body>
</html>