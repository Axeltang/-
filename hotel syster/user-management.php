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
// 查询所有用户
$stmt = $conn->prepare("SELECT * FROM user");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 设置每页记录数
$records_per_page = 10;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

try {
    // 获取当前页的用户列表
    $stmt = $conn->prepare("SELECT * FROM user LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $records_per_page, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 获取用户总数
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM user");
    $stmt->execute();
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $total_pages = ceil($total_users / $records_per_page);
} catch (PDOException $e) {
    die("操作失败: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户管理</title>
    <link rel="stylesheet" href="css\style3.css">
</head>
<body>
    <div class="container">
        <h1>用户管理</h1>
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
        <a href="./add-user.php">添加新用户</a>
        <table>
            <thead>
                <tr>
                    <th>用户名</th>
                    <th>角色</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                        <a href="edit-user.php?id=<?php echo $user ['id'];?>" >编辑</a>
                          <a href="delete_user.php?id=<?php echo $user ['id'];?>" onclick="return confirm('确定删除此用户吗？')">删除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="pagination">
        <?php if ($current_page > 1): ?>
           <a href="./user-management.php?page=<?php echo $current_page -1;?>"> &laquo; 上一页</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="./user-management.php?page=<?php echo $i; ?>"class="<?php echo $i==$current_page ? 'active':'';?>"<?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($current_page < $total_pages): ?>
            <a href ="./user-management.php?page=<?php echo $current_page +1;?>"下一页 &raquo;</a>
        <?php endif; ?>
    </div>
    </div>
</body>
</html>