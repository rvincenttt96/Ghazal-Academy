<?php
$title = 'Material';
$current_page = 'material.php';
require_once 'layout.php';
require_once '../../includes/db.php';
require_role('teacher');

$teacher_username = trim((string)($user_data['username'] ?? $_SESSION['username'] ?? ''));
$teacher_fullname = trim((string)($user_data['full_name'] ?? $_SESSION['full_name'] ?? 'معلم گرامی'));
$cloud_url = 'https://cloud.ghazalify.com';
$password = $teacher_username; // کلمه عبور برابر با نام کاربری تعیین شده است
?>

<style>
.material-container {
    max-width: 1000px;
    margin: 0 auto;
}
.material-hero {
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.7), rgba(15, 23, 42, 0.85));
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 20px;
    padding: 2rem;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.35);
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}
.material-hero::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(14, 165, 233, 0.35) 0%, rgba(14, 165, 233, 0) 70%);
    pointer-events: none;
}
.material-hero-title {
    color: #fff;
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 12px;
}
.material-hero-subtitle {
    color: #94a3b8;
    font-size: 0.95rem;
    line-height: 1.7;
    margin-bottom: 0;
}
.cloud-badge {
    background: rgba(14, 165, 233, 0.2);
    color: #38bdf8;
    border: 1px solid rgba(56, 189, 248, 0.35);
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 600;
}
.credentials-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 1.75rem;
    backdrop-filter: blur(12px);
    margin-bottom: 1.5rem;
    transition: transform 0.2s, border-color 0.2s;
}
.credentials-card:hover {
    border-color: rgba(56, 189, 248, 0.3);
}
.cred-row {
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 0.85rem 1.2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    gap: 10px;
}
.cred-label {
    color: #94a3b8;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 130px;
}
.cred-value {
    color: #f1f5f9;
    font-size: 1.05rem;
    font-weight: 600;
    font-family: monospace, 'IRANSansWeb', sans-serif;
    letter-spacing: 0.5px;
    direction: ltr;
    text-align: left;
    flex-grow: 1;
    word-break: break-all;
}
.btn-copy {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #e2e8f0;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.btn-copy:hover {
    background: rgba(56, 189, 248, 0.2);
    border-color: #38bdf8;
    color: #38bdf8;
}
.btn-copy.copied {
    background: rgba(34, 197, 94, 0.2);
    border-color: #22c55e;
    color: #4ade80;
}
.btn-login-cloud {
    background: linear-gradient(135deg, #0284c7, #0369a1);
    color: #fff !important;
    border: none;
    border-radius: 12px;
    padding: 14px 28px;
    font-size: 1.05rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-decoration: none;
    box-shadow: 0 8px 20px rgba(2, 132, 199, 0.4);
    transition: all 0.25s ease;
    width: 100%;
}
.btn-login-cloud:hover {
    background: linear-gradient(135deg, #0369a1, #075985);
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(2, 132, 199, 0.55);
}
.info-box-item {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 14px;
    padding: 1.25rem;
    height: 100%;
    transition: background 0.2s;
}
.info-box-item:hover {
    background: rgba(255, 255, 255, 0.05);
}
.info-box-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: rgba(14, 165, 233, 0.15);
    color: #38bdf8;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.9rem;
}
.info-box-title {
    color: #f1f5f9;
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.4rem;
}
.info-box-desc {
    color: #94a3b8;
    font-size: 0.88rem;
    line-height: 1.65;
    margin-bottom: 0;
}
.toast-notice {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%) translateY(100px);
    background: #0f172a;
    color: #4ade80;
    border: 1px solid #22c55e;
    padding: 12px 24px;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    font-size: 0.95rem;
    font-weight: 600;
    transition: transform 0.3s ease, opacity 0.3s ease;
    opacity: 0;
    z-index: 9999;
    pointer-events: none;
}
.toast-notice.show {
    transform: translateX(-50%) translateY(0);
    opacity: 1;
}
</style>

