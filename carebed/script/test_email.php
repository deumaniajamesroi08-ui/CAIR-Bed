<?php
require 'send_email.php';

$result = sendAlertEmail(
    'receiver@gmail.com',
    'CAIR-Bed Test',
    'This is a test email from CAIR-Bed.'
);

echo $result ? '✅ Email sent successfully!' : '❌ Email failed.';