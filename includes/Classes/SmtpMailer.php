<?php

namespace TCEmail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class TCEmails
{
    public function __construct($config = [])
    {
        // Optional: initialize config if needed
    }

    // Replace variables in string
    private function replace_email_vars($str, $vars = [], $is_email_body = false)
    {
        foreach ($vars as $var => $value) {
            $var = strtoupper($var);
            if (!$is_email_body)
                $value = replaceBreaksToBr($value); // your helper
            $var = "_:TC_" . $var . "_VAR:_";
            $str = str_replace($var, $value, $str);
        }
        return $str;
    }

    // Read template file
    private function get_data_from_file($filename, $vars = [])
    {
        $filepath = __DIR__ . "/templates/" . $filename;
        if (!is_file($filepath)) return null;

        $file_data = file_get_contents($filepath);
        $vars = array_merge([
            'SITE_URL' => defined('SITE_URL') ? SITE_URL : '',
            'SITE_NAME' => defined('SITE_NAME') ? SITE_NAME : '',
            'SITE_EMAIL' => defined('SITE_EMAIL') ? SITE_EMAIL : '',
            'EMAIL_HEADER_IMAGE' => defined('SITE_URL') ? SITE_URL . '/images/email-header.jpg' : '',
            'EMAIL_FOOTER_IMAGE' => defined('SITE_URL') ? SITE_URL . '/images/email-footer.jpg' : '',
        ], $vars);

        return $this->replace_email_vars($file_data, $vars);
    }

    // Get email structure
    private function get_email_structure()
    {
        return $this->get_data_from_file('email_structure.html');
    }

    // Read template with structure and variables
    public function readTemplateFile($filename, $vars = [])
    {
        $email_body = $this->get_data_from_file($filename, $vars);
        $email_structure = $this->get_email_structure();

        return $this->replace_email_vars($email_structure, [
            'email_body' => $email_body
        ], true);
    }

    /**
     * Send email using template
     * 
     * $options = [
     *   'template' => 'contactEmail',
     *   'to' => 'recipient@example.com',
     *   'to_name' => 'Recipient Name',   // optional
     *   'subject' => 'Email Subject',    // optional, overrides template
     *   'vars' => [],                     // template variables
     *   'attachments' => [],              // optional file paths
     *   'return_html' => false            // optional, returns HTML instead of sending
     * ]
     */
    public function send($options)
    {
        $templateKey = $options['template'] ?? null;
        $to = $options['to'] ?? null;
        $to_name = $options['to_name'] ?? '';
        $subject = $options['subject'] ?? null;
        $attachments = $options['attachments'] ?? [];

        if (!$templateKey || !$to) return false;

        if (!isset(EMAILS[$templateKey])) return "Template '{$templateKey}' not found";

        $template = EMAILS[$templateKey];
        $filename = $template['filename'];
        if (!$subject) $subject = $template['subject'];

        $vars = $options['vars'] ?? [];

        // **Replace variables in the subject**
        $subject = $this->replace_email_vars($subject, $vars);

        $body = $this->readTemplateFile($filename, $vars);

        // Return HTML only
        if (!empty($options['return_html'])) return $body;

        // Send email
        return $this->sendEmailTo([
            'to' => $to,
            'to_name' => $to_name,
            'subject' => $subject,
            'body' => $body,
            'attachments' => $attachments
        ]);
    }


    /**
     * Send email via SMTP
     * 
     * $data = [
     *   'to' => 'recipient@example.com',
     *   'to_name' => 'Recipient Name',
     *   'subject' => 'Email Subject',
     *   'body' => '<p>HTML body</p>',
     *   'attachments' => [] // optional
     * ]
     */
    public function sendEmailTo($data)
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;         // e.g. smtp.hostinger.com
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_EMAIL;        // SMTP email
            $mail->Password   = SMTP_PASSWORD;     // SMTP password
            $mail->SMTPSecure = 'ssl';             // 'ssl' or 'tls'
            $mail->Port       = 465;               // 465 for SSL, 587 for TLS

            // From
            $mail->setFrom(SMTP_EMAIL, defined('SITE_NAME') ? SITE_NAME : '');

            // Recipient
            $mail->addAddress($data['to'], $data['to_name'] ?? '');

            // Subject & Body
            $mail->isHTML(true);
            $mail->Subject = $data['subject'];
            $mail->Body    = $data['body'];
            $mail->AltBody = strip_tags($data['body']);

            // Attachments
            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    if (file_exists($file)) $mail->addAttachment($file);
                }
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            return "Mailer Error: " . $mail->ErrorInfo;
        }
    }
}


$_tc_email = new TCEmails();
require_once _DIR_ . "includes/inc/emails.php";
