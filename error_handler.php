<?php
/**
 * Error Handler for B-Care Insurance
 * معالج الأخطاء لمشروع بي كير للتأمين
 */

// إعدادات الأخطاء للإنتاج
ini_set('display_errors', 0);           // لا تظهر الأخطاء للمستخدمين
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);               // سجّل الأخطاء
ini_set('error_log', __DIR__ . '/logs/error_log.txt');

// إنشاء مجلد logs إذا لم يكن موجوداً
if (!file_exists(__DIR__ . '/logs')) {
    @mkdir(__DIR__ . '/logs', 0755, true);
}

// معالج الأخطاء المخصص
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $message = sprintf(
        "[%s] Error [%d]: %s in %s:%d\n",
        date('Y-m-d H:i:s'),
        $errno,
        $errstr,
        $errfile,
        $errline
    );
    
    error_log($message, 3, __DIR__ . '/logs/error_log.txt');
    
    // عرض صفحة خطأ عامة للمستخدم
    if (error_reporting() !== 0) {
        // لا تعرض تفاصيل الخطأ
        return true;
    }
    
    return false;
});

// معالج الاستثناءات
set_exception_handler(function($exception) {
    $message = sprintf(
        "[%s] Exception: %s in %s:%d\n%s\n",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );
    
    error_log($message, 3, __DIR__ . '/logs/error_log.txt');
    
    // عرض صفحة خطأ صديقة للمستخدم
    http_response_code(500);
    echo '<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>خطأ مؤقت</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #156394; }
    </style>
</head>
<body>
    <h1>عذراً، حدث خطأ مؤقت</h1>
    <p>نعمل على حل المشكلة. يرجى المحاولة لاحقاً.</p>
    <a href="/">العودة للصفحة الرئيسية</a>
</body>
</html>';
    exit;
});

// معالج الأخطاء القاتلة
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        $message = sprintf(
            "[%s] Fatal Error [%d]: %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            $error['type'],
            $error['message'],
            $error['file'],
            $error['line']
        );
        
        error_log($message, 3, __DIR__ . '/logs/error_log.txt');
    }
});
?>
