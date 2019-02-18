<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__ . '/../mail/PHPMailer.php';
require_once __DIR__ . '/../mail/SMTP.php';
require_once __DIR__ . '/../mail/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

class SmtpConfig {

    private static $server = 'smtp.gmail.com';
    private static $username = 'openvle.admn@gmail.com';
    private static $password = '7C71wVlbPqumShRUvmbW';
    private static $port = 587;
    private static $secure = 'tls';

    public static function SendMail($email, $subject, $body) {
        $mailer = new PHPMailer(FALSE);
        $mailer->isSMTP();
        $mailer->Host = SmtpConfig::$server;
        $mailer->Username = SmtpConfig::$username;
        $mailer->Password = SmtpConfig::$password;
        $mailer->Port = SmtpConfig::$port;
        $mailer->SMTPSecure = SmtpConfig::$secure;
        $mailer->SMTPAuth = TRUE;

        $mailer->setFrom(SmtpConfig::$username, 'Open VLE');
        $mailer->addAddress($email);

        $mailer->Subject = $subject;
        $mailer->Body = $body;
        $mailer->send();
    }

}
