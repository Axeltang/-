<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
include 'db.php';

// 每页显示的记录数
$records_per_page = 8;

// 获取当前页码，如果没有提供，则默认为第一页
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// 计算偏移量（Offset）
$offset = ($current_page - 1) * $records_per_page;

try {
    // 获取总记录数
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM logs");
    $stmt->execute();
    $total_logs = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 计算总页数
    $total_pages = ceil($total_logs / $records_per_page);

    // 获取当前页的数据
    $stmt = $conn->prepare("SELECT logs.*, user.username 
                            FROM logs 
                            JOIN user ON logs.user_id = user.id 
                            ORDER BY logs.timestamp DESC 
                            LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $records_per_page, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("数据库操作失败: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统日志</title>
    <link rel="stylesheet" href="./css/room.css">
    <style>
        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            color: black;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>系统日志</h1>
    <div class="sidebar">
        <ul>
        <br><a href="./room_management.php">房间管理</a></br>
            <br><a href="./view_customers.php">客户管理</a></br>
            <br><a href="./user-management.php">用户管理</a></br>
            <br><a href="./logs.php">查看日志</a></br>
            <br><a href ="./qureey.php">SQL 查询</a><br>
            <br><a href="./log_out.php">登出</a></br>
        </ul>
    </div>
    <table border="1">
        <tr>
            <th>时间</th>
            <th>用户</th>
            <th>操作</th> <!-- 假设 logs 表中有 action 列来记录操作 -->
        </tr>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= htmlspecialchars($log['timestamp']) ?></td>
                <td><?= htmlspecialchars($log['username']) ?></td>
                <td><?= htmlspecialchars($log['action']) ?></td> <!-- 确保 logs 表中有 action 列 -->
            </tr>
        <?php endforeach; ?>
    </table>
    <!-- 分页 -->
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="logs.php?page=<?php echo $current_page - 1; ?>" class="pagination-link">&laquo; 上一页</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="logs.php?page=<?php echo $i; ?>" 
               class="pagination-link <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($current_page < $total_pages): ?>
            <a href="logs.php?page=<?php echo $current_page + 1; ?>" class="pagination-link">&raquo; 下一页</a>
        <?php endif; ?>
    </div>
</body>
</html>