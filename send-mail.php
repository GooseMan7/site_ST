<?php
// send-mail.php - с отправкой через SMTP (рекомендуется!)

// ===== НАСТРОЙКИ - ЗАМЕНИТЕ ЭТИ ДАННЫЕ! =====
$smtp_host = 'smtp.mail.ru';     // SMTP сервер
$smtp_port = 587;                // Порт
$smtp_user = 'start_tuapse_from@mail.ru'; // ВАШ EMAIL (отправитель)
$smtp_password = 'z5nGftAa9YaSyTyShRAY';  // ПАРОЛЬ от почты
$to_email = 'start_tuapse_to@mail.ru';    // КОМУ отправлять (получатель)
// =============================================

// Получаем данные из формы
$name = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

// Проверка
if (empty($name) || empty($phone) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все обязательные поля']);
    exit;
}

// Текст письма
$subject = "Новая заявка с сайта STARТ";
$message = "==========================================\n";
$message .= "📝 НОВАЯ ЗАЯВКА С САЙТА STARТ\n";
$message .= "==========================================\n\n";
$message .= "👤 ФИО: " . $name . "\n";
$message .= "📞 Телефон: " . $phone . "\n";
$message .= "✉️ Email: " . $email . "\n";
$message .= "💬 Комментарий: " . ($comment ?: "Не указан") . "\n\n";
$message .= "📅 Дата: " . date("d.m.Y H:i:s") . "\n";
$message .= "🌐 IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
$message .= "==========================================\n";

// Формируем заголовки
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/plain; charset=utf-8\r\n";
$headers .= "From: " . $smtp_user . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";

// Отправляем через SMTP
$result = mail($to_email, $subject, $message, $headers, "-f" . $smtp_user);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Заявка успешно отправлена!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка отправки. Попробуйте позже.']);
}
?>
