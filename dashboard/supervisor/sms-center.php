<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/sms.php';
require_role('supervisor');

$title = 'مدیریت پیامک‌ها';
$current_page = 'sms-center.php';
require_once 'layout.php';

sms_ensure_schema($pdo);
?>

<style>
.sms-shell {
    display: flex;
    flex-direction: column;
    gap: 24px;
    padding-bottom: 60px;
}

/* Glass Cards */
.sms-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    transition: all 0.3s ease;
}

/* Balance Hero Widget */
.balance-widget {
    background: linear-gradient(135deg, rgba(20, 30, 48, 0.7) 0%, rgba(36, 59, 85, 0.7) 100%);
    border: 1.5px solid rgba(255, 255, 255, 0.15);
    border-radius: 22px;
    padding: 24px 28px;
    position: relative;
    overflow: hidden;
}
.balance-widget::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 280px;
    height: 280px;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%);
    pointer-events: none;
}

.balance-amount {
    font-size: 2.1rem;
    font-weight: 800;
    color: #10b981;
    letter-spacing: -0.5px;
}
.balance-count-badge {
    background: rgba(16, 185, 129, 0.15);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #34d399;
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 0.95rem;
    font-weight: 600;
}

/* Tab Navigation */
.sms-tabs-nav {
    display: flex;
    gap: 8px;
    background: rgba(0, 0, 0, 0.3);
    padding: 6px;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    overflow-x: auto;
    scrollbar-width: none;
}
.sms-tab-btn {
    background: transparent;
    border: none;
    color: #94a3b8;
    padding: 10px 18px;
    border-radius: 12px;
    font-size: 0.92rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.25s ease;
}
.sms-tab-btn:hover {
    color: #e2e8f0;
    background: rgba(255, 255, 255, 0.05);
}
.sms-tab-btn.active {
    background: #2563eb;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
}

/* Pattern Cards */
.pattern-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 18px;
}
.pattern-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, border-color 0.2s ease;
}
.pattern-card:hover {
    transform: translateY(-3px);
    border-color: rgba(99, 102, 241, 0.4);
    background: rgba(255, 255, 255, 0.045);
}
.pattern-code-pill {
    background: rgba(99, 102, 241, 0.15);
    color: #818cf8;
    border: 1px solid rgba(99, 102, 241, 0.3);
    font-family: monospace;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 8px;
    font-size: 0.95rem;
    cursor: pointer;
}
.pattern-text-box {
    background: rgba(0, 0, 0, 0.35);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 10px;
    padding: 12px;
    font-size: 0.88rem;
    line-height: 1.6;
    color: #cbd5e1;
    white-space: pre-wrap;
    word-break: break-word;
    margin: 12px 0;
}
.var-highlight {
    background: rgba(245, 158, 11, 0.2);
    color: #fbbf24;
    padding: 1px 5px;
    border-radius: 4px;
    font-weight: 600;
}

/* Automation Card */
.auto-card {
    background: rgba(255, 255, 255, 0.035);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 14px;
    transition: all 0.25s ease;
}
.auto-card:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.14);
}
.auto-card.inactive {
    opacity: 0.6;
}

/* Switch styling */
.form-switch .form-check-input {
    width: 2.75rem;
    height: 1.45rem;
    cursor: pointer;
}

/* Custom form elements */
.sms-input, .sms-select, .sms-textarea {
    background: rgba(0, 0, 0, 0.35) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    color: #f8fafc !important;
    border-radius: 12px !important;
    padding: 11px 14px;
}
.sms-input:focus, .sms-select:focus, .sms-textarea:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}

/* Datatable style */
.sms-table {
    width: 100%;
    color: #cbd5e1;
    border-collapse: separate;
    border-spacing: 0 6px;
}
.sms-table th {
    background: rgba(255, 255, 255, 0.05);
    padding: 12px 14px;
    font-weight: 600;
    font-size: 0.85rem;
    color: #94a3b8;
    border: none;
}
.sms-table th:first-child { border-radius: 10px 0 0 10px; }
.sms-table th:last-child { border-radius: 0 10px 10px 0; }
.sms-table td {
    background: rgba(255, 255, 255, 0.02);
    padding: 14px;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    font-size: 0.88rem;
    vertical-align: middle;
}
.sms-table tr:hover td {
    background: rgba(255, 255, 255, 0.04);
}
.sms-table td:first-child { border-radius: 10px 0 0 10px; border-left: 1px solid rgba(255, 255, 255, 0.05); }
.sms-table td:last-child { border-radius: 0 10px 10px 0; border-right: 1px solid rgba(255, 255, 255, 0.05); }

