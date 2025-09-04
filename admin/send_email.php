<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['full_name'];
    $email = $_POST['email_address'];
    $phone = $_POST['phone_number'];
    $subject = $_POST['email_subject'];
    $message = $_POST['message'];

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'mustafizur01815@gmail.com'; // Replace with your email
        $mail->Password = 'jups axkm ouin mmly'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('mustafizur01815@gmail.com'); // Replace with your email where you want to receive messages

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Portfolio Contact: $subject";
        
        // Create HTML message
        $mail->Body = "
            <h3>New Contact Form Submission</h3>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Phone:</strong> {$phone}</p>
            <p><strong>Subject:</strong> {$subject}</p>
            <p><strong>Message:</strong></p>
            <p>{$message}</p>
        ";

        $mail->AltBody = "
            New Contact Form Submission\n
            Name: {$name}\n
            Email: {$email}\n
            Phone: {$phone}\n
            Subject: {$subject}\n
            Message:\n{$message}
        ";

        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Message sent successfully!']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Message could not be sent. Try again later.']);
    }
} else {
    // If someone tries to access this file directly
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
}
?>
