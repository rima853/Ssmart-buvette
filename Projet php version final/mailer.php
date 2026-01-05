<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/vendor/autoload.php';

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->SMTPAuth   = true;
$mail->Host       = 'smtp.gmail.com';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port       = 587;
$mail->Username   = 'someoneoutthere.01@gmail.com';
$mail->Password   = 'qyxfuziflbqlxspz'; // mot de passe application Gmail
$mail->isHTML(true);

return $mail;
?>