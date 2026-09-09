<?php
// ============================================================
// НАСТРОЙКИ - ЗАПОЛНИТЕ ТОЛЬКО ЭТИ 4 СТРОЧКИ!
// ============================================================

$mailru_email = 'start_tuapse_from@mail.ru';      // ВАШ EMAIL на mail.ru
$mailru_password = 'z5nGftAa9YaSyTyShRAY';  // ПАРОЛЬ ПРИЛОЖЕНИЯ (см. инструкцию ниже)
$to_email = 'start_tuapse_fromк@mail.ru';          // КУДА приходят письма (можно тот же)
$site_name = 'Школа-студия STARt';       // Имя отправителя

// ============================================================
// ДАЛЬШЕ НИЧЕГО НЕ МЕНЯЙТЕ
// ============================================================

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json; charset=utf-8');

// Получаем данные из формы
$fullName = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
$childName = isset($_POST['childName']) ? trim($_POST['childName']) : '';
$childAge = isset($_POST['childAge']) ? trim($_POST['childAge']) : '';
$direction = isset($_POST['direction']) ? trim($_POST['direction']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

// Проверка обязательных полей
if (empty($fullName) || empty($childName) || empty($childAge) || empty($direction) || empty($phone)) {
    echo json_encode([
        'success' => false,
        'message' => 'Пожалуйста, заполните все обязательные поля'
    ]);
    exit;
}

// Формируем тело письма
$body = "
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f1e3; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { text-align: center; border-bottom: 3px solid #FFD84A; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { font-family: 'Unbounded', sans-serif; color: #241638; font-size: 24px; margin: 0; }
        .field { margin: 12px 0; padding: 10px 14px; background: #f7f1e3; border-radius: 10px; }
        .field strong { color: #632895; display: inline-block; min-width: 140px; }
        .footer { margin-top: 25px; padding-top: 15px; border-top: 2px solid #efefe0; text-align: center; color: #6E6288; font-size: 13px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>✨ Новая заявка ✨</h1>
        </div>
        <div class='field'><strong>👤 ФИО родителя:</strong> " . htmlspecialchars($fullName) . "</div>
        <div class='field'><strong>🧒 Имя ребенка:</strong> " . htmlspecialchars($childName) . "</div>
        <div class='field'><strong>📅 Возраст:</strong> " . htmlspecialchars($childAge) . "</div>
        <div class='field'><strong>📚 Направление:</strong> " . htmlspecialchars($direction) . "</div>
        <div class='field'><strong>📞 Телефон:</strong> <a href='tel:" . htmlspecialchars($phone) . "'>" . htmlspecialchars($phone) . "</a></div>
        <div class='footer'>
            📩 Заявка отправлена с сайта " . htmlspecialchars($_SERVER['HTTP_HOST']) . "
        </div>
    </div>
</body>
</html>
";

// Альтернативный текст для старых почтовых клиентов
$altBody = "
Новая заявка с сайта

ФИО родителя: $fullName
Имя ребенка: $childName
Возраст: $childAge
Направление: $direction
Телефон: $phone
";

$mail = new PHPMailer(true);

try {
    // Настройки сервера
    $mail->SMTPDebug = 0;                         // 0 - отключить отладку, 1 - ошибки, 2 - подробно
    $mail->isSMTP();
    $mail->Host       = 'smtp.mail.ru';
    $mail->SMTPAuth   = true;
    $mail->Username   = $mailru_email;
    $mail->Password   = $mailru_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';

    // Отправитель и получатель
    $mail->setFrom($mailru_email, $site_name);
    $mail->addAddress($to_email);
    $mail->addReplyTo($mailru_email, $site_name);

    // Содержание письма
    $mail->isHTML(true);
    $mail->Subject = 'Новая заявка с сайта ' . $_SERVER['HTTP_HOST'];
    $mail->Body    = $body;
    $mail->AltBody = $altBody;

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Письмо отправлено']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $mail->ErrorInfo]);
}
?>
