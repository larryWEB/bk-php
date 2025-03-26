<?php
// config.php
$host = 'localhost';
$dbname = 'user_authentication';
$username = 'root';  // Default XAMPP username
$password = '';      // Default XAMPP password (empty)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Function to generate secure random token
// function generateToken($length = 32) {
 //  return bin2hex(random_bytes($length));
//}

// Function to send email (you'll need to configure your email settings)
 //function sendEmail($to, $subject, $message) {
   //  $headers = "From: noreply@yourwebsite.com\r\n";
    // $headers .= "Reply-To: noreply@yourwebsite.com\r\n";
    // $headers .= "X-Mailer: PHP/" . phpversion();

  //   return mail($to, $subject, $message, $headers);
 //}
