<?php
class DB {
    private static $conn;
    public static function connect() {
        if (!self::$conn) {
            self::$conn = new mysqli("localhost", "root", "", "cert_generator_db");
            if (self::$conn->connect_error) {
                die("Database connection failed: " . self::$conn->connect_error);
            }
        }
        return self::$conn;
    }
}