<?php
// Parse MySQL URL if available
$mysql_url = getenv('MYSQL_URL');

if ($mysql_url) {
    $url_parts = parse_url($mysql_url);
    $DB_HOST = $url_parts['host'];
    $DB_PORT = $url_parts['port'] ?? 3306;
    $DB_USER = $url_parts['user'];
    $DB_PASSWORD = $url_parts['pass'];
    $DB_NAME = ltrim($url_parts['path'], '/');
} else {
    // Fallback to individual env vars
    $DB_HOST = getenv('DB_HOST') ?: 'localhost';
    $DB_PORT = getenv('DB_PORT') ?: '3306';
    $DB_USER = getenv('DB_USER') ?: 'root';
    $DB_PASSWORD = getenv('DB_PASSWORD') ?: '';
    $DB_NAME = getenv('DB_NAME') ?: 'railway';
}

$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME, $DB_PORT);

if (!$con) {
    error_log("DB Error: " . mysqli_connect_error());
    error_log("Host: $DB_HOST, Port: $DB_PORT, User: $DB_USER, DB: $DB_NAME");
    die("⚠️ خطأ في الاتصال بقاعدة البيانات: " . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8mb4");
?>
