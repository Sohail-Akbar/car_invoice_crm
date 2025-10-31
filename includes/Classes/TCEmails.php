<?php

namespace TCEmail;

use \Mailjet\Resources;
use \DB\Database;

class TCEmails extends Database
{
    public function __construct($config = [])
    {
    }
    // Get Email Headers
    public function getHeaders()
    {
        $from = SITE_EMAIL;
        $site_name = SITE_NAME;
        $headers = "Reply-To: $site_name <" . $from . ">\r\n";
        $headers .= "Return-Path: $site_name <" . $from . ">\r\n";
        $headers .= "From: $site_name <" . $from . ">\r\n" .
            $headers .= "Organization: $site_name\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "X-Priority: 3\r\n";
        $headers .= "X-Mailer: PHP" . phpversion() . "\r\n";
        return $headers;
    }
    // Replace Variables from string
    function replace_email_vars($str, $vars = [], $is_email_body = false)
    {
        foreach ($vars as $var => $value) {
            $var = strtoupper($var);
            if (!$is_email_body)
                $value = replaceBreaksToBr($value);
            $var = "_:TC_" . $var . "_VAR:_";
            $str = str_replace($var, $value, $str);
        }
        return $str;
    }
    // Read Email File
    function get_data_from_file($filename, $vars = [])
    {
        $filepath = _DIR_ . "includes/Classes/templates/" . $filename;
        if (!is_file($filepath))
            return null;

        $file_data = file_get_contents($filepath);
        $vars = array_merge([
            'SITE_URL' => SITE_URL,
            'SITE_NAME' => SITE_NAME,
            'SITE_EMAIL' => SITE_EMAIL,
            'EMAIL_HEADER_IMAGE' => merge_path(SITE_URL, 'images/email-header.jpg'),
            'EMAIL_FOOTER_IMAGE' => merge_path(SITE_URL, 'images/email-footer.jpg'),
        ], $vars);
        $file_data = $this->replace_email_vars($file_data, $vars);
        return $file_data;
    }
    // Get Email Structures
    function get_email_structure()
    {
        return $this->get_data_from_file('email_structure.html');
    }
    // Read Template file
    public function readTemplateFile($filename, $vars = [])
    {
        $email_body = $this->get_data_from_file($filename, $vars);

        // Get Email Structure
        $email_structure = $this->get_email_structure();

        $file_data = $this->replace_email_vars($email_structure, [
            'email_body' => $email_body
        ], true);
        return $file_data;
    }
    // Send Email
    /* 
    @param $options = [
        'template' => 'contactEmail',
        'to' => ADMIN_EMAIL,
        'subject' => "New Message from Naxotop.com",
        'vars' => [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ]
    ]
    */
    public function send($options)
    {
        $template = arr_val($options, 'template');
        $to = $options['to'];
        $subject = arr_val($options, 'subject');
        if ($template) {
            if (!isset(EMAILS[$template])) {
                return error("Template not found in emails");
            }
            // Read Template file
            $template = EMAILS[$template];
            $filename = $template['filename'];
            if (!$subject)
                $subject = $template['subject'];
            $vars = arr_val($options, 'vars', []);

            $data = $this->readTemplateFile($filename, $vars);
            // Return Html
            if (arr_val($options, 'return_html', false))
                return $data;
            // Send Email
            return $this->sendEmailTo([
                'to' => $to,
                'body' => $data,
                'subject' => $subject
            ]);
        }
        return false;
    }
    // Send email
    /* 
    @param $data = [
        'to' => ,
        'body' => ,
        'subject' => ,
        'to_name' => optional
    ]
    */
    public function sendEmailTo($data)
    {
        $mj = new \Mailjet\Client(MAILJET_API_KEY, MAILJET_SECRET_KEY, true, ['version' => 'v3.1']);
        $to_name = arr_val($data, 'to_name', '');
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => CONTACT_EMAIL,
                        'Name' => SITE_NAME
                    ],
                    'To' => [
                        [
                            'Email' => $data['to'],
                            'Name' => $to_name
                        ]
                    ],
                    'Subject' => $data['subject'],
                    'TextPart' => "",
                    'HTMLPart' => $data['body'],
                    'CustomID' => "AppGettingStartedTest"
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        return $response->success();
    }
}
$_tc_email = new TCEmails();
require_once _DIR_ . "includes/inc/emails.php";