<div class="material-container">

    <!-- Hero Card -->
    <div class="material-hero">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-2">
            <h1 class="material-hero-title">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                    <polyline points="12 11 12 17"></polyline>
                    <polyline points="9 14 12 17 15 14"></polyline>
                </svg>
                منابع و جزوات آموزشی (Material)
            </h1>
            <span class="cloud-badge">Nextcloud Enterprise</span>
        </div>
        <p class="material-hero-subtitle">
            همکار گرامی، <strong><?= htmlspecialchars($teacher_fullname) ?></strong>؛ شما می‌توانید از طریق فضای ابری امن و پرسرعت آکادمی به پوشه <strong>Material</strong>، فایل‌ها و طرح‌درس‌های به‌روز دسترسی داشته و آن‌ها را با نهایت سرعت دانلود نمایید.
        </p>
    </div>

    <!-- Credentials & Direct Access Card -->
    <div class="credentials-card">
        <h3 class="text-white fs-5 fw-bold mb-3 d-flex align-items-center gap-2">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            اطلاعات ورود به سامانه ابری شما
        </h3>

        <!-- Cloud URL -->
        <div class="cred-row">
            <div class="cred-label">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                آدرس سامانه:
            </div>
            <div class="cred-value" id="valCloudUrl"><?= htmlspecialchars($cloud_url) ?></div>
            <button class="btn-copy" onclick="copyText('<?= htmlspecialchars($cloud_url) ?>', this)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                کپی
            </button>
        </div>

        <!-- Username -->
        <div class="cred-row">
            <div class="cred-label">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                نام کاربری:
            </div>
            <div class="cred-value" id="valUsername"><?= htmlspecialchars($teacher_username) ?></div>
            <button class="btn-copy" onclick="copyText('<?= htmlspecialchars($teacher_username) ?>', this)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                کپی
            </button>
        </div>

        <!-- Password -->
        <div class="cred-row">
            <div class="cred-label">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                کلمه عبور:
            </div>
            <div class="cred-value" id="valPassword"><?= htmlspecialchars($password) ?></div>
            <button class="btn-copy" onclick="copyText('<?= htmlspecialchars($password) ?>', this)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                کپی
            </button>
        </div>

        <!-- Big Launch Button -->
        <div class="mt-4">
            <a href="<?= htmlspecialchars($cloud_url) ?>" target="_blank" rel="noopener noreferrer" class="btn-login-cloud">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
                ورود به سامانه متریال آموزشی (cloud.ghazalify.com)
            </a>
        </div>
    </div>

    <!-- Feature & Guidance Cards -->
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="info-box-item">
                <div class="info-box-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <h4 class="info-box-title">پوشه اشتراکی Material</h4>
                <p class="info-box-desc">
                    به محض ورود به نکست‌کلود، پوشه <strong>Material</strong> به صورت اشتراکی در حساب شما قرار دارد و آخرین فایل‌ها و کتاب‌ها از طریق آن قابل دریافت است.
                </p>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="info-box-item">
                <div class="info-box-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="8 12 12 16 16 12"></polyline>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                    </svg>
                </div>
                <h4 class="info-box-title">دانلود سریع و بدون محدودیت</h4>
                <p class="info-box-desc">
                    امکان دانلود مستقیم فایل‌ها به صورت تکی یا پوشه‌ای (فایل زیپ) با بالاترین پهنای باند و بدون نیاز به ابزارهای جانبی فراهم است.
                </p>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="info-box-item">
                <div class="info-box-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                        <line x1="12" y1="18" x2="12.01" y2="18"></line>
                    </svg>
                </div>
                <h4 class="info-box-title">نرم‌افزار موبایل و دسکتاپ</h4>
                <p class="info-box-desc">
                    می‌توانید اپلیکیشن <strong>Nextcloud</strong> را روی گوشی (Android / iOS) یا رایانه نصب کرده و با همین اطلاعات به سادگی متصل شوید.
                </p>
            </div>
        </div>
    </div>

</div>

<!-- Copy toast notification -->
<div id="copyToast" class="toast-notice">✓ در کلیپ‌بورد کپی شد</div>

<script>
function copyText(text, btn) {
    if (!navigator.clipboard) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showCopyFeedback(btn);
        return;
    }
    navigator.clipboard.writeText(text).then(function() {
        showCopyFeedback(btn);
    }).catch(function() {
        showCopyFeedback(btn);
    });
}

function showCopyFeedback(btn) {
    const origHtml = btn.innerHTML;
    btn.classList.add('copied');
    btn.innerHTML = '✓ کپی شد';
    
    const toast = document.getElementById('copyToast');
    if (toast) {
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2200);
    }
    
    setTimeout(() => {
        btn.classList.remove('copied');
        btn.innerHTML = origHtml;
    }, 2000);
}
</script>

<?php require_once 'layout-end.php'; ?>
