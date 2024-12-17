<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'db.php';

$errors = [];
$results = [];
$query = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = trim($_POST['sql_query']);

    // 防止空查询
    if (empty($query)) {
        $errors[] = "查询不能为空！";
    } else {
        try {
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $errors[] = "查询失败: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL 查询工具</title>
    <link rel="stylesheet" href="./css/style3.css">
</head>
<body>
    <h1>SQL 查询工具</h1>
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
    <form method="POST">
        <label>输入 SQL 查询语句:</label><br>
        <textarea name="sql_query" rows="5" cols="50" placeholder="请输入 SQL 查询语句..."><?= htmlspecialchars($query) ?></textarea><br>
        <button type="submit">执行查询</button>
    </form>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p >
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($results)): ?>
        <h2>查询结果</h2>
        <table border="1">
            <thead>
                <tr>
                    <?php foreach (array_keys($results[0]) as $column): ?>
                        <th><?= htmlspecialchars($column) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <td><?= htmlspecialchars($cell) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>