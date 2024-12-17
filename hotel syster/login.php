<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 查询用户名
    $stmt = $conn->prepare("SELECT id, password, role FROM user WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

}

// 生成随机的6个字母的验证码
function generateCaptcha() {
    return substr(str_shuffle(str_repeat('123456789', 3)), 0, 6);
}

// 如果会话中没有验证码，则生成一个新的验证码并存储在会话中
if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = generateCaptcha();
}

// 检查用户提交的表单
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 验证用户名和密码的逻辑（这里省略）

    
    // 验证用户输入的验证码
    $userCaptcha = trim($_POST['captcha']);
    if (strcasecmp($userCaptcha, $_SESSION['captcha']) === 0) {
        // 验证码正确，继续处理登录逻辑
        echo "验证码正确！<br>";
        if ($user) {
            // 密码验证
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
    
                // 根据角色跳转
                if ($user['role'] === 'admin') {
                    header("Location: dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
            } else {
                $_SESSION['error'] = "密码错误";
                header("Location: login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "用户名不存在";
            header("Location: login.php");
            exit();
        }
        // 这里可以添加重定向到另一个页面或显示成功消息等逻辑
    } else {
        // 验证码错误，显示错误消息并重新生成一个新的验证码（可选）
        echo "验证码错误，请重试。<br>";
        // 清除旧的验证码并生成一个新的（为了安全起见，每次提交失败后都应该这样做）
        unset($_SESSION['captcha']);
        header("Location: login.php");
        exit();
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录</title>
    <link rel="stylesheet" href="css\style2.css">
</head>
<body>
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
        </form>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<p class='error'>{$_SESSION['error']}</p >";
            unset($_SESSION['error']);
        }
        ?>
    </div>
</body>
</html>