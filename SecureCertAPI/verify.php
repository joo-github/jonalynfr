<?php
require_once '../classes/Token.php';
require_once '../classes/Certificate.php';
$token = $_GET['token'] ?? '';
$cert_code = $_GET['cert_code'] ?? '';
if (!$token || !$cert_code) {
    http_response_code(400);
    echo json_encode(["error" => "Missing token or cert_code"]);
    exit;
}
if (!Token::isValid($token)) {
    http_response_code(403);
    echo json_encode(["error" => "Invalid or expired API token"]);
    exit;
}
$cert = Certificate::getByCode($cert_code);
if (!$cert) {
    http_response_code(404);
    echo json_encode(["error" => "Certificate not found"]);
    exit;
}
$public_key = file_get_contents("../keys/public.key");
$verified = openssl_verify(
    $cert['recipient_name'] . $cert['issued_at'],
    base64_decode($cert['signature']),
    $public_key,
    OPENSSL_ALGO_SHA256
);
echo json_encode([
    "cert_code" => $cert['cert_code'],
    "recipient_name" => $cert['recipient_name'],
    "issued_at" => $cert['issued_at'],
    "signature_valid" => $verified === 1 ? true : false
]);