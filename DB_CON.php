<?php
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_PORT = getenv('DB_PORT') ?: '3306';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASSWORD = getenv('DB_PASSWORD') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'railway';

$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME, $DB_PORT);

if (!$con) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("⚠️ خطأ في الاتصال بقاعدة البيانات");
}

mysqli_set_charset($con, "utf8mb4");
?>
