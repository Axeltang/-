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

// 获取房间列表
try {
    $stmt = $conn->query("SELECT * FROM room");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("获取房间列表失败: " . $e->getMessage());
}

// 删除房间
if (isset($_GET['delete'])) {
    $room_id = $_GET['delete'];
    try {
        $stmt = $conn->prepare("DELETE FROM room WHERE id = :id");
        $stmt->bindParam(':id', $room_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header('Location: room_management.php');
            exit;
        }
    } catch (PDOException $e) {
        die("删除房间失败: " . $e->getMessage());
    }
}
// 分页参数
$perPage = 8; // 每页显示记录数
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // 当前页码，默认为1
$offset = ($page - 1) * $perPage; // 计算偏移量

try {
    // 查询数据库
    $sql = "SELECT * FROM room LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 计算总记录数和总页数
    $totalSql = "SELECT COUNT(*) as total FROM room";
    $totalStmt = $conn->query($totalSql);
    $total = $totalStmt->fetchColumn();
    $totalPages = ceil($total / $perPage);
} catch (PDOException $e) {
    // 数据库查询出错处理
    die("数据库查询失败: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>房间管理</title>
    <link rel="stylesheet" href="css\room.css">
</head>
<body>
    <div class="container">
        <h1>房间管理</h1>
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
        <a href="add_room.php">添加房间</a>
        <table>
            <thead>
                <tr>
                    <th>房间ID</th>
                    <th>房间号</th>
                    <th>类型</th>
                    <th>价格</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rooms): ?>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($room['id']); ?></td>
                            <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                            <td><?php echo htmlspecialchars($room['type']); ?></td>
                            <td><?php echo htmlspecialchars($room['price']); ?></td>
                            <td><?php echo htmlspecialchars($room['status']); ?></td>
                            <td>
                             <a href="./update_room.php?id=<?php echo $room['id'];?>" class="btn">编辑
                            <a href="./room_management.php? delete=<?php echo $room['id'];?>"class="btn danger" onclick="return confirm('确认删除这个房间？')">删除
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">暂无房间信息。</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" <?php echo $i == $page ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
    <?php endfor; ?>
</div>
</body>
</html>