<?php
// تعریف متغیر برای نمایش پیام وضعیت
$status_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // دریافت و پاک‌سازی اطلاعات
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $mobile = htmlspecialchars(trim($_POST['mobile']));
    
    if (!empty($full_name) && !empty($mobile)) {
        
        // نام فایل اکسلی که قرار است ساخته شود
        $file_name = 'tkt_registrations.csv';
        
        // بررسی اینکه آیا فایل از قبل وجود دارد یا خیر (برای نوشتن هدر ستون‌ها)
        $file_exists = file_exists($file_name);
        
        // باز کردن فایل در حالت Append (اضافه کردن به انتهای فایل)
        $file = fopen($file_name, 'a');
        
        // اگر فایل تازه ساخته شده، BOM و عنوان ستون‌ها را بنویس تا فارسی در اکسل به هم نریزد
        if (!$file_exists) {
            fputs($file, "\xEF\xBB\xBF"); 
            fputcsv($file, ['نام و نام خانوادگی', 'شماره موبایل', 'تاریخ و ساعت ثبت‌نام']);
        }
        
        // تنظیم تایم‌زون روی تهران برای ثبت دقیق زمان
        date_default_timezone_set('Asia/Tehran');
        $current_date = date('Y-m-d H:i:s');
        
        // نوشتن اطلاعات کاربر جدید در یک سطر جدید اکسل
        fputcsv($file, [$full_name, $mobile, $current_date]);
        
        // بستن فایل
        fclose($file);

        // پیام موفقیت‌آمیز
        $status_message = "<div class='alert success'>ثبت‌نام اولیه شما برای دوره TKT با موفقیت انجام شد.</div>";
        
    } else {
        // پیام خطا
        $status_message = "<div class='alert error'>لطفاً نام و شماره موبایل خود را وارد کنید.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت‌نام دوره تخصصی TKT</title>
    <style>
        /* تعریف متغیرهای رنگی */
        :root {
            --primary-red: #ff003c;
            --dark-red: #8b0000;
            --bg-color: #050505;
            --glass-bg: rgba(20, 20, 20, 0.4);
            --glass-border: rgba(255, 0, 60, 0.2);
            --text-color: #f0f0f0;
        }

        /* تنظیمات پایه صفحه */
        body {
            margin: 0;
            min-height: 100vh;
            background-color: var(--bg-color);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Tahoma, 'Segoe UI', sans-serif;
            color: var(--text-color);
            overflow: hidden;
            position: relative;
        }

        /* افکت‌های مایع پس‌زمینه (Liquid Blobs) */
        .blob {
            position: absolute;
            filter: blur(90px);
            z-index: -1;
            border-radius: 50%;
            animation: float 10s infinite alternate ease-in-out;
        }

        .blob-1 {
            width: 450px;
            height: 450px;
            background: linear-gradient(135deg, var(--primary-red), var(--dark-red));
            top: -150px;
            left: -100px;
        }

        .blob-2 {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, #4a0000, var(--primary-red));
            bottom: -100px;
            right: -50px;
            animation-delay: -5s;
        }

        /* کانتینر شیشه‌ای (Glassmorphism) */
        .form-container {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            width: 90%;
            max-width: 420px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.6), inset 0 0 20px rgba(255, 0, 60, 0.05);
            z-index: 1;
            position: relative;
        }

        /* تایپوگرافی فرم */
        .form-container h2 {
            text-align: center;
            color: #fff;
            margin-top: 0;
            margin-bottom: 30px;
            font-weight: bold;
            letter-spacing: 1px;
            text-shadow: 0 0 15px rgba(255, 0, 60, 0.5);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #ccc;
            letter-spacing: 0.5px;
        }

        /* فیلدهای ورودی */
        .form-group input {
            width: 100%;
            padding: 14px;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-family: inherit;
            font-size: 15px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-red);
            background: rgba(0, 0, 0, 0.6);
            box-shadow: 0 0 15px rgba(255, 0, 60, 0.3);
        }

        /* دکمه ارسال شیشه‌ای و درخشان */
        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #ff003c, #b3002a);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 0, 60, 0.4);
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(255, 0, 60, 0.6);
        }

        /* پیام‌های وضعیت */
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
            text-align: center;
            backdrop-filter: blur(10px);
            line-height: 1.6;
        }

        .success {
            background-color: rgba(0, 255, 100, 0.1);
            color: #00ff64;
            border: 1px solid rgba(0, 255, 100, 0.2);
            text-shadow: 0 0 10px rgba(0, 255, 100, 0.2);
        }

        .error {
            background-color: rgba(255, 0, 60, 0.1);
            color: #ff4d6d;
            border: 1px solid rgba(255, 0, 60, 0.2);
        }

        /* کی‌فریم انیمیشن مایع */
        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(40px, 40px) scale(1.1); }
        }
    </style>
</head>
<body>

    <!-- افکت‌های بک‌گراند -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="form-container">
        <h2>ثبت‌نام دوره TKT</h2>
        
        <?php echo $status_message; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="full_name">نام و نام خانوادگی</label>
                <input type="text" id="full_name" name="full_name" placeholder="مثلاً: علی احمدی" required>
            </div>
            
            <div class="form-group">
                <label for="mobile">شماره موبایل</label>
                <input type="tel" id="mobile" name="mobile" placeholder="09123456789" dir="ltr" required>
            </div>

            <button type="submit" class="submit-btn">ثبت اطلاعات و رزرو</button>
        </form>
    </div>

</body>
</html>