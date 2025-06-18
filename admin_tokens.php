<?php
// Connect to DB
$conn = new mysqli("localhost", "root", "", "certigen_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desc = $_POST['description'];
    $expires_at = $_POST['expires_at'];

    // Generate secure random token
    $token = bin2hex(random_bytes(16)); // 32-char token

    $stmt = $conn->prepare("INSERT INTO api_tokens (token, description, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $token, $desc, $expires_at);
    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Token generated: <code>$token</code></p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }
}

// Fetch tokens
$result = $conn->query("SELECT *, NOW() as now_time FROM api_tokens ORDER BY created_at DESC");
?>

<h2>🔑 Generate New API Token</h2>
<form method="POST" action="">
    <label>Description:</label><br>
    <input type="text" name="description" required><br><br>

    <label>Expiration Date:</label><br>
    <input type="datetime-local" name="expires_at" required><br><br>

    <input type="submit" value="Generate Token">
</form>

<hr>

<h2>📋 Existing Tokens</h2>
<table border="1" cellpadding="8">
    <tr>
        <th>Description</th>
        <th>Token</th>
        <th>Created At</th>
        <th>Expires At</th>
        <th>Status</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><code><?= $row['token'] ?></code></td>
            <td><?= $row['created_at'] ?></td>
            <td><?= $row['expires_at'] ?></td>
            <td style="color:<?= $row['now_time'] > $row['expires_at'] ? 'red' : 'green' ?>">
                <?= $row['now_time'] > $row['expires_at'] ? 'Expired' : 'Active' ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
