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
