<?php
require_once 'DB.php';
class Certificate {
    public static function getByCode($cert_code) {
        $conn = DB::connect();
        $stmt = $conn->prepare("SELECT * FROM certificates WHERE cert_code = ?");
        $stmt->bind_param("s", $cert_code);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}