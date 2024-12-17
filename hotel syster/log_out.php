
<?php
session_start();
include 'db.php';
if (isset($_SESSION['user_id'])) {
    // 记录登出日志
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $action = "用户 {$username} (ID: {$user_id}) 登出系统";

    $sql = "INSERT INTO logs (user_id, action) VALUES (:user_id, :action)";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['user_id' => $user_id, 'action' => $action]);

    // 清除会话数据
    session_unset();
    session_destroy();
}
// 重定向到登录页面
header('Location: login.php');
exit;
?>