<?php
/**
 * send.php — отправка формы записи на курс через PHPMailer + SMTP Mail.ru
 * Принимает POST от формы на index.html, отправляет письмо на tvoiokruzeni@ya.ru
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* ============================================================
   1. ПОДКЛЮЧЕНИЕ PHPMailer
   ============================================================ */

// Вариант А — через Composer (рекомендуется)
require __DIR__ . '/vendor/autoload.php';

// Вариант Б — вручную (раскомментируй, если не используешь Composer,
//                  а строку с autoload.php выше — закомментируй)
// require __DIR__ . '/PHPMailer/src/PHPMailer.php';
// require __DIR__ . '/PHPMailer/src/SMTP.php';
// require __DIR__ . '/PHPMailer/src/Exception.php';

/* ============================================================
   2. НАСТРОЙКИ SMTP MAIL.RU
   ============================================================ */

$smtpHost   = 'smtp.mail.ru';
$smtpPort   = 465;                          // 465 = SSL, 587 = TLS
$smtpSecure = 'ssl';                        // 'ssl' для 465, 'tls' для 587
$smtpUser   = 'tvoe_nt_from@mail.ru';         // ← замени на свой ящик Mail.ru
$smtpPass   = 'riqvmsE19MxCyaIOlVZD';      // ← пароль приложения (НЕ обычный!)

/* ============================================================
   3. КОМУ ОТПРАВЛЯЕМ
   ============================================================ */

$toEmail = 'tvoiokruzeni@ya.ru';
$toName  = 'Твоё Окружение';

/* ============================================================
   4. ЗАГОЛОВКИ И ПРИЁМ ДАННЫХ
   ============================================================ */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Метод не разрешён'], JSON_UNESCAPED_UNICODE);
    exit;
}

function clean($v) {
    return htmlspecialchars(trim((string)$v), ENT_QUOTES, 'UTF-8');
}

$fullName  = clean($_POST['fullName']  ?? '');
$childName = clean($_POST['childName'] ?? '');
$childAge  = clean($_POST['childAge']  ?? '');
$direction = clean($_POST['direction'] ?? '');
$phone     = clean($_POST['phone']     ?? '');

/* ============================================================
   5. ВАЛИДАЦИЯ
   ============================================================ */

$errors = [];
if ($fullName  === '') $errors[] = 'не указано имя родителя';
if ($childName === '') $errors[] = 'не указано имя ребёнка';
if ($childAge  === '') $errors[] = 'не указан возраст ребёнка';
if ($direction === '') $errors[] = 'не выбрано направление';
if ($phone     === '') $errors[] = 'не указан телефон';

if ($errors) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => implode(', ', $errors)], JSON_UNESCAPED_UNICODE);
    exit;
}

/* ============================================================
   6. ФОРМИРУЕМ ПИСЬМО
   ============================================================ */

$subject = 'Заявка на курс — ' . $direction . ' (' . $childName . ')';

$bodyHtml = '
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; background:#F5F0FF; padding:24px; color:#1A1A2E;">
  <div style="max-width:600px; margin:0 auto; background:#fff; border-radius:20px; overflow:hidden; box-shadow:0 8px 40px rgba(108,99,249,0.2);">
    <div style="background:linear-gradient(135deg,#6C63F9 0%,#5A52D9 50%,#4A42C9 100%); padding:28px 32px; color:#fff;">
      <h2 style="margin:0; font-size:22px;">📝 Новая заявка на курс</h2>
      <p style="margin:6px 0 0; opacity:0.85; font-size:14px;">Твоё Окружение — онлайн-класс</p>
    </div>
    <div style="padding:28px 32px;">
      <table style="width:100%; border-collapse:collapse; font-size:15px;">
        <tr>
          <td style="padding:10px 0; color:#6B7A8F; width:180px;">Имя родителя:</td>
          <td style="padding:10px 0; font-weight:600;">' . $fullName . '</td>
        </tr>
        <tr>
          <td style="padding:10px 0; color:#6B7A8F;">Имя ребёнка:</td>
          <td style="padding:10px 0; font-weight:600;">' . $childName . '</td>
        </tr>
        <tr>
          <td style="padding:10px 0; color:#6B7A8F;">Возраст ребёнка:</td>
          <td style="padding:10px 0; font-weight:600;">' . $childAge . '</td>
        </tr>
        <tr>
          <td style="padding:10px 0; color:#6B7A8F;">Направление:</td>
          <td style="padding:10px 0; font-weight:600;">' . $direction . '</td>
        </tr>
        <tr>
          <td style="padding:10px 0; color:#6B7A8F;">Телефон:</td>
          <td style="padding:10px 0; font-weight:600;">
            <a href="tel:' . $phone . '" style="color:#6C63F9;">' . $phone . '</a>
          </td>
        </tr>
      </table>
      <hr style="border:none; border-top:1px solid #E8ECF1; margin:20px 0;">
      <p style="color:#6B7A8F; font-size:13px; margin:0;">Заявка отправлена: ' . date('d.m.Y H:i') . '</p>
    </div>
  </div>
</body>
</html>';

$bodyAlt = "Новая заявка на курс\n\n"
         . "Имя родителя: {$fullName}\n"
         . "Имя ребёнка: {$childName}\n"
         . "Возраст: {$childAge}\n"
         . "Направление: {$direction}\n"
         . "Телефон: {$phone}\n"
         . "Дата: " . date('d.m.Y H:i') . "\n";

/* ============================================================
   7. ОТПРАВКА
   ============================================================ */

$mail = new PHPMailer(true);

try {
    // --- SMTP-настройки ---
    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    $mail->SMTPSecure = $smtpSecure;
    $mail->Port       = $smtpPort;
    $mail->CharSet    = 'UTF-8';
    $mail->Encoding   = 'base64';

    // --- Отладка (включи при проблемах, потом выключи) ---
    // $mail->SMTPDebug   = 2;
    // $mail->Debugoutput = 'echo';

    // --- От кого / кому ---
    $mail->setFrom($smtpUser, 'Твоё Окружение');
    $mail->addAddress($toEmail, $toName);
    $mail->addReplyTo($smtpUser, $fullName);

    // --- Содержимое ---
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $bodyHtml;
    $mail->AltBody = $bodyAlt;

    $mail->send();

    echo json_encode(['ok' => true, 'message' => 'Заявка отправлена!'], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => 'Ошибка отправки: ' . $mail->ErrorInfo
    ], JSON_UNESCAPED_UNICODE);
}
