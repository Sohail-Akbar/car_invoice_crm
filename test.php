<?php
require_once('includes/db.php');



// Uncomment to test
// testGenerateInvoice();

// $db->insert("users", [
//     "fname" => ["encrypt" => "Super"],
//     "lname" => ["encrypt" => "Admin"],
//     "gender" => ["encrypt" => "Male"],
//     "title" => ["encrypt" => "Mr"],
//     "name" => ["encrypt" => "Super Admin"],
//     "email" => "admin@gmail.com",
//     "password" => ["encrypt" => "$2y$10$101hiV2jGn2sTvMpszu/peorZ4Uj6oZZS/CeaA0.4OKi4WJ7T8EMm"],
//     "type" => "main_admin",
//     "address" => ["encrypt" => ""],
//     "contact" => ["encrypt" => "+923081438096"],
//     "city" => ["encrypt" => "Mian Channu"],
//     "image" => ["encrypt" => "avatar.png"],
//     "is_admin" =>  1,
//     "verify_status" => 1,
// ]);


// $result = $_mailer->send(
//     'sohailakbar3324@gmail.com',         // Receiver email
//     'Sohail Akbar',                      // Receiver name
//     'Test Email from Mailer Class',      // Subject
//     '<h2>Hello!</h2><p>This email was sent via <b>Mailer class</b> using Hostinger SMTP.</p>' // HTML Body
// );

// if ($result === true) {
//     echo "✅ Message sent successfully!";
// } else {
//     echo "❌ Failed: $result";
// }


// $result = $_tc_email->send([
//     'to' => 'sohailakbar3324@gmail.com',
//     'to_name' => 'Sohail Akbar',
//     'template' => 'contactEmail',
//     'vars' => [
//         'name' => 'Sohail',
//         'email' => 'sohail@example.com',
//         'message' => 'Hello, this is a test SMTP email',
//         'subject' => 'New Message from Site',
//     ],
//     'subject' => 'New Message from Site',
//     'attachments' => [__DIR__ . '/invoices/INV-1234.pdf']
// ]);

// if ($result === true) {
//     echo "✅ Email sent successfully!";
// } else {
//     echo "❌ Failed: $result";
// }
