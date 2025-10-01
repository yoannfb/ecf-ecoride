<?php
// public/transfer_user_data.php — transfère les données de l'admin vers un nouveau compte "utilisateur"
require __DIR__ . '/../includes/db.php';
header('Content-Type: text/plain; charset=utf-8');

// PARAMÈTRES
$ADMIN_EMAIL = 'admin@ecoride.fr';
$NEW_EMAIL   = 'utilisateur@ecoride.fr';
$NEW_PSEUDO  = 'utilisateur';
$NEW_PASS    = 'utilisateur'; // sera hashé

function colExists(PDO $pdo, string $table, string $col): bool {
    $st = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns
                         WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c");
    $st->execute(['t'=>$table, 'c'=>$col]);
    return (bool)$st->fetchColumn();
}

echo "== TRANSFERT données admin -> nouvel utilisateur ==\n";

// 1) Récupérer l'admin
$st = $pdo->prepare("SELECT id, credits FROM utilisateurs WHERE email = :e LIMIT 1");
$st->execute(['e'=>$ADMIN_EMAIL]);
$admin = $st->fetch();
if (!$admin) { echo "Admin introuvable avec email {$ADMIN_EMAIL}\n"; exit(1); }
$ADMIN_ID = (int)$admin['id'];
$ADMIN_CREDITS = (int)$admin['credits'];
echo "Admin id={$ADMIN_ID}, credits={$ADMIN_CREDITS}\n";

// 2) Créer / MAJ le nouvel utilisateur
$hash = password_hash($NEW_PASS, PASSWORD_DEFAULT);
$sql = "INSERT INTO utilisateurs (pseudo,email,mot_de_passe,role,credits,suspendu)
        VALUES (:p,:e,:h,'user',:c,0)
        ON DUPLICATE KEY UPDATE mot_de_passe=VALUES(mot_de_passe), role='user', suspendu=0";
$ok = $pdo->prepare($sql)->execute(['p'=>$NEW_PSEUDO,'e'=>$NEW_EMAIL,'h'=>$hash,'c'=>$ADMIN_CREDITS]);
echo "Upsert nouvel utilisateur: ".($ok?'OK':'KO')."\n";

$st = $pdo->prepare("SELECT id FROM utilisateurs WHERE email=:e LIMIT 1");
$st->execute(['e'=>$NEW_EMAIL]);
$new = $st->fetch();
if (!$new) { echo "Nouveau compte introuvable (!)\n"; exit(1); }
$NEW_ID = (int)$new['id'];
echo "New user id={$NEW_ID}\n";

$pdo->beginTransaction();

// 3) Transférer les données table par table (si colonnes présentes)
$updated = [];

// vehicules.utilisateur_id
if (colExists($pdo,'vehicules','utilisateur_id')) {
    $n = $pdo->exec("UPDATE vehicules SET utilisateur_id={$NEW_ID} WHERE utilisateur_id={$ADMIN_ID}");
    $updated[] = "vehicules.utilisateur_id => {$n}";
}

// participations.utilisateur_id
if (colExists($pdo,'participations','utilisateur_id')) {
    $n = $pdo->exec("UPDATE participations SET utilisateur_id={$NEW_ID} WHERE utilisateur_id={$ADMIN_ID}");
    $updated[] = "participations.utilisateur_id => {$n}";
}

// avis.auteur_id
if (colExists($pdo,'avis','auteur_id')) {
    $n = $pdo->exec("UPDATE avis SET auteur_id={$NEW_ID} WHERE auteur_id={$ADMIN_ID}");
    $updated[] = "avis.auteur_id => {$n}";
}

// trajets.[conducteur_id|driver_id|createur_id]
foreach (['conducteur_id','driver_id','createur_id','utilisateur_id'] as $c) {
    if (colExists($pdo,'trajets',$c)) {
        $n = $pdo->exec("UPDATE trajets SET {$c}={$NEW_ID} WHERE {$c}={$ADMIN_ID}");
        $updated[] = "trajets.{$c} => {$n}";
    }
}

// covoiturages.[conducteur_id|createur_id|utilisateur_id|auteur_id]
foreach (['conducteur_id','createur_id','utilisateur_id','auteur_id'] as $c) {
    if (colExists($pdo,'covoiturages',$c)) {
        $n = $pdo->exec("UPDATE covoiturages SET {$c}={$NEW_ID} WHERE {$c}={$ADMIN_ID}");
        $updated[] = "covoiturages.{$c} => {$n}";
    }
}

$pdo->commit();

echo "Mises à jour :\n- ".implode("\n- ", $updated)."\n";

// 4) (optionnel) remettre les crédits admin à 0 pour qu'il soit “propre”
$pdo->exec("UPDATE utilisateurs SET credits=0 WHERE id={$ADMIN_ID}");
echo "Admin credits remis à 0.\n";

echo "\nTransfert terminé.\n";

