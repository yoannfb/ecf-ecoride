<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/db.php';

$email = 'employe@ecoride.fr';
$hash  = '$2y$10$YcM7k8hQq2zq3dX4Xb/7yuRX0dI6Jk7cQWk0m4hB3X2b0G7bq6n2W'; // = Employe123!

$sql = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role, credits)
        VALUES ('employe', :email, :hash, 'employe', 100)
        ON DUPLICATE KEY UPDATE mot_de_passe = VALUES(mot_de_passe), role = VALUES(role)";
$ok = $pdo->prepare($sql)->execute(['email'=>$email,'hash'=>$hash]);

$r = $pdo->query("SELECT id, email, role, LENGTH(mot_de_passe) len FROM utilisateurs WHERE email = ".$pdo->quote($email))->fetch();
header('Content-Type: text/plain; charset=utf-8');
echo "upsert: ".($ok?'OK':'KO').PHP_EOL;
var_dump($r);
