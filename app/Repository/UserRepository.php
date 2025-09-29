<?php
namespace App\Repository;

class UserRepository
{
    private \PDO $db;

    public function __construct()
    {
        // chemin absolu jusqu'au projet, puis includes/db.php
        $root = \dirname(__DIR__, 2);            // /app
        $dbFile = $root . '/includes/db.php';    // /app/includes/db.php

        if (!\file_exists($dbFile)) {
            throw new \RuntimeException("includes/db.php introuvable: {$dbFile}");
        }

        require $dbFile; // doit définir $pdo

        if (!isset($pdo) || !($pdo instanceof \PDO)) {
            throw new \RuntimeException("PDO non initialisé par includes/db.php (vérifie les variables d'env Heroku).");
        }

        $this->db = $pdo;
    }

    public function trouverParEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $u = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $u ?: null;
    }
}

