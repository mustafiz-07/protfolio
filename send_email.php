<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the form data
    $full_name = htmlspecialchars($_POST['full_name']);
    $email_address = htmlspecialchars($_POST['email_address']);
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $email_subject = htmlspecialchars($_POST['email_subject']);
    $message = htmlspecialchars($_POST['message']);

    // Set your email address here
    $to = "mustafizur01815@gmail.com"; // REPLACE THIS with your real email

    // Prepare the email content
    $email_content = "Name: $full_name\n";
    $email_content .= "Email: $email_address\n";
    $email_content .= "Phone: $phone_number\n\n";
    $email_content .= "Message:\n$message\n";

    // Build the email headers
    $email_headers = "From: $full_name <$email_address>";
    

    // Send the email
    if (mail($to, $email_subject, $email_content, $email_headers)) {
        // Redirect back to the homepage with a success message
        header("Location: index.html?status=success");
    } else {
        // Redirect back to the homepage with a failure message
        header("Location: index.html?status=error");
    }
} else {
    // Not a POST request, so redirect to the homepage
    header("Location: index.html");
}
?>