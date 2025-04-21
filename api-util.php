<?php

header('Content-Type: application/json');

// Set headers for JSON response
function BadReqeust(string $message)
{
    http_response_code(400);
    die(json_encode([
        "message" => $message
    ]));
}

function SendResponse(array $response)
{
    $json = json_encode($response);
    if (strlen($json) == 0)
        echo json_last_error_msg();
    else
        echo $json;
}