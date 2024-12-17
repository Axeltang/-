<?php
session_start();
if (!isset($_SESSION['user_id'])) {
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
    // 获取当前页的数据
    $stmt = $conn->prepare("SELECT customer.*, room.room_number 
                            FROM customer 
                            LEFT JOIN room ON customer.room_id = room.id 
                            LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $records_per_page, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 获取总记录数
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM customer");
    $stmt->execute();
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 计算总页数
    $total_pages = ceil($total_users / $records_per_page);
} catch (PDOException $e) {
    die("数据库操作失败: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>客户管理</title>
    <link rel="stylesheet" href="css/room.css">
    <a href="./search_customer.php">查找客户</a>
</head>
<body>
    <h1>客户管理</h1>
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
            <th>姓名</th>
            <th>电话</th>
            <th>邮箱</th>
            <th>入住日期</th>
            <th>退房日期</th>
            <th>房间号</th>
            <th>操作</th>
        </tr>
        <?php foreach ($customers as $customer): ?>
            <tr>
                <td><?= $customer['id'] ?></td>
                <td><?= htmlspecialchars($customer['name']) ?></td>
                <td><?= htmlspecialchars($customer['phone']) ?></td>
                <td><?= htmlspecialchars($customer['email']) ?></td>
                <td><?= $customer['checkin_date'] ?></td>
                <td><?= $customer['checkout_date'] ?></td>
                <td><?= htmlspecialchars($customer['room_number'] ?? '无') ?></td>
                <td>
                    <a href="./update_customer.php?id=<?=$customer['id']?>">更新</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- 分页 -->
    <div class="pagination">
        <?php if ($current_page > 1): ?>
        <a href="view_customers.php?page=<?php echo $current_page - 1; ?>">&laquo; 上一页</a>
    <?php endif; ?>
 
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="view_customers.php?page=<?php echo $i; ?>" 
           class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
 
    <?php if ($current_page < $total_pages): ?>
        <a href="view_customers.php?page=<?php echo $current_page + 1; ?>">&raquo; 下一页</a>
    <?php endif; ?>
</div>
</body>
</html>