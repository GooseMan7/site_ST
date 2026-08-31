<?php
// send-mail.php - обработчик формы отправки на почту

// Настройки получателя
$to_email = "arzamazovnikita@gmail.com"; // Ваш email (замените на свой)
$subject = "Новая заявка с сайта STARТ";

// Получаем данные из формы
$name = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

// Проверяем обязательные поля
if (empty($name) || empty($phone) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все обязательные поля']);
    exit;
}

// Формируем сообщение
$message = "📝 Новая заявка с сайта STARТ\n\n";
$message .= "👤 ФИО: $name\n";
$message .= "📞 Телефон: $phone\n";
$message .= "✉️ Email: $email\n";
$message .= "💬 Комментарий: " . ($comment ?: "Не указан") . "\n\n";
$message .= "📅 Дата заявки: " . date("d.m.Y H:i:s") . "\n";
$message .= "🌐 IP адрес: " . $_SERVER['REMOTE_ADDR'] . "\n";
$message .= "🌍 Браузер: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Не определен');

// Заголовки письма
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/plain; charset=utf-8" . "\r\n";
$headers .= "From: Сайт STARТ <noreply@start-tuapse.ru>" . "\r\n";
$headers .= "Reply-To: $email" . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Отправка письма
$mail_sent = mail($to_email, $subject, $message, $headers);

// Ответ клиенту
if ($mail_sent) {
    echo json_encode(['success' => true, 'message' => 'Заявка успешно отправлена!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при отправке. Попробуйте позже.']);
}
?>
