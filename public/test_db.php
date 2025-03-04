<?php
// 獲取環境變量
$hostname = getenv('DB_HOST');
$database = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');
$port = getenv('DB_PORT') ?: 3306; // 默認端口為 3306

// DSN (數據源名稱)
$dsn = "mysql:host=$hostname;port=$port;dbname=$database;charset=utf8";

try {
    // 創建 PDO 實例
    $pdo = new PDO($dsn, $username, $password);
    // 設置 PDO 錯誤模式為異常
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to AWS RDS Successfully！";
} catch (PDOException $e) {
    echo "Failed to connect to AWS RDS: " . $e->getMessage();
}
?>
