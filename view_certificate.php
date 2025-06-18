<?php
include 'db.php';

$id = $_GET['id'] ?? '';

$stmt = $conn->prepare("SELECT *, recipient_name AS recipient FROM certificates WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cert = $result->fetch_assoc();

if ($cert) {
    echo "<h2>Certificate for: {$cert['recipient']}</h2>"; // this now works
    echo "<p>Issued At: {$cert['issued_at']}</p>";
    echo "<p>Certificate Code: <strong>{$cert['cert_code']}</strong></p>";
} else {
    echo "Certificate not found.";
}
?>
