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
require 'db.php'; // 引入数据库连接

// 定义变量
$search_term = '';
$customers = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search_term = trim($_POST['search_term']); // 获取搜索关键词

    if (!empty($search_term)) {
        try {
            // 查询客户表
            $stmt = $conn->prepare("SELECT * FROM customer WHERE name LIKE :search OR id = :exact_id");
            $stmt->bindValue(':search', '%' . $search_term . '%'); // 模糊匹配姓名
            $stmt->bindValue(':exact_id', $search_term, PDO::PARAM_INT); // 精确匹配 ID
            $stmt->execute();
            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("查询失败: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查找客户</title>
    <link rel="stylesheet" href="css\s_c_style.css">
</head>
<body>
    <div class="container">
        <h1>查找客户</h1>
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
        <form method="post" class="search-form">
            <label for="search_term">客户姓名或ID:</label>
            <input type="text" name="search_term" id="search_term" placeholder="输入客户姓名或客户ID" value="<?php echo htmlspecialchars($search_term); ?>" required>
            <button type="submit">查找</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <h2>搜索结果</h2>
            <?php if (count($customers) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>客户ID</th>
                            <th>姓名</th>
                            <th>联系方式</th>
                            <th>邮箱</th>
                            <th>房间id</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($customer['id']); ?></td>
                                <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo htmlspecialchars($customer['room_id']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>没有找到符合条件的客户。</p >
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>