/* Quick pill badge */
.pill-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 600;
}
.pill-green { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
.pill-yellow { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
.pill-blue { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
.pill-red { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }

/* Spin animation */
.spin-anim {
    animation: rotateSpin 0.8s linear infinite;
}
@keyframes rotateSpin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<div class="sms-shell">

    <!-- Top Hero / Balance Widget -->
    <div class="balance-widget d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <div class="text-white-50 small mb-1">
                <span>سامانه پیامکی غزال (فراز اس‌ام‌اس / IPPanel)</span>
                <span class="mx-2">•</span>
                <span id="lineInfo">خط فرستنده: ۹۰۰۰۸۳۶۱</span>
            </div>
            <div class="d-flex align-items-baseline gap-3 flex-wrap">
                <span class="text-white opacity-75 fs-5">اعتبار باقی‌مانده:</span>
                <span class="balance-amount" id="balanceAmountText">در حال دریافت...</span>
                <span class="text-white opacity-50 small" id="tomanAmountText"></span>
                <span class="balance-count-badge" id="balanceCountBadge">~ ... پیامک</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-light btn-sm rounded-pill px-3" id="btnRefreshBalance" onclick="loadBalance()">
                <span id="refreshIcon">🔄</span> به‌روزرسانی موجودی
            </button>
            <a href="https://farazsms.com" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm rounded-pill px-3">
                ➕ شارژ مستقیم پنل
            </a>
        </div>
    </div>

    <!-- Tabs Bar -->
    <div class="sms-tabs-nav">
        <button class="sms-tab-btn active" onclick="switchTab('patterns', this)">
            <span>📋</span> پترن‌های تایید شده
        </button>
        <button class="sms-tab-btn" onclick="switchTab('new-pattern', this)">
            <span>✨</span> ساخت پترن جدید
        </button>
        <button class="sms-tab-btn" onclick="switchTab('send', this)">
            <span>📤</span> ارسال پیامک (تکی / گروهی)
        </button>
        <button class="sms-tab-btn" onclick="switchTab('automations', this)">
            <span>⚡</span> موتور اتومیشن‌ها
        </button>
        <button class="sms-tab-btn" onclick="switchTab('logs', this)">
            <span>📊</span> لاگ و تاریخچه پیامک‌ها
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: VERIFIED PATTERNS -->
    <!-- ========================================================================= -->
    <div class="sms-tab-pane" id="pane-patterns">
        <div class="sms-card">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="text-white fw-bold mb-1">الگوهای خدماتی تایید شده (Patterns)</h5>
                    <p class="text-white-50 small mb-0">این الگوها تاییدشده و بدون مسدودی بلک‌لیست مخابرات (ارسال ۱۰۰٪ تضمینی به تمام شماره‌ها) هستند.</p>
                </div>
                <div>
                    <button class="btn btn-outline-primary btn-sm rounded-pill" onclick="loadPatterns()">
                        🔄 بارگذاری مجدد پترن‌ها
                    </button>
                </div>
            </div>

            <div id="patternsLoading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="text-white-50 mt-2 small">در حال فراخوانی پترن‌ها از وب‌سرویس فراز اس‌ام‌اس...</div>
            </div>

            <div class="pattern-grid" id="patternsGrid" style="display: none;"></div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: CREATE NEW PATTERN -->
    <!-- ========================================================================= -->
    <div class="sms-tab-pane" id="pane-new-pattern" style="display: none;">
        <div class="sms-card">
            <div class="mb-4">
                <h5 class="text-white fw-bold mb-1">درخواست و ساخت پترن جدید (Pattern Builder)</h5>
                <p class="text-white-50 small mb-0">الگوی جدید مستقیماً جهت تأیید به اپراتور فراز اس‌ام‌اس ارسال می‌شود (تأیید معمولاً بین ۱۰ الی ۳۰ دقیقه در ساعات کاری).</p>
            </div>

            <div class="alert alert-info bg-opacity-10 border-info text-info small mb-4 py-2 px-3 rounded-3">
                <strong>💡 قوانین مهم مخابرات جهت رد نشدن پترن:</strong>
                <ul class="mb-0 mt-1 pe-3">
                    <li>قبل از هر متغیر باید حتماً کلمه مربوطه قید شود (مثال صحیح: <code>کلاس %class%</code> یا <code>تاریخ %date%</code> | مثال غلط: <code>%class%</code> به تنهایی).</li>
                    <li>نام تجاری «آموزشگاه زبان‌های خارجه غزال» در انتهای متن قید شود.</li>
                    <li>متن نباید جنبه تبلیغاتی داشته باشد؛ صرفاً جنبه اطلاع‌رسانی و خدماتی به زبان‌آموز یا استاد.</li>
                </ul>
            </div>

            <form id="newPatternForm" onsubmit="handleCreatePattern(event)">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">عنوان و کاربرد پترن (جهت بررسی اپراتور)</label>
                        <input type="text" class="form-control sms-input" id="newPatternDesc" placeholder="مثال: یادآوری شروع جلسه و لینک کلاس" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">آدرس وب‌سایت آموزشگاه</label>
                        <input type="text" class="form-control sms-input" id="newPatternWeb" value="https://panel.ghazalify.com" required>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label text-white-50 small mb-0">متن پترن (همراه با متغیرهای درصدی)</label>
                            <div class="d-flex gap-1">
                                <span class="small text-white-50 me-2">افزودن متغیر:</span>
                                <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2 rounded-2" onclick="insertVar('%name%')">+ %name%</button>
                                <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2 rounded-2" onclick="insertVar('%date%')">+ %date%</button>
                                <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2 rounded-2" onclick="insertVar('%class%')">+ %class%</button>
                                <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2 rounded-2" onclick="insertVar('%code%')">+ %code%</button>
                                <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2 rounded-2" onclick="insertVar('%item%')">+ %item%</button>
                            </div>
                        </div>
                        <textarea class="form-control sms-textarea" id="newPatternText" rows="4" placeholder="مثال: زبان‌آموز گرامی %name%، کلاس %class% شما در تاریخ %date% تشکیل خواهد شد. آموزشگاه زبان‌های خارجه غزال" required></textarea>
                    </div>

                    <div class="col-12 pt-2">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill" id="btnSubmitPattern">
                            🚀 ثبت و ارسال پترن به اپراتور
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: SEND SMS (ONE-SHOT / BULK) -->
    <!-- ========================================================================= -->
    <div class="sms-tab-pane" id="pane-send" style="display: none;">
        <div class="sms-card">
            <div class="mb-4">
                <h5 class="text-white fw-bold mb-1">ارسال پیامک تکی و جمعی</h5>
                <p class="text-white-50 small mb-0">ارسال پیامک به اساتید، زبان‌آموزان یک کلاس، یا لیست شماره‌های اختصاصی.</p>
            </div>

            <form id="sendSmsForm" onsubmit="handleSendSms(event)">
                <div class="row g-4">
                    <!-- Method Selection -->
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">نوع ارسال</label>
                        <select class="form-select sms-select" id="sendType" onchange="toggleSendTypeUI()">
                            <option value="pattern" selected>ارسال بر اساس پترن خدماتی (۱۰۰٪ تضمینی بدون بلک‌لیست)</option>
                            <option value="simple">ارسال پیامک متنی آزاد (ساده)</option>
                        </select>
                    </div>

                    <!-- Target Selection -->
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">مخاطبان هدف</label>
                        <select class="form-select sms-select" id="targetType" onchange="toggleTargetTypeUI()">
                            <option value="manual" selected>ورود دستی شماره‌ها (تکی یا چندتایی)</option>
                            <option value="class">دانش‌آموزان یک کلاس خاص</option>
                            <option value="teachers">کلیه اساتید فعال آموزشگاه</option>
                            <option value="students">کلیه زبان‌آموزان فعال سامانه</option>
                        </select>
                    </div>

                    <!-- Class Select (Conditional) -->
                    <div class="col-md-12" id="boxClassSelect" style="display: none;">
                        <label class="form-label text-white-50 small">انتخاب کلاس</label>
                        <select class="form-select sms-select" id="targetClassSelect">
                            <option value="">در حال بارگذاری کلاس‌ها...</option>
                        </select>
                    </div>

                    <!-- Manual Phones Input (Conditional) -->
                    <div class="col-md-12" id="boxManualPhones">
                        <label class="form-label text-white-50 small">شماره موبایل‌ها (با کاما یا اینتر جدا کنید)</label>
                        <textarea class="form-control sms-textarea" id="manualPhones" rows="2" placeholder="مثال: 09121111111, 09362222222"></textarea>
                    </div>

                    <!-- Pattern Chooser & Dynamic Variables (When sendType == 'pattern') -->
                    <div class="col-12" id="boxPatternFields">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small">انتخاب پترن</label>
                                <select class="form-select sms-select" id="selectPatternCode" onchange="onPatternSelected()">
                                    <option value="">انتخاب از میان پترن‌های تاییدشده...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small">پیش‌نمایش قالب پترن</label>
                                <div id="selectedPatternPreview" class="p-2 rounded-3 bg-black bg-opacity-25 border border-white-10 text-white-50 small" style="min-height: 44px;">
                                    لطفاً یک پترن را انتخاب کنید.
                                </div>
                            </div>

                            <!-- Dynamic Inputs for Variables -->
                            <div class="col-12">
                                <div id="dynamicVarsContainer" class="row g-2 mt-1"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Simple Text Area (When sendType == 'simple') -->
                    <div class="col-12" id="boxSimpleText" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label text-white-50 small mb-0">متن پیامک آزاد</label>
                            <span class="small text-white-50" id="charCountLabel">۰ کاراکتر (۱ پارت)</span>
                        </div>
                        <textarea class="form-control sms-textarea" id="simpleMessageText" rows="4" placeholder="متن پیامک خود را اینجا بنویسید..." oninput="updateCharCount()"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12 pt-2">
                        <button type="submit" class="btn btn-success px-4 py-2 rounded-pill fw-bold" id="btnDispatchSms">
                            📨 ارسال پیامک
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: AUTOMATIONS ENGINE -->
    <!-- ========================================================================= -->
    <div class="sms-tab-pane" id="pane-automations" style="display: none;">
        <div class="sms-card">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="text-white fw-bold mb-1">موتور اتومیشن‌های خودکار پیامکی</h5>
                    <p class="text-white-50 small mb-0">رویدادهایی که در سیستم رخ می‌دهند (غیبت، تأخیر، رول‌کال، نمرات پایانی) و به صورت خودکار پیامک مربوطه را شلیک می‌کنند.</p>
                </div>
                <div>
                    <button class="btn btn-primary btn-sm rounded-pill px-3" onclick="openNewAutomationModal()">
                        ➕ ایجاد اتومیشن جدید
                    </button>
                </div>
            </div>

            <div id="automationsList">
                <div class="text-center py-4 text-white-50 small">در حال بارگذاری اتومیشن‌ها...</div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 5: UNIFIED LOGS -->
    <!-- ========================================================================= -->
    <div class="sms-tab-pane" id="pane-logs" style="display: none;">
        <div class="sms-card">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h5 class="text-white fw-bold mb-1">مرکز جامع لاگ و تاریخچه پیامک‌ها</h5>
                    <p class="text-white-50 small mb-0">تمامی پیامک‌های ارسالی سیستم (غیبت، تأخیر، QC اساتید و ارسال‌های دستی سوپروایزر).</p>
                </div>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <select class="form-select sms-select form-select-sm" id="logTypeFilter" onchange="loadLogs(1)">
                        <option value="all">تمام پیامک‌ها (تجمیعی)</option>
                        <option value="manual">فقط ارسال‌های دستی سوپروایزر</option>
                        <option value="qc">فقط هشدارهای QC اساتید</option>
                        <option value="absence">فقط اعلام غیبت دانش‌آموزان</option>
                        <option value="late">فقط اعلام تأخیر دانش‌آموزان</option>
                    </select>
                    <button class="btn btn-outline-light btn-sm rounded-pill" onclick="loadLogs(1)">
                        🔄 تازه‌سازی
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="sms-table">
                    <thead>
                        <tr>
                            <th>زمان ارسال</th>
                            <th>منبع / نوع رویداد</th>
                            <th>گیرنده</th>
                            <th>شماره تماس</th>
                            <th>قالب / متن پیام</th>
                            <th>وضعیت</th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody">
                        <tr>
                            <td colspan="6" class="text-center py-4 text-white-50">در حال دریافت تاریخچه پیامک‌ها...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 text-white-50 small">
                <span id="logsPaginationInfo">صفحه ۱</span>
                <div class="d-flex gap-1" id="logsPaginationButtons"></div>
            </div>
        </div>
    </div>

</div>

<!-- Modal: New Automation -->
<div class="modal fade" id="modalAutomation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border border-secondary rounded-4 p-2">
            <div class="modal-header border-0 pb-1">
                <h5 class="modal-title fw-bold">ایجاد / ویرایش اتومیشن پیامکی</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form onsubmit="handleSaveAutomation(event)">
                <input type="hidden" id="autoId" value="0">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white-50 small">عنوان اتومیشن</label>
                        <input type="text" class="form-control sms-input" id="autoTitle" placeholder="مثال: یادآوری رول‌کال به استاد" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white-50 small">رویداد محرک (Trigger)</label>
                        <select class="form-select sms-select" id="autoTrigger">
                            <option value="student_absent">غیبت دانش‌آموز (رول‌کال)</option>
                            <option value="student_late">تأخیر دانش‌آموز (رول‌کال)</option>
                            <option value="teacher_qc_rollcall">رول‌کال معوق استاد</option>
                            <option value="teacher_qc_finalmarks">نمرات پایانی ترم استاد (جلسه آخر)</option>
                            <option value="birthday">زادروز (تولد زبان‌آموز یا استاد)</option>
                            <option value="pre_class_reminder">یادآوری قبل از شروع کلاس</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-white-50 small">مخاطب پیامک</label>
                            <select class="form-select sms-select" id="autoAudience">
                                <option value="parent">ولی دانش‌آموز</option>
                                <option value="student">خود دانش‌آموز</option>
                                <option value="teacher">استاد کلاس</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-white-50 small">فاصله زمانی (دقیقه)</label>
                            <input type="number" class="form-control sms-input" id="autoOffset" value="15" placeholder="مثال: 15 دقیقه پس از پایان">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white-50 small">کد پترن خدماتی</label>
                        <input type="text" class="form-control sms-input" id="autoPatternCode" placeholder="مثال: cyedV1fZDy" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-white-50 small">مپینگ متغیرها (JSON اختیاری)</label>
                        <textarea class="form-control sms-textarea" id="autoVarsMap" rows="2" placeholder='{"name":"{student_name}","date":"{jalali_date}"}'></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">ذخیره اتومیشن</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let globalPatterns = [];
let audienceClasses = [];
let currentLogPage = 1;

document.addEventListener('DOMContentLoaded', () => {
    loadBalance();
    loadPatterns();
    loadAudienceMeta();
    loadAutomations();
});

// =========================================================
// TAB SWITCHING
// =========================================================
function switchTab(tabId, btn) {
    document.querySelectorAll('.sms-tab-pane').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.sms-tab-btn').forEach(el => el.classList.remove('active'));
    
    const target = document.getElementById('pane-' + tabId);
    if (target) target.style.display = 'block';
    if (btn) btn.classList.add('active');

    if (tabId === 'logs') {
        loadLogs(1);
    } else if (tabId === 'automations') {
        loadAutomations();
    }
}

// =========================================================
// 1. BALANCE
// =========================================================
async function loadBalance() {
    const icon = document.getElementById('refreshIcon');
    if (icon) icon.classList.add('spin-anim');

    try {
        const res = await fetch('ajax_sms_center.php?action=get_balance');
        const data = await res.json();
        if (data.ok) {
            const amountRial = Number(data.amount || 0);
            const amountToman = Math.floor(amountRial / 10);
            document.getElementById('balanceAmountText').textContent = amountRial.toLocaleString('fa-IR') + ' ریال';
            document.getElementById('tomanAmountText').textContent = '(' + amountToman.toLocaleString('fa-IR') + ' تومان)';
            document.getElementById('balanceCountBadge').textContent = '~ ' + Number(data.count || 0).toLocaleString('fa-IR') + ' پیامک';

            if (data.count < 500) {
                document.getElementById('balanceCountBadge').className = 'balance-count-badge pill-red';
            } else if (data.count < 1500) {
                document.getElementById('balanceCountBadge').className = 'balance-count-badge pill-yellow';
            } else {
                document.getElementById('balanceCountBadge').className = 'balance-count-badge pill-green';
            }
        } else {
            document.getElementById('balanceAmountText').textContent = 'خطا در وب‌سرویس';
        }
    } catch (e) {
        document.getElementById('balanceAmountText').textContent = 'خطای ارتباطی';
    } finally {
        if (icon) icon.classList.remove('spin-anim');
    }
}

// =========================================================
// 2. PATTERNS
// =========================================================
async function loadPatterns() {
    const loader = document.getElementById('patternsLoading');
    const grid = document.getElementById('patternsGrid');
    loader.style.display = 'block';
    grid.style.display = 'none';

    try {
        const res = await fetch('ajax_sms_center.php?action=get_patterns');
        const data = await res.json();
        if (data.ok && Array.isArray(data.patterns)) {
            globalPatterns = data.patterns;
            renderPatternsGrid(globalPatterns);
            populatePatternDropdown(globalPatterns);
        } else {
            grid.innerHTML = '<div class="col-12 text-center text-danger py-4">عدم امکان دریافت پترن‌ها: ' + (data.error || 'ناشناخته') + '</div>';
        }
    } catch (e) {
        grid.innerHTML = '<div class="col-12 text-center text-danger py-4">خطا در ارتباط با سرور</div>';
    } finally {
        loader.style.display = 'none';
        grid.style.display = 'grid';
    }
}

function renderPatternsGrid(patterns) {
    const grid = document.getElementById('patternsGrid');
    if (!patterns.length) {
        grid.innerHTML = '<div class="col-12 text-center text-white-50 py-4">هیچ پترنی یافت نشد.</div>';
        return;
    }

    let html = '';
    patterns.forEach(p => {
        const statusPill = p.status === 'active'
            ? '<span class="pill-badge pill-green">تایید شده</span>'
            : '<span class="pill-badge pill-yellow">در انتظار بررسی</span>';

        // Highlight %variables%
        const highlightedText = (p.text || '').replace(/(%[a-zA-Z0-9_]+%)/g, '<span class="var-highlight">$1</span>');

        let varsPills = '';
        if (Array.isArray(p.attributes)) {
            varsPills = p.attributes.map(a => `<span class="badge bg-secondary bg-opacity-50 text-white me-1 mb-1 font-monospace">%${a.var}%</span>`).join('');
        }

        html += `
        <div class="pattern-card">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="pattern-code-pill" onclick="copyText('${p.code}')" title="برای کپی کلیک کنید">
                        ${p.code} 📋
                    </span>
                    ${statusPill}
                </div>
                <div class="fw-bold text-white small mb-1">${p.description || 'بدون توضیح'}</div>
                <div class="pattern-text-box">${highlightedText}</div>
                <div class="mb-2">${varsPills}</div>
            </div>
            <div class="pt-2 border-top border-white-10 d-flex justify-content-between align-items-center">
                <small class="text-white-50">${p.website || 'ghazalify.com'}</small>
                <button class="btn btn-sm btn-outline-info rounded-pill px-3 py-1" onclick="usePatternInSend('${p.code}')">
                    ارسال با این پترن 🚀
                </button>
            </div>
        </div>
        `;
    });
    grid.innerHTML = html;
}

function copyText(str) {
    navigator.clipboard.writeText(str).then(() => {
        alert('کد پترن کپی شد: ' + str);
    });
}

function usePatternInSend(code) {
    const tabBtn = document.querySelectorAll('.sms-tab-btn')[2];
    switchTab('send', tabBtn);
    document.getElementById('sendType').value = 'pattern';
    toggleSendTypeUI();
    document.getElementById('selectPatternCode').value = code;
    onPatternSelected();
}

function populatePatternDropdown(patterns) {
    const sel = document.getElementById('selectPatternCode');
    sel.innerHTML = '<option value="">-- انتخاب پترن مورد نظر --</option>';
    patterns.forEach(p => {
        sel.innerHTML += `<option value="${p.code}">${p.code} — ${p.description || p.code} (${p.status})</option>`;
    });
}

function onPatternSelected() {
    const code = document.getElementById('selectPatternCode').value;
    const preview = document.getElementById('selectedPatternPreview');
    const container = document.getElementById('dynamicVarsContainer');
    container.innerHTML = '';

    const p = globalPatterns.find(x => x.code === code);
    if (!p) {
        preview.textContent = 'لطفاً یک پترن را انتخاب کنید.';
        return;
    }

    preview.innerHTML = (p.text || '').replace(/(%[a-zA-Z0-9_]+%)/g, '<span class="var-highlight">$1</span>');

    if (Array.isArray(p.attributes) && p.attributes.length > 0) {
        let inputsHtml = '<div class="col-12"><div class="text-white-50 small mb-1">مقداردهی متغیرهای پترن (می‌توانید از <code>{name}</code> برای جایگذاری خودکار نام مخاطب استفاده کنید):</div></div>';
        p.attributes.forEach(attr => {
            let defVal = '';
            if (attr.var === 'name' || attr.var === 'parent' || attr.var === 'family') {
                defVal = '{name}';
            }
            inputsHtml += `
            <div class="col-md-4">
                <label class="form-label text-warning small font-monospace">%${attr.var}%</label>
                <input type="text" class="form-control sms-input" name="vars[${attr.var}]" value="${defVal}" placeholder="مقدار برای %${attr.var}%" required>
            </div>
            `;
        });
        container.innerHTML = inputsHtml;
    }
}

// Insert variable button inside Pattern Builder
function insertVar(varName) {
    const txt = document.getElementById('newPatternText');
    const start = txt.selectionStart;
    const end = txt.selectionEnd;
    const current = txt.value;
    txt.value = current.substring(0, start) + ' ' + varName + ' ' + current.substring(end);
    txt.focus();
    txt.setSelectionRange(start + varName.length + 2, start + varName.length + 2);
}

// =========================================================
// 3. CREATE PATTERN
// =========================================================
async function handleCreatePattern(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitPattern');
    btn.disabled = true;
    btn.textContent = 'در حال ثبت در سامانه پیامکی...';

    const desc = document.getElementById('newPatternDesc').value;
    const text = document.getElementById('newPatternText').value;
    const web = document.getElementById('newPatternWeb').value;

    const fd = new FormData();
    fd.append('action', 'create_pattern');
    fd.append('description', desc);
    fd.append('text', text);
    fd.append('website', web);

    try {
        const res = await fetch('ajax_sms_center.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.ok) {
            alert('✅ پترن با موفقیت ثبت شد و در صف تأیید اپراتور قرار گرفت.');
            document.getElementById('newPatternForm').reset();
            loadPatterns();
            switchTab('patterns', document.querySelectorAll('.sms-tab-btn')[0]);
        } else {
            alert('❌ خطا در ثبت پترن: ' + (data.message || data.error || 'ناشناخته'));
        }
    } catch (err) {
        alert('خطای شبکه یا سرور');
    } finally {
        btn.disabled = false;
        btn.textContent = '🚀 ثبت و ارسال پترن به اپراتور';
    }
}

// =========================================================
// 4. AUDIENCE METADATA
// =========================================================
async function loadAudienceMeta() {
    try {
        const res = await fetch('ajax_sms_center.php?action=get_audience_meta');
        const data = await res.json();
        if (data.ok) {
            audienceClasses = data.classes || [];
            const sel = document.getElementById('targetClassSelect');
            sel.innerHTML = '<option value="">-- انتخاب کلاس --</option>';
            audienceClasses.forEach(c => {
                sel.innerHTML += `<option value="${c.id}">${c.title} — استاد ${c.teacher_name || 'نامشخص'} (${c.student_count} دانش‌آموز)</option>`;
            });

            // Update option labels with counts
            const optTeachers = document.querySelector('#targetType option[value="teachers"]');
            if (optTeachers && data.stats) optTeachers.textContent = `کلیه اساتید فعال (${data.stats.teachers} نفر)`;
            const optStudents = document.querySelector('#targetType option[value="students"]');
            if (optStudents && data.stats) optStudents.textContent = `کلیه زبان‌آموزان فعال (${data.stats.students} نفر)`;
        }
    } catch (e) {
        console.error(e);
    }
}

function toggleSendTypeUI() {
    const val = document.getElementById('sendType').value;
    if (val === 'pattern') {
        document.getElementById('boxPatternFields').style.display = 'block';
        document.getElementById('boxSimpleText').style.display = 'none';
    } else {
        document.getElementById('boxPatternFields').style.display = 'none';
        document.getElementById('boxSimpleText').style.display = 'block';
    }
}

function toggleTargetTypeUI() {
    const val = document.getElementById('targetType').value;
    document.getElementById('boxClassSelect').style.display = (val === 'class') ? 'block' : 'none';
    document.getElementById('boxManualPhones').style.display = (val === 'manual') ? 'block' : 'none';
}

function updateCharCount() {
    const text = document.getElementById('simpleMessageText').value;
    const len = text.length;
    let parts = 1;
    if (len > 70) {
        parts = Math.ceil(len / 67);
    }
    document.getElementById('charCountLabel').textContent = `${len} کاراکتر (${parts} پارت پیامک)`;
}

// =========================================================
// 5. DISPATCH SMS
// =========================================================
async function handleSendSms(e) {
    e.preventDefault();
    if (!confirm('آیا از ارسال این پیامک‌ها اطمینان دارید؟')) return;

    const btn = document.getElementById('btnDispatchSms');
    btn.disabled = true;
    btn.textContent = 'در حال ارسال... لطفاً صبر کنید';

    const fd = new FormData();
    fd.append('action', 'send_sms');
    fd.append('send_type', document.getElementById('sendType').value);
    fd.append('target_type', document.getElementById('targetType').value);
    fd.append('class_id', document.getElementById('targetClassSelect').value);
    fd.append('manual_phones', document.getElementById('manualPhones').value);
    fd.append('pattern_code', document.getElementById('selectPatternCode').value);
    fd.append('message_text', document.getElementById('simpleMessageText').value);

    // Variables for pattern
    const varInputs = document.querySelectorAll('#dynamicVarsContainer input');
    varInputs.forEach(inp => {
        const match = inp.name.match(/vars\[(.*?)\]/);
        if (match && match[1]) {
            fd.append(`variables[${match[1]}]`, inp.value);
        }
    });

    try {
        const res = await fetch('ajax_sms_center.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.ok) {
            alert(`✅ عملیات ارسال به پایان رسید.\nموفق: ${data.success}\nناموفق: ${data.failed}\nکل گیرندگان: ${data.total}`);
            loadBalance();
        } else {
            alert('❌ خطا: ' + (data.error || 'ارسال انجام نشد'));
        }
    } catch (e) {
        alert('خطای اتصال به سرور');
    } finally {
        btn.disabled = false;
        btn.textContent = '📨 ارسال پیامک';
    }
}

// =========================================================
// 6. AUTOMATIONS
// =========================================================
async function loadAutomations() {
    const list = document.getElementById('automationsList');
    try {
        const res = await fetch('ajax_sms_center.php?action=get_automations');
        const data = await res.json();
        if (data.ok && Array.isArray(data.automations)) {
            renderAutomations(data.automations);
        } else {
            list.innerHTML = '<div class="text-danger py-3">خطا در بارگذاری اتومیشن‌ها</div>';
        }
    } catch (e) {
        list.innerHTML = '<div class="text-danger py-3">خطای شبکه</div>';
    }
}

function renderAutomations(automations) {
    const list = document.getElementById('automationsList');
    if (!automations.length) {
        list.innerHTML = '<div class="text-white-50 text-center py-4">هیچ اتومیشنی تعریف نشده است.</div>';
        return;
    }

    const triggerLabels = {
        'student_absent': 'غیبت دانش‌آموز (رول‌کال)',
        'student_late': 'تأخیر دانش‌آموز (رول‌کال)',
        'teacher_qc_rollcall': 'هشدار رول‌کال معوق استاد',
        'teacher_qc_finalmarks': 'هشدار نمرات پایانی ترم استاد',
        'birthday': 'تبریک سالروز تولد',
        'pre_class_reminder': 'یادآوری قبل کلاس',
    };

    let html = '';
    automations.forEach(a => {
        const isActive = Number(a.is_active) === 1;
        const triggerName = triggerLabels[a.trigger_event] || a.trigger_event;
        const timeNote = a.time_offset_min > 0 ? `+${a.time_offset_min} دقیقه بعد` : (a.time_offset_min < 0 ? `${a.time_offset_min} دقیقه قبل` : 'بلادرنگ');

        html += `
        <div class="auto-card ${isActive ? '' : 'inactive'}">
            <div class="d-flex align-items-center gap-3">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" ${isActive ? 'checked' : ''} onchange="toggleAutomation(${a.id}, this.checked)">
                </div>
                <div>
                    <div class="fw-bold text-white fs-6 mb-1">${a.title}</div>
                    <div class="d-flex align-items-center gap-2 flex-wrap text-white-50 small">
                        <span class="pill-badge pill-blue">رویداد: ${triggerName}</span>
                        <span class="pill-badge pill-yellow">مخاطب: ${a.target_audience}</span>
                        <span class="pill-badge pill-green">${timeNote}</span>
                        ${a.pattern_code ? `<span class="badge bg-secondary font-monospace">پترن: ${a.pattern_code}</span>` : ''}
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="deleteAutomation(${a.id})">
                    🗑️ حذف
                </button>
            </div>
        </div>
        `;
    });
    list.innerHTML = html;
}

async function toggleAutomation(id, isChecked) {
    const fd = new FormData();
    fd.append('action', 'toggle_automation');
    fd.append('id', id);
    fd.append('is_active', isChecked ? 1 : 0);
    try {
        await fetch('ajax_sms_center.php', { method: 'POST', body: fd });
        loadAutomations();
    } catch (e) {
        alert('خطا در تغییر وضعیت اتومیشن');
    }
}

function openNewAutomationModal() {
    document.getElementById('autoId').value = '0';
    document.getElementById('autoTitle').value = '';
    document.getElementById('autoPatternCode').value = '';
    document.getElementById('autoVarsMap').value = '';
    const modal = new bootstrap.Modal(document.getElementById('modalAutomation'));
    modal.show();
}

async function handleSaveAutomation(e) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('action', 'save_automation');
    fd.append('id', document.getElementById('autoId').value);
    fd.append('title', document.getElementById('autoTitle').value);
    fd.append('trigger_event', document.getElementById('autoTrigger').value);
    fd.append('target_audience', document.getElementById('autoAudience').value);
    fd.append('time_offset_min', document.getElementById('autoOffset').value);
    fd.append('pattern_code', document.getElementById('autoPatternCode').value);
    fd.append('variables_mapping', document.getElementById('autoVarsMap').value);

    try {
        const res = await fetch('ajax_sms_center.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.ok) {
            bootstrap.Modal.getInstance(document.getElementById('modalAutomation')).hide();
            loadAutomations();
        } else {
            alert('خطا: ' + (data.error || 'ذخیره نشد'));
        }
    } catch (e) {
        alert('خطای ارتباط با سرور');
    }
}

async function deleteAutomation(id) {
    if (!confirm('آیا از حذف این اتومیشن اطمینان دارید؟')) return;
    const fd = new FormData();
    fd.append('action', 'delete_automation');
    fd.append('id', id);
    try {
        const res = await fetch('ajax_sms_center.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.ok) {
            loadAutomations();
        } else {
            alert('خطا در حذف');
        }
    } catch (e) {
        alert('خطا در ارتباط با سرور');
    }
}

// =========================================================
// 7. LOGS & HISTORY
// =========================================================
async function loadLogs(page = 1) {
    currentLogPage = page;
    const filter = document.getElementById('logTypeFilter').value;
    const tbody = document.getElementById('logsTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-white-50">در حال دریافت تاریخچه پیامک‌ها...</td></tr>';

    try {
        const res = await fetch(`ajax_sms_center.php?action=get_logs&log_type=${filter}&page=${page}`);
        const data = await res.json();
        if (data.ok && Array.isArray(data.items)) {
            renderLogsTable(data.items);
            renderLogsPagination(data.page, data.total_pages);
        } else {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">خطا در دریافت لاگ‌ها: ${data.error || 'ناشناخته'}</td></tr>`;
        }
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">خطای ارتباطی</td></tr>';
    }
}

function renderLogsTable(items) {
    const tbody = document.getElementById('logsTableBody');
    if (!items.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-white-50 py-4">هیچ لاگی یافت نشد.</td></tr>';
        return;
    }

    let html = '';
    items.forEach(it => {
        const statusBadge = it.status === 'sent'
            ? '<span class="pill-badge pill-green">ارسال موفق</span>'
            : '<span class="pill-badge pill-red">ناموفق / خطا</span>';

        const roleBadge = it.role === 'teacher'
            ? '<span class="pill-badge pill-yellow">استاد</span>'
            : (it.role === 'parent' ? '<span class="pill-badge pill-blue">ولی دانش‌آموز</span>' : '<span class="pill-badge pill-green">دانش‌آموز</span>');

        html += `
        <tr>
            <td class="text-nowrap font-monospace text-white-50 small">${it.sent_at || '-'}</td>
            <td><span class="badge bg-secondary bg-opacity-50 text-white">${it.source_label || 'سیستم'}</span></td>
            <td>
                <div class="fw-bold text-white small">${it.recipient || 'نامشخص'}</div>
                <div class="mt-1">${roleBadge}</div>
            </td>
            <td class="font-monospace text-warning">${it.phone || '-'}</td>
            <td>
                <div class="small text-white-50 text-truncate" style="max-width: 320px;" title="${it.message_text || ''}">
                    ${it.message_text || (it.pattern_code ? `پترن ${it.pattern_code}` : '-')}
                </div>
            </td>
            <td>${statusBadge}</td>
        </tr>
        `;
    });
    tbody.innerHTML = html;
}

function renderLogsPagination(current, total) {
    document.getElementById('logsPaginationInfo').textContent = `صفحه ${current} از ${total}`;
    const btnContainer = document.getElementById('logsPaginationButtons');
    btnContainer.innerHTML = '';

    if (current > 1) {
        btnContainer.innerHTML += `<button class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="loadLogs(${current - 1})">قبلی</button>`;
    }
    if (current < total) {
        btnContainer.innerHTML += `<button class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="loadLogs(${current + 1})">بعدی</button>`;
    }
}
</script>
