# Ghazal Academy

Public snapshot of Ghazal Language Institute web systems. Each live DirectAdmin site is on its own branch. The `main` branch only documents the repo.

هر پنل روی یک برنچ جدا است. فایل‌های داخل هر برنچ از `public_html` زنده DirectAdmin کپی شده‌اند (بدون تغییر محتوا). رمزها، توکن‌ها، و فایل‌های دانش‌آموز در این ریپوی عمومی نیستند.

## Branches

| Branch | Live host | DirectAdmin root | Description |
|---|---|---|---|
| `ai` | AI.ghazalify.com | `domains/AI.ghazalify.com/public_html` | زیر دامنه AI |
| `pro` | pro.ghazalify.com | `domains/pro.ghazalify.com/public_html` | زیر دامنه pro |
| `announcement` | announcement.ghazalify.com | `domains/announcement.ghazalify.com/public_html` | اعلانات |
| `chat` | chat.ghazalify.com | `domains/chat.ghazalify.com/public_html` | چت |
| `accounting` | acccounting.ghazalify.com | `domains/acccounting.ghazalify.com/public_html` | پنل حسابداری |
| `bookstore` | books.ghazalify.com | `domains/books.ghazalify.com/public_html` | فروشگاه کتاب |
| `education` | panel.ghazalify.com | `domains/panel.ghazalify.com/public_html` | پنل آموزشی |
| `wordpress` | ghazalify.com | `domains/ghazalify.com/public_html` | سایت وردپرس |

## Secrets / PII

This repository is public. Credential files (`db.php`, `wp-config.php`, `*-config.php` secrets, `.env`, …) and student uploads (receipts, assignments, identity logs, login spreadsheets) are listed in each branch `.gitignore` and were not committed.

Revoke any GitHub personal access token that was used to create this clone.

## Layout

On every site branch, paths are relative to that site’s `public_html` (for example `index.php` is at the branch root).


---

## تازه ترین به‌روزرسانی‌ها (سیستم‌های فعال در برنچ education)

### ۱. سیستم جامع کنترل کیفیت اساتید (Teacher QC - 100 نمره)
- **موتور ارزیابی (includes/qc.php):** محاسبه خودکار نمرات کلاسی و ارزیابی سوپروایزر.
- **داشبورد استاد (dashboard/teacher/qc.php):** نمایش نمره نهایی Overall، کارت‌های تفکیکی ۶ گانه هر کلاس با چیدمان فلکس یکدست (Equal Heights) و ریزگزارش بازشونده، به همراه بازخورد متنی سوپروایزر.
- **داشبورد ارزیابی سوپروایزر (dashboard/supervisor/qc.php و jax_qc_save.php):** ثبت نمرات ۴ گانه ارزیابی مستقیم سوپروایزر (Observation, Poll, Subs, Courses & Attendance) و یادداشت بازخورد.
- **ممیزی جلسات ۱۳ گانه (EAS):** در dashboard/supervisor/eas.php بر پایه تقویم روزهای زوج/فرد و روزهای لغوشده.
- **سیستم صوتی ویس پلن ترم:** آپلود و استریم فایل‌های صوتی اساتید در جزئیات ترم‌پلن (	erm-plan-details.php).

### ۲. یکپارچه‌سازی پیامک خدماتی فراز اس‌ام‌اس (Faraz SMS)
- پترن اختصاصی بدون بلک‌لیست: کد cyedV1fZDy با متن محترمانه یادآوری عدم ثبت موارد آموزشی (%name%, %item%, %class%).
- سیستم ضداسپم (Deduplication) با ثبت لاگ یکتا در qc_sms_alerts_log و محدودسازی ارسال تنها در ساعات مجاز ۰۸:۰۰ الی ۲۲:۳۰.

### ۳. حساب‌های مترموست اساتید و هشدارهای رول‌کال
- ساخت حساب کاربری رسمی برای تمامی ۲۲ استاد با یوزرنیم [first_initial].[lastname] در تیم ghazal-institute و کانال 
oll-call.
- مانیتورینگ خودکار رأس دقیقه ۳۰ پس از شروع کلاس در dashboard/supervisor/cron_rollcall_alert.php.
