<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/db.php';

$email = 'employe@ecoride.fr';
$hash  = '$2y$10$aTT1Yfnu8RLEYGr0mh5yWuYDulHkcfLeFeyFZvskmd2nDQpwBSGhK'; // = Employe123

$sql = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role, credits)
        VALUES ('employe', :email, :hash, 'employe', 100)
        ON DUPLICATE KEY UPDATE mot_de_passe = VALUES(mot_de_passe), role = VALUES(role)";
$ok = $pdo->prepare($sql)->execute(['email'=>$email,'hash'=>$hash]);

header('Content-Type: text/plain; charset=utf-8');
echo $ok ? "upsert OK\n" : "upsert KO\n";

