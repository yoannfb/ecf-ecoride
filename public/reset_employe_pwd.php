<?php
// public/reset_employe_pwd.php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/db.php';

header('Content-Type: text/plain; charset=utf-8');

$email = 'employe@ecoride.fr';
$plain = $_GET['p'] ?? '';
if ($plain === '') { echo "Usage: /reset_employe_pwd.php?p=MotDePasse\n"; exit; }

$hash = password_hash($plain, PASSWORD_DEFAULT);
echo "New hash: $hash\n";

$sql = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role, credits)
        VALUES ('employe', :email, :hash, 'employe', 100)
        ON DUPLICATE KEY UPDATE mot_de_passe = VALUES(mot_de_passe), role = VALUES(role)";
$ok = $pdo->prepare($sql)->execute(['email'=>$email,'hash'=>$hash]);
echo "Upsert: " . ($ok ? "OK" : "KO") . "\n";

$stmt = $pdo->prepare("SELECT id, email, role, mot_de_passe FROM utilisateurs WHERE email=:e LIMIT 1");
$stmt->execute(['e'=>$email]);
$row = $stmt->fetch();
$verify = $row ? password_verify($plain, $row['mot_de_passe']) : false;

var_dump($row);
echo "password_verify: "; var_dump($verify);
