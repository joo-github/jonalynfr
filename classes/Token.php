<?php
require_once 'DB.php';
class Token {
    public static function isValid($token) {
        $conn = DB::connect();
        $stmt = $conn->prepare("SELECT * FROM api_tokens WHERE token = ? AND expires_at > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
    public static function generate() {
        $token = bin2hex(random_bytes(32));
        $created_at = date('Y-m-d H:i:s');
        $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
        $conn = DB::connect();
        $stmt = $conn->prepare("INSERT INTO api_tokens (token, created_at, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $token, $created_at, $expires_at);
        $stmt->execute();
        return $token;
    }
}