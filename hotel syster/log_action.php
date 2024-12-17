<?php
function log_action($user_id, $action, $description = null) {
    global $conn;

    // 如果未提供 user_id，使用默认值 0 表示系统操作
    if (empty($user_id)) {
        $user_id = 0; // 或者抛出异常
    }

    if ($description === null) {
        $description = $action;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO logs (user_id, action, description) VALUES (:user_id, :action, :description)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
    } catch (Exception $e) {
        die("日志记录失败: " . $e->getMessage());
    }
}
?>