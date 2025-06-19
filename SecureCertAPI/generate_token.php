<?php
require_once '../classes/Token.php';
$token = Token::generate();
echo json_encode([
    "token" => $token,
    "message" => "✅ API Token generated successfully."
]);