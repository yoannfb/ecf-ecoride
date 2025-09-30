<?php
require __DIR__ . '/../includes/db.php';
header('Content-Type: text/plain; charset=utf-8');

$email = 'employe@ecoride.fr';
$plain = $_GET['p'] ?? '';
if ($plain === '') { echo "Usage: /reset_employe_pwd_local.php?p=MotDePasse\n"; exit; }

$hash = password_hash($plain, PASSWORD_DEFAULT);
$ok = $pdo->prepare("
  INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role, credits)
  VALUES ('employe', :email, :hash, 'employe', 100)
  ON DUPLICATE KEY UPDATE mot_de_passe=VALUES(mot_de_passe), role=VALUES(role)
")->execute(['email'=>$email,'hash'=>$hash]);

echo "Upsert: ".($ok?'OK':'KO')."\n";
