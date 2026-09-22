<?php
declare(strict_types=1);

namespace App\Core;

use Config\Database;

class RateLimiter {
    public static function check(string $ip, string $action, int $maxAttempts = 5, int $decayMinutes = 15): bool {
        $db = Database::getInstance()->getConnection();
        
        // Remove old entries
        $stmt = $db->prepare("DELETE FROM rate_limits WHERE last_attempt < NOW() - INTERVAL '" . $decayMinutes . " minutes'");
        $stmt->execute();
        
        $stmt = $db->prepare("SELECT attempts FROM rate_limits WHERE ip_address = :ip AND action = :action");
        $stmt->execute(['ip' => $ip, 'action' => $action]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($result && $result['attempts'] >= $maxAttempts) {
            return false;
        }
        return true;
    }

    public static function hit(string $ip, string $action): void {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            INSERT INTO rate_limits (ip_address, action, attempts, last_attempt) 
            VALUES (:ip, :action, 1, NOW())
            ON CONFLICT (ip_address, action) 
            DO UPDATE SET attempts = rate_limits.attempts + 1, last_attempt = NOW()
        ");
        $stmt->execute(['ip' => $ip, 'action' => $action]);
    }
    
    public static function clear(string $ip, string $action): void {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM rate_limits WHERE ip_address = :ip AND action = :action");
        $stmt->execute(['ip' => $ip, 'action' => $action]);
    }
    
    public static function purgeOldRecords(): void {
        $db = Database::getInstance()->getConnection();
        // Elimina registros con más de 24 horas de antigüedad
        $stmt = $db->prepare("DELETE FROM rate_limits WHERE last_attempt < NOW() - INTERVAL '24 hours'");
        $stmt->execute();
    }
}
