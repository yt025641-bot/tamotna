<?php
/**
 * قالب إعدادات قاعدة البيانات
 * نسخ هذا الملف إلى DB_CON.php وتعديل البيانات
 */

// إعدادات قاعدة البيانات من Hostinger
$DB_HOST = 'localhost';  // غالباً localhost
$DB_USER = 'u123456_username';  // من لوحة تحكم Hostinger → Databases
$DB_PASSWORD = 'your_password_here';  // كلمة المرور من Hostinger
$DB_NAME = 'u123456_insurance';  // اسم قاعدة البيانات

// الاتصال بقاعدة البيانات
$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);

if (!$con) {
    // تسجيل الخطأ دون كشف التفاصيل
    error_log('Database connection failed: ' . mysqli_connect_error());
    die('خطأ في الاتصال بقاعدة البيانات');
}

// تعيين ترميز UTF-8
mysqli_set_charset($con, 'utf8mb4');
?>
