<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/db.php';

header('Content-Type: text/plain; charset=utf-8');

$email = 'employe@ecoride.fr';
$pass  = 'Employe123!'; // mot de passe attendu

$u = $pdo->prepare("SELECT id, email, role, mot_de_passe FROM utilisateurs WHERE email = :e LIMIT 1");
$u->execute(['e'=>$email]);
$row = $u->fetch();

echo "ROW = "; var_dump($row);

if ($row) {
    echo "len(hash) = " . strlen($row['mot_de_passe']) . PHP_EOL;
    $ok = password_verify($pass, $row['mot_de_passe']);
    echo "password_verify = "; var_dump($ok);
} else {
    echo "Aucun utilisateur avec cet email." . PHP_EOL;
}
