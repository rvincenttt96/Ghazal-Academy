# Fiura Pay for WooCommerce

درگاه پرداخت رمزارزی [Fiura](https://fiurapay.com) برای فروشگاه ووکامرس.

نسخهٔ ZIP که از `fiurapay.com/woocommerce` دانلود می‌شود با **جداکنندهٔ مسیر ویندوزی** (`\`) ساخته شده. وردپرس روی هاست لینوکس آن را به پوشه‌ای تو در تو باز می‌کند و هنگام فعال‌سازی خطای **«پرونده افزونه پیدا نشد»** می‌دهد.

این بسته همان افزونه است، با مسیرهای یونیکس (`/`) که وردپرس انتظار دارد:

```
fiurapay-woocommerce/fiurapay-woocommerce.php
fiurapay-woocommerce/includes/...
fiurapay-woocommerce/assets/js/blocks.js
```

## نصب

1. اگر نصب قبلی خراب است، از FTP یا فایل‌منیجر این پوشه‌ها را از `wp-content/plugins/` حذف کنید:
   - `fiurapay-woocommerce`
   - `fiurapay-woocommerce-1`
2. فایل `fiurapay-woocommerce.zip` همین مخزن را در **افزونه‌ها → افزودن → بارگذاری افزونه** آپلود کنید.
3. فعال کنید، سپس **ووکامرس → پیکربندی → پرداخت‌ها → Fiura Pay**.
4. کلید `ath_test_` یا `ath_live_` و راز webhook را از [داشبورد Fiura](https://fiurapay.com/login) بچسبانید.
5. آدرس webhook فروشگاه را در Fiura ثبت کنید:

```
https://YOUR-SHOP/?wc-api=fiura_pay
```

کلید API، اتصال کیف تسویه و چرخش راز webhook فقط روی fiurapay.com انجام می‌شود. این افزونه فاکتور می‌سازد و مشتری را به `/d/:id` هدایت می‌کند.

تأیید سفارش با رویداد `invoice.settled` و HMAC روی بدنهٔ خام POST است (`X-Athura-Timestamp` / `X-Athura-Signature`).

نیازمندی‌ها: وردپرس ۶.۴+، ووکامرس ۸+، PHP ۸.۱+.
