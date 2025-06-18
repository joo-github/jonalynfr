<?php
header("Content-Type: application/json");

// ✅ 1. Connect to database
$conn = new mysqli("localhost", "root", "", "certigen_db");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// ✅ 2. Get token and cert_code from URL
$token = $_GET['token'] ?? '';
$cert_code = $_GET['cert_code'] ?? '';

if (!$token || !$cert_code) {
    echo json_encode(["error" => "Missing token or cert_code"]);
    exit;
}

// ✅ 3. Validate API token
$stmt = $conn->prepare("SELECT * FROM api_tokens WHERE token = ? AND status = 'active' AND (expires_at IS NULL OR expires_at > NOW())");
$stmt->bind_param("s", $token);
$stmt->execute();
$token_result = $stmt->get_result();
$token_row = $token_result->fetch_assoc();

if (!$token_row) {
    echo json_encode(["error" => "Invalid or expired token"]);
    exit;
}

// ✅ 4. Check usage limit
if ($token_row['usage_limit'] !== null && $token_row['usage_count'] >= $token_row['usage_limit']) {
    echo json_encode(["error" => "Token rate limit exceeded"]);
    exit;
}

// ✅ 5. Increment usage count
$update_stmt = $conn->prepare("UPDATE api_tokens SET usage_count = usage_count + 1 WHERE id = ?");
$update_stmt->bind_param("i", $token_row['id']);
$update_stmt->execute();

// ✅ 6. Check for certificate (case-insensitive match)
$stmt = $conn->prepare("SELECT recipient_name AS recipient, issued_at, cert_code FROM certificates WHERE UPPER(cert_code) = UPPER(?)");
$stmt->bind_param("s", $cert_code);
$stmt->execute();
$result = $stmt->get_result();
$cert = $result->fetch_assoc();

if ($cert) {
    echo json_encode([
        "certificate" => $cert,
        "verified" => true  // change this later if you implement real digital signature check
    ]);
} else {
    echo json_encode(["error" => "Certificate not found"]);
}
?>
