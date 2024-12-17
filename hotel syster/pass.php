<?php
require 'db.php';

// 假设这是你要更新的用户名和密码
$username = 'admin'; // 替换成你需要更新密码的用户名
$new_password = '123456'; // 新密码

// 生成哈希密码
$hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);

// 更新数据库中的密码
$stmt = $conn->prepare("UPDATE user SET password = :password WHERE username = :username");
$stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);

if ($stmt->execute()) {
    echo "密码更新成功！";
} else {
    echo "密码更新失败！";
}
?>