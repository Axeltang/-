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
$success_message = '';
$error_message = '';
// // 检查登录
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit;
// }
if (is_null($pdo)) {
  die('数据库连接未成功。');
} else {
  echo '数据库连接成功。';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 接收表单数据
  $room_number = trim($_POST['room_number']);
  $type = isset($_POST['type']) ? $_POST['type'] : null;
  $price = trim($_POST['price']);

  if (!$room_number || !$price) {
      die('房间号和价格不能为空！');
  }
  try {
    $stmt = $pdo->prepare("INSERT INTO room (room_number, type, price) VALUES (:room_number, :type, :price)");
    $stmt->bindParam(':room_number', $room_number);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':price', $price);
    $stmt->execute();
    if ($stmt->execute()) {
      $_SESSION['success'] = "房间添加成功！";
      
      // 获取当前用户ID（假设是管理员）
      $admin_user_id = $_SESSION['user_id']; // 假设session中存有当前用户ID
      
      // 记录日志
      log_action($admin_user_id, '添加房间', "添加了房间号为$room_number 的新房间");

      header("Location: room-management.php");
      exit();
  }
    
} catch (PDOException $e) {
    echo "添加失败: " . $e->getMessage();
}
  header('Location: room_management.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加房间</title>
    <link rel="stylesheet" href="css\room.css">
</head>
<body>
    <div class="container">
        <h1>添加房间</h1>
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
        <?php if ($success_message): ?>
            <p class="success"><?php echo $success_message; ?></p >
        <?php endif; ?>
        <?php if ($error_message): ?>
            <p class="error"><?php echo $error_message; ?></p >
        <?php endif; ?>
        <form method="POST" action="add_room.php">
        <label for="room_number">房间号:</label>
        <input type="text" id="room_number" name="room_number" required>

        <label for="type">房间类型:</label>
        <select id="type" name="type">
            <option value="">--请选择--</option>
            <option value="单人房">单人房</option>
            <option value="双人房">双人房</option>
        </select>

        <label for="price">价格:</label>
        <input type="number" id="price" name="price" step="0.01" required>

        <button type="submit">添加房间</button>
    </form>
        <a href="./room_management.php">返回房间管理</a>
    </div>
</body>
</html>