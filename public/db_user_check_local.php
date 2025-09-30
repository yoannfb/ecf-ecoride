<?php
require __DIR__ . '/../includes/db.php';
header('Content-Type: text/plain; charset=utf-8');

$email = 'employe@ecoride.fr';
$pass  = 'Employe123!'; // mets ici le mdp que TU tapes au login

$u = $pdo->prepare("SELECT id, email, role, mot_de_passe FROM utilisateurs WHERE email = :e LIMIT 1");
$u->execute(['e'=>$email]);
$row = $u->fetch();

var_dump($row);
if ($row) {
    var_dump(['len'=>strlen($row['mot_de_passe'])]);
    var_dump(['password_verify'=>password_verify($pass, $row['mot_de_passe'])]);
} else {
    echo "Aucun utilisateur\n";
}
