<?php

// just a workaround to mail from my server

$email = $_GET["email"];
$message = $_GET["message"];
$subject = $_GET["subject"];
$password = $_GET["password"];

if ($password == "sdoifhuASD)ASBF(ybas")
{   
    mail($email, $subject, $message);
}