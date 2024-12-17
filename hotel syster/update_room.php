<?php
session_start();
require 'log_action.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('房间 ID 缺失！');
}

$room_id = $_GET['id'];
$room = null;
$success_message = '';
$error_message = '';

// 获取房间信息
try {
    $stmt = $conn->prepare("SELECT * FROM room WHERE id = :id");
    $stmt->bindParam(':id', $room_id, PDO::PARAM_INT);
    $stmt->execute();
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        die('未找到对应的房间！');
    }
} catch (PDOException $e) {
    die("获取房间信息失败: " . $e->getMessage());
}

// 更新房间信息
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_type = $_POST['room_type'];
    $room_price = $_POST['room_price'];
    $room_status = $_POST['room_status'];

    try {
        $stmt = $conn->prepare("UPDATE room SET type = :type, price = :price, status = :status WHERE id = :id");
        $stmt->bindParam(':type', $room_type);
        $stmt->bindParam(':price', $room_price, PDO::PARAM_STR);
        $stmt->bindParam(':status', $room_status);
        $stmt->bindParam(':id', $room_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // $success_message = '房间信息更新成功！';
            $_SESSION['success'] = "房间添加成功！";
      
            // 获取当前用户ID（假设是管理员）
            $admin_user_id = $_SESSION['user_id']; // 假设session中存有当前用户ID
            
            // 记录日志
            log_action($admin_user_id, '添加房间', "添加了房间号为$room_number 的新房间");
      
            header("Location: room-management.php");
        } else {
            $error_message = '房间信息更新失败，请稍后重试。';
        }
    } catch (PDOException $e) {
        $error_message = "房间信息更新失败: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>更新房间信息</title>
    <link rel="stylesheet" href="css\room.css">
</head>
<body>
    <div class="container">
        <h1>更新房间信息</h1>
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
        <form method="post">
            <label for="room_type">房间类型:</label>
            <select name="room_type" id="room_type" required>
                <option value="单人房" <?php echo $room['status'] == '单人房' ? 'selected' : ''; ?>>单人房</option>
                <option value="双人房" <?php echo $room['status'] == '双人房' ? 'selected' : ''; ?>>双人房</option>
            </select>
            
            <label for="room_price">房间价格:</label>
            <input type="number" name="room_price" id="room_price" value="<?php echo htmlspecialchars($room['price']); ?>" step="0.01" required>
            
            <label for="room_status">房间状态:</label>
            <select name="room_status" id="room_status" required>
                <option value="空闲" <?php echo $room['status'] == '空闲' ? 'selected' : ''; ?>>空闲</option>
                <option value="入住" <?php echo $room['status'] == '入住' ? 'selected' : ''; ?>>入住</option>
                <option value="清理中" <?php echo $room['status'] == '清理中' ? 'selected' : ''; ?>>清理中</option>
            </select>
            
            <button type="submit">更新房间信息</button>
        </form>
        <a href="./room_management.php">返回房间管理</a>
    </div>
</body>
</html>