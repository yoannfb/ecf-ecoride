<?php
require 'includes/db.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM utilisateurs");
    $result = $stmt->fetch();
    echo "Utilisateurs en base : " . $result['total'];
} catch (Exception $e) {
    echo "Erreur base : " . $e->getMessage();
}
