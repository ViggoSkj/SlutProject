<?php
function uuidv4(): string
{
    $data = random_bytes(16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function email($email, $subject, $message)
{
    $url = "https://labb.vgy.se/~viggosg2/SlutProject/mail.php?" . http_build_query([
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'password' => "sdoifhuASD)ASBF(ybas"
    ]);
    echo $url;
    echo file_get_contents($url);
}