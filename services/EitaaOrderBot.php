<?php

require_once __DIR__ . '/EitaaUniomService.php';

final class EitaaOrderBot
{
    private PDO $pdo;
    private EitaaUniomService $api;
    private string $academyName;

    public function __construct(PDO $pdo, EitaaUniomService $api, string $academyName = 'آموزشگاه زبان')
    {
        $this->pdo = $pdo;
        $this->api = $api;
        $this->academyName = trim($academyName) ?: 'آموزشگاه زبان';
    }

    public function processUpdate(array $update): void
    {
        $updateId = isset($update['update_id']) ? (string)$update['update_id'] : '';
        if ($updateId !== '' && !$this->claimUpdate($updateId)) {
            return;
        }

        $message = $update['message'] ?? $update['edited_message'] ?? null;
        if (!is_array($message)) {
            return;
        }

        $chat = $message['chat'] ?? [];
        $from = $message['from'] ?? [];
        $chatId = (string)($chat['id'] ?? '');
        $userId = (string)($from['id'] ?? $chatId);
        $text = trim((string)($message['text'] ?? ''));

        if ($chatId === '' || $text === '') {
            return;
        }

        $chatType = (string)($chat['type'] ?? 'private');
        if ($chatType !== '' && $chatType !== 'private') {
            return;
        }

        $selfId = trim((string)get_setting('eitaa_self_id', ''));
        if ($selfId !== '' && ($userId === $selfId || $chatId === $selfId)) {
            return;
        }

        $normalized = $this->normalizeCommand($text);
        if (in_array($normalized, ['/start', 'start', 'شروع', 'منو', 'menu'], true)) {
            $this->resetConversation($chatId);
            $this->sendMainMenu($chatId);
            return;
        }

        if (in_array($normalized, ['لغو', 'انصراف', 'cancel', '/cancel'], true)) {
            $this->resetConversation($chatId);
            $this->api->sendMessage($chatId, "فرآیند جاری لغو شد.\n\nبرای شروع دوباره «شروع» را ارسال کنید.");
            return;
        }

        $conv = $this->getConversation($chatId);
        $step = $conv['step'] ?? 'menu';
        $data = $conv['data'] ?? [];

        switch ($step) {
            case 'menu':
                $this->handleMenu($chatId, $text);
                break;
            case 'first_name':
                $this->handleFirstName($chatId, $text, $data);
                break;
            case 'last_name':
                $this->handleLastName($chatId, $text, $data);
                break;
            case 'mobile':
                $this->handleMobile($chatId, $text, $data);
                break;
            case 'term':
                $this->handleTerm($chatId, $text, $data);
                break;
            case 'confirm_order':
                $this->handleConfirm($chatId, $text, $userId, $data);
                break;
            case 'tracking':
                $this->handleTracking($chatId, $text);
                break;
            default:
                $this->resetConversation($chatId);
                $this->sendMainMenu($chatId);
        }
    }

    private function handleMenu(string $chatId, string $text): void
    {
        $choice = $this->normalizeDigits(trim($text));
        if (in_array($choice, ['1', '۱', 'ثبت سفارش', 'سفارش', 'پیش سفارش', 'پیش‌سفارش'], true)) {
            $this->setConversation($chatId, 'first_name', []);
            $this->api->sendMessage($chatId, "لطفاً نام خود را ارسال کنید.\n\nبرای انصراف «لغو» را بفرستید.");
            return;
        }
        if (in_array($choice, ['2', '۲', 'پیگیری', 'پیگیری سفارش'], true)) {
            $this->setConversation($chatId, 'tracking', []);
            $this->api->sendMessage($chatId, "کد رهگیری سفارش را ارسال کنید.\nنمونه: BK-260906-AB12");
            return;
        }
        $this->sendMainMenu($chatId);
    }

    private function handleFirstName(string $chatId, string $text, array $data): void
    {
        if (!$this->validName($text)) {
            $this->api->sendMessage($chatId, 'نام واردشده معتبر نیست. لطفاً فقط نام خود را ارسال کنید.');
            return;
        }
        $data['first_name'] = $text;
        $this->setConversation($chatId, 'last_name', $data);
        $this->api->sendMessage($chatId, 'نام خانوادگی خود را ارسال کنید.');
    }

    private function handleLastName(string $chatId, string $text, array $data): void
    {
        if (!$this->validName($text)) {
            $this->api->sendMessage($chatId, 'نام خانوادگی واردشده معتبر نیست. لطفاً دوباره ارسال کنید.');
            return;
        }
        $data['last_name'] = $text;
        $this->setConversation($chatId, 'mobile', $data);
        $this->api->sendMessage($chatId, "شماره همراه خود را با 09 ارسال کنید.\nمثال: 09123456789");
    }

