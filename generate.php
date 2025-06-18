<?php
// Connect to DB
$conn = new mysqli("localhost", "root", "", "certigen_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipient = trim($_POST['recipient']);
    $cert_code = strtoupper(uniqid("CERT"));
    $issued_at = date("Y-m-d H:i:s");

    // Load private key
    $privateKey = file_get_contents("private.pem");
    if (!$privateKey) {
        die("❌ Failed to load private.pem");
    }

    // Sign recipient name
    openssl_sign($recipient, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    $signature_base64 = base64_encode($signature);

    // Save to database
    $stmt = $conn->prepare("INSERT INTO certificates (recipient_name, cert_code, issued_at, signature) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $recipient, $cert_code, $issued_at, $signature_base64);

    if ($stmt->execute()) {
        echo "<h3>✅ Certificate Created</h3>";
        echo "<b>Recipient:</b> $recipient<br>";
        echo "<b>Cert Code:</b> $cert_code<br>";
        echo "<b>Issued At:</b> $issued_at<br>";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}
?>

<!-- HTML Form -->
<h2>📝 Issue New Certificate</h2>
<form method="POST" action="">
    <label>Recipient Name:</label><br>
    <input type="text" name="recipient" required><br><br>
    <input type="submit" value="Generate Certificate">
</form>
