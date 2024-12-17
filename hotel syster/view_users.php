<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../db.php';

// 每页显示的记录数
$per_page = 10;
 
// 获取当前页码（默认为1）
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
 
// 计算偏移量
$offset = ($page - 1) * $per_page;
 
// 查询数据库
$sql = "SELECT id, name, description FROM items LIMIT $offset, $per_page";
$result = $conn->query($sql);
 
// 获取总记录数
$total_sql = "SELECT COUNT(*) as total FROM items";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetchAll()['total'];
 
// 计算总页数
$total_pages = ceil($total_rows / $per_page);
// 获取用户数据
$sql = "SELECT id, username, role, created_at FROM user";
$stmt = $conn->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户管理</title>
    <link rel="stylesheet" href="./css/room.css">
</head>
<body>
    <h1>用户管理</h1>
    添加新用户
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
    <table border="1">
        <tr>
            <th>ID</th>
            <th>用户名</th>
            <th>角色</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= $user['created_at'] ?></td>
                <td>
                    <a href="./update_user.php?id=<?=$user['id']?>">编辑</a>
                    <a href="./delete_user.php?id=<?=$user['id']?>" onclick="return confirm('确定要删除该用户吗？')">删除</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div>
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>">上一页</a>
        <?php endif; ?>
 
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'style="font-weight: bold;"'; ?>><?php echo $i; ?></a>
        <?php endfor; ?>
 
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>">下一页</a>
        <?php endif; ?>
    </div>
</body>
</html>