<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "sali.lale123@gmail.com";
    $subject = "Contact Form Submission";
    $message = htmlspecialchars($_POST['message']);
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);

    $headers = "From: $email";

    if (mail($to, $subject, "Name: $name\n\nMessage: $message", $headers)) {
        echo "Email sent successfully.";
    } else {
        echo "Failed to send the email.";
    }
}
?>