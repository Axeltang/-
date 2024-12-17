<!--  <?php
$host = 'localhost';
$dbname = 'sql628581';
$username = 'sql628581';
$password = 'wBrcSXTX6z';


try {
    // 使用 PDO 连接数据库
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // 设置错误模式为异常
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "连接失败: " . $e->getMessage();
} 

?> -->
<?php
$host = '127.0.0.1';
$db= 'sql628581';
$user = 'sql628581';
$pass = 'wBrcSXTX6z';


$charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // 启用异常模式
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // 默认返回关联数组
        PDO::ATTR_EMULATE_PREPARES   => false,                  // 禁用模拟预处理
    ]);
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}
?>