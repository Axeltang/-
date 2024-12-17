<?php
session_start();

// 检查是否已登录
if (!isset($_SESSION['user_id'])) {
    // 如果未登录，重定向到登录页面
    header("Location: login.php");
    exit;
}

// 加载数据库配置
require 'db.php';

// 加载仪表盘内容
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>酒店管理系统</title>
    <link rel="stylesheet" href="css\style2.css"> <!-- 引入 CSS 文件 -->
</head>
<body>
    <header>
        <h1>酒店管理系统</h1>
        <div class="container">
        <h1>用户登录</h1>
        <form action="login.php" method="POST">
            <label for="username">用户名:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">密码:</label>
            <input type="password" name="password" id="password" required>

            <div class="captcha-group">
                <label>验证码:</label>
                <span id="captcha-display"><?php echo isset($_SESSION['captcha']) ? htmlspecialchars($_SESSION['captcha']) : ''; ?></span>
                <input type="text" id="captcha-input" name="captcha" required>
            </div>
            <button type="submit">登录</button>
    </header>

</body>
</html>