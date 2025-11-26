<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once "env.php";

define("SITE_NAME", "Garage");
define("SITE_EMAIL", "garagenoreply@devmedqon.com");
define("CONTACT_EMAIL", "");


define('EMAILS', [
    'forgotEmail' => [
        'filename' => 'forgotEmail.html',
        'subject' => 'We received request to reset the password of your account'
    ],
    'verifyEmail' => [
        'filename' => 'verifyEmail.html',
        'subject' => 'Please Verify Your email'
    ],
    'contactEmail' => [
        'filename' => 'contactEmail.html',
        'subject' => 'You Recieved a New Message'
    ],
    'invoice' => [
        'filename' => 'invoice_template.html',
        'subject' => 'Invoice'
    ],
    'set_password' => [
        'filename' => 'set_password.html',
        'subject' => 'Set Your Account Password'
    ],
    'sendOtp' => [
        'filename' => 'sendOtp.html',
        'subject'  => 'Your OTP Code: _:TC_OTP_VAR:_'
    ],
]);

define('SMTP_HOST', 'smtp.hostinger.com');
define('SMTP_PASSWORD', '_-bi!DX2TZGHv69');
define('SMTP_EMAIL', "garagenoreply@devmedqon.com");

define("UTC_TIME", new DateTime("now", new DateTimeZone("UTC")));
define("CREATED_AT", UTC_TIME->format("Y-m-d H:i:s"));
