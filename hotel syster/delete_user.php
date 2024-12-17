<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'db.php';

// 获取用户ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 获取用户信息以进行删除操作
    $sql = "SELECT * FROM user WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 执行删除操作
        $delete_sql = "DELETE FROM user WHERE id = :id";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->execute(['id' => $id]);
        

        // 记录操作日志
        $action = "删除用户：{$user['username']}";
        $log_sql = "INSERT INTO logs (user_id, action) VALUES (:user_id, :action)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->execute(['user_id' => $_SESSION['user_id'], 'action' => $action]);

        // 删除后重定向
        header('Location: user-management.php');
        exit;
    } else {
        echo "用户不存在";
    }
}
?>