    private function handleMobile(string $chatId, string $text, array $data): void
    {
        $mobile = normalize_mobile($text);
        if (!preg_match('/^09\d{9}$/', $mobile)) {
            $this->api->sendMessage($chatId, 'شماره همراه معتبر نیست. شماره را به شکل 09123456789 ارسال کنید.');
            return;
        }
        $data['mobile'] = $mobile;
        $terms = $this->activeTerms();
        if (!$terms) {
            $this->resetConversation($chatId);
            $this->api->sendMessage($chatId, 'در حال حاضر هیچ ترم فعالی برای ثبت سفارش وجود ندارد. لطفاً بعداً دوباره تلاش کنید.');
            return;
        }

        $data['term_ids'] = array_map(static fn($t) => (int)$t['id'], $terms);
        $this->setConversation($chatId, 'term', $data);
        $this->api->sendMessage($chatId, $this->termsMessage($terms));
    }

    private function handleTerm(string $chatId, string $text, array $data): void
    {
        $terms = $this->activeTerms();
        if (!$terms) {
            $this->resetConversation($chatId);
            $this->api->sendMessage($chatId, 'در حال حاضر هیچ ترم فعالی وجود ندارد.');
            return;
        }

        $choice = (int)$this->normalizeDigits($text);
        if ($choice < 1 || $choice > count($terms)) {
            $this->api->sendMessage($chatId, "شماره ترم معتبر نیست.\n\n" . $this->termsMessage($terms));
            return;
        }

        $term = $terms[$choice - 1];
        $items = $this->termBooks((int)$term['id']);
        if (!$items) {
            $this->api->sendMessage($chatId, 'برای این ترم هنوز کتابی تعریف نشده است. لطفاً یک ترم دیگر انتخاب کنید.');
            return;
        }

        $data['term_id'] = (int)$term['id'];
        $data['term_title'] = $term['title'];
        $this->setConversation($chatId, 'confirm_order', $data);

        $lines = [];
        foreach ($items as $item) {
            $qty = max(1, (int)$item['quantity']);
            $lines[] = '• ' . $item['title'] . ($qty > 1 ? " × {$qty}" : '');
        }

        $summary = "لطفاً اطلاعات پیش‌سفارش را بررسی کنید:\n\n";
        $summary .= "نام: {$data['first_name']} {$data['last_name']}\n";
        $summary .= "موبایل: {$data['mobile']}\n";
        $summary .= "ترم: {$term['title']}\n\nکتاب‌ها:\n" . implode("\n", $lines);
        $summary .= "\n\n1) تأیید و ثبت پیش‌سفارش\n2) لغو";
        $this->api->sendMessage($chatId, $summary);
    }

    private function handleConfirm(string $chatId, string $text, string $userId, array $data): void
    {
        $choice = $this->normalizeDigits(trim($text));
        if (in_array($choice, ['2', 'لغو', 'انصراف'], true)) {
            $this->resetConversation($chatId);
            $this->api->sendMessage($chatId, 'پیش‌سفارش لغو شد. برای شروع دوباره «شروع» را ارسال کنید.');
            return;
        }
        if (!in_array($choice, ['1', 'تایید', 'تأیید', 'ثبت', 'بله'], true)) {
            $this->api->sendMessage($chatId, "برای ثبت سفارش عدد 1 و برای لغو عدد 2 را ارسال کنید.");
            return;
        }

        foreach (['first_name','last_name','mobile','term_id'] as $required) {
            if (empty($data[$required])) {
                $this->resetConversation($chatId);
                $this->api->sendMessage($chatId, 'اطلاعات این پیش‌سفارش کامل نیست. لطفاً «شروع» را ارسال و دوباره ثبت کنید.');
                return;
            }
        }

        try {
            $this->pdo->beginTransaction();
            $result = create_order_record($this->pdo, [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'mobile' => $data['mobile'],
                'term_id' => (int)$data['term_id'],
                'eitaa_user_id' => $userId,
                'source' => 'eitaa',
            ]);
            $this->pdo->commit();
            $this->resetConversation($chatId);

            $message = "✅ پیش‌سفارش شما با موفقیت ثبت شد.\n\n";
            $message .= "کد رهگیری: {$result['tracking_code']}\n";
            $message .= "ترم: {$result['term_title']}\n";
            $message .= "وضعیت: در انتظار تأیید آموزشگاه\n\n";
            $message .= "کد رهگیری را نگه دارید. برای مشاهده وضعیت، «پیگیری» را ارسال کنید.";
            $this->api->sendMessage($chatId, $message);
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Eitaa order error: ' . $e->getMessage());
            $this->api->sendMessage($chatId, 'ثبت پیش‌سفارش با خطا مواجه شد. لطفاً چند دقیقه بعد دوباره تلاش کنید.');
        }
    }

    private function handleTracking(string $chatId, string $text): void
    {
        $tracking = strtoupper(trim($this->latinize($text)));
        if ($tracking === '') {
            $this->api->sendMessage($chatId, 'کد رهگیری را ارسال کنید.');
            return;
        }
        $stmt = $this->pdo->prepare('SELECT tracking_code,first_name,last_name,term_title_snapshot,status,created_at FROM orders WHERE tracking_code=? LIMIT 1');
        $stmt->execute([$tracking]);
        $order = $stmt->fetch();
        if (!$order) {
            $this->api->sendMessage($chatId, "سفارشی با این کد رهگیری پیدا نشد.\nکد را بررسی و دوباره ارسال کنید، یا «شروع» را بفرستید.");
            return;
        }
        $this->resetConversation($chatId);
        $label = status_label($order['status']);
        $msg = "وضعیت سفارش شما:\n\n";
        $msg .= "کد رهگیری: {$order['tracking_code']}\n";
        $msg .= "نام: {$order['first_name']} {$order['last_name']}\n";
        $msg .= "ترم: {$order['term_title_snapshot']}\n";
        $msg .= "وضعیت: {$label}\n\nبرای بازگشت به منو «شروع» را ارسال کنید.";
        $this->api->sendMessage($chatId, $msg);
    }

    private function sendMainMenu(string $chatId): void
    {
        $text = "سلام 👋\nبه سامانه پیش‌سفارش کتاب {$this->academyName} خوش آمدید.\n\n";
        $text .= "1) ثبت پیش‌سفارش کتاب\n2) پیگیری سفارش\n\n";
        $text .= "شماره گزینه را ارسال کنید.";
        $this->setConversation($chatId, 'menu', []);
        $this->api->sendMessage($chatId, $text);
    }

    private function activeTerms(): array
    {
        return $this->pdo->query("SELECT id,title FROM terms WHERE is_active=1 ORDER BY sort_order,id")->fetchAll() ?: [];
    }

    private function termBooks(int $termId): array
    {
        $stmt = $this->pdo->prepare('SELECT b.id,b.title,tb.quantity FROM term_books tb JOIN books b ON b.id=tb.book_id WHERE tb.term_id=? AND b.is_active=1 ORDER BY b.title');
        $stmt->execute([$termId]);
        return $stmt->fetchAll() ?: [];
    }

    private function termsMessage(array $terms): string
    {
        $lines = ["ترم خود را انتخاب کنید:"];
        foreach ($terms as $i => $term) {
            $lines[] = ($i + 1) . ') ' . $term['title'];
        }
        $lines[] = "\nفقط شماره ترم را ارسال کنید.";
        return implode("\n", $lines);
    }

    private function claimUpdate(string $updateId): bool
    {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO eitaa_updates (update_id,processed_at) VALUES (?,NOW())');
            $stmt->execute([$updateId]);
            return true;
        } catch (PDOException $e) {
            if ((string)$e->getCode() === '23000') {
                return false;
            }
            throw $e;
        }
    }

    private function getConversation(string $chatId): array
    {
        $stmt = $this->pdo->prepare('SELECT step,data_json FROM eitaa_conversations WHERE chat_id=? LIMIT 1');
        $stmt->execute([$chatId]);
        $row = $stmt->fetch();
        if (!$row) {
            return ['step' => 'menu', 'data' => []];
        }
        $data = json_decode((string)$row['data_json'], true);
        return ['step' => $row['step'], 'data' => is_array($data) ? $data : []];
    }

    private function setConversation(string $chatId, string $step, array $data): void
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $stmt = $this->pdo->prepare('INSERT INTO eitaa_conversations (chat_id,step,data_json,updated_at) VALUES (?,?,?,NOW()) ON DUPLICATE KEY UPDATE step=VALUES(step),data_json=VALUES(data_json),updated_at=NOW()');
        $stmt->execute([$chatId, $step, $json]);
    }

    private function resetConversation(string $chatId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM eitaa_conversations WHERE chat_id=?');
        $stmt->execute([$chatId]);
    }

    private function validName(string $text): bool
    {
        $text = trim($text);
        $len = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
        if ($len < 2 || $len > 80) return false;
        return !preg_match('/[0-9۰-۹٠-٩@<>={}\[\]]/u', $text);
    }

    private function normalizeCommand(string $text): string
    {
        $text = trim($text);
        if (function_exists('mb_strtolower')) {
            $text = mb_strtolower($text, 'UTF-8');
        } else {
            $text = strtolower($text);
        }
        return $text;
    }

    private function normalizeDigits(string $text): string
    {
        return strtr($text, [
            '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
            '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9',
        ]);
    }

    private function latinize(string $text): string
    {
        return $this->normalizeDigits($text);
    }
}
