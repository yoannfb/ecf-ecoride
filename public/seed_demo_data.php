<?php
// public/seed_demo_data.php — insère des données de démo (utilisateurs, véhicules, trajets, participations, avis)
require __DIR__ . '/../includes/db.php';
header('Content-Type: text/plain; charset=utf-8');

/**
 * Ce script crée :
 * - 10 utilisateurs "demo" (emails user1@demo.local ... user10@demo.local / mdp: demo123)
 * - des véhicules (si table/colonnes présentes)
 * - ~30 trajets sur les 14 derniers jours (conducteurs parmi les "demo")
 * - des participations (passagers "demo")
 * - des avis (mélange de 'en attente' et validés)
 *
 * Tout est balisé avec le domaine demo.local pour pouvoir nettoyer si besoin.
 */

function colExists(PDO $pdo, string $table, string $col): bool {
    $q = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns
                        WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c");
    $q->execute(['t'=>$table, 'c'=>$col]);
    return (bool)$q->fetchColumn();
}
function tableExists(PDO $pdo, string $table): bool {
    $q = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables
                        WHERE table_schema = DATABASE() AND table_name = :t");
    $q->execute(['t'=>$table]);
    return (bool)$q->fetchColumn();
}

echo "== SEED DEMO ==\n";

mt_srand(12345); // reproductible
$now = new DateTimeImmutable('now');

// --- 0) Option de nettoyage: /seed_demo_data.php?drop_demo=1 ---
if (!empty($_GET['drop_demo'])) {
    echo "Nettoyage des données demo...\n";

    // Récupère les IDs des users demo
    $ids = $pdo->query("SELECT id FROM utilisateurs WHERE email LIKE '%@demo.local'")->fetchAll(PDO::FETCH_COLUMN);
    if ($ids) {
        $in = implode(',', array_map('intval', $ids));
        if (tableExists($pdo,'avis') && colExists($pdo,'avis','auteur_id')) {
            $pdo->exec("DELETE FROM avis WHERE auteur_id IN ($in)");
        }
        if (tableExists($pdo,'participations') && colExists($pdo,'participations','utilisateur_id')) {
            $pdo->exec("DELETE FROM participations WHERE utilisateur_id IN ($in)");
        }
        if (tableExists($pdo,'vehicules') && colExists($pdo,'vehicules','utilisateur_id')) {
            $pdo->exec("DELETE FROM vehicules WHERE utilisateur_id IN ($in)");
        }
        // Trajets conduits par des demo (si tu veux les supprimer aussi)
        if (tableExists($pdo,'trajets')) {
            foreach (['conducteur_id','driver_id','createur_id','utilisateur_id'] as $c) {
                if (colExists($pdo,'trajets',$c)) {
                    $pdo->exec("DELETE FROM trajets WHERE $c IN ($in)");
                    break;
                }
            }
        }
        $pdo->exec("DELETE FROM utilisateurs WHERE id IN ($in)");
    }
    echo "Nettoyage terminé.\n";
    exit;
}

// --- 1) Crée 10 utilisateurs demo ---
$hashDemo = password_hash('demo123', PASSWORD_DEFAULT);
$pdo->beginTransaction();
$createdUsers = 0;
for ($i=1; $i<=10; $i++) {
    $email = "user{$i}@demo.local";
    $pseudo = "user{$i}";
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (pseudo,email,mot_de_passe,role,credits,suspendu)
                           VALUES (:p,:e,:h,'user',:c,0)
                           ON DUPLICATE KEY UPDATE pseudo=VALUES(pseudo)");
    $credits = mt_rand(0, 200);
    $ok = $stmt->execute(['p'=>$pseudo,'e'=>$email,'h'=>$hashDemo,'c'=>$credits]);
    if ($ok && $stmt->rowCount() > 0) $createdUsers++;
}
$pdo->commit();
$users = $pdo->query("SELECT id, email FROM utilisateurs WHERE email LIKE '%@demo.local' ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
echo "Utilisateurs demo: ".count($users)." (créés/présents)\n";

$userIds = array_column($users, 'id');

// --- 2) Vehicules (si table/colonne) ---
$vehInserted = 0;
if (tableExists($pdo,'vehicules') && colExists($pdo,'vehicules','utilisateur_id')) {
    $pdo->beginTransaction();
    foreach ($userIds as $uid) {
        // 50% des demo ont 1 véhicule
        if (mt_rand(0,1) === 1) {
            $marques = ['Renault','Peugeot','Citroën','Tesla','VW','Toyota'];
            $modeles = ['Clio','208','C3','Model 3','Golf','Yaris'];
            $marque = $marques[array_rand($marques)];
            $modele = $modeles[array_rand($modeles)];
            // Insère avec colonnes si elles existent
            $cols = ['utilisateur_id'];
            $vals = [':uid'];
            $params = ['uid'=>$uid];

            foreach (['marque','modele','couleur','plaque'] as $opt) {
                if (colExists($pdo,'vehicules',$opt)) {
                    $cols[] = $opt;
                    $vals[] = ':'.$opt;
                }
            }
            if (in_array('marque',$cols)) $params['marque'] = $marque;
            if (in_array('modele',$cols)) $params['modele'] = $modele;
            if (in_array('couleur',$cols)) $params['couleur'] = ['noire','grise','bleue','rouge'][array_rand(['noire','grise','bleue','rouge'])];
            if (in_array('plaque',$cols))  $params['plaque']  = 'DEMO-'.mt_rand(100,999);

            $sql = "INSERT INTO vehicules (".implode(',',$cols).") VALUES (".implode(',',$vals).")";
            $pdo->prepare($sql)->execute($params);
            $vehInserted++;
        }
    }
    $pdo->commit();
}
echo "Véhicules insérés: $vehInserted\n";

// --- 3) Trajets (si table) ---
$trajInserted = 0;
$trajIds = [];
if (tableExists($pdo,'trajets')) {
    // Colonnes optionnelles
    $hasDepartVille   = colExists($pdo,'trajets','depart_ville');
    $hasArriveeVille  = colExists($pdo,'trajets','arrivee_ville');
    $hasDateDepart    = colExists($pdo,'trajets','date_depart');
    $hasDateArrivee   = colExists($pdo,'trajets','date_arrivee');
    $hasStatut        = colExists($pdo,'trajets','statut');
    $hasCommission    = colExists($pdo,'trajets','commission');

    // Colonne "conducteur"
    $driverCol = null;
    foreach (['conducteur_id','driver_id','createur_id','utilisateur_id'] as $c) {
        if (colExists($pdo,'trajets',$c)) { $driverCol = $c; break; }
    }

    if ($driverCol !== null) {
        $villes = ['Paris','Lyon','Marseille','Bordeaux','Lille','Nantes','Rennes','Toulouse','Nice','Strasbourg'];

        $pdo->beginTransaction();
        for ($k=0; $k<30; $k++) {
            $driver = $userIds[array_rand($userIds)];
            $d1 = $now->sub(new DateInterval('P'.mt_rand(0,13).'D')); // 0..13 jours
            $d2 = $d1->add(new DateInterval('PT'.mt_rand(30,300).'M')); // +30..300 min

            $cols = [$driverCol];
            $vals = [':driver'];
            $params = ['driver'=>$driver];

            if ($hasDepartVille)  { $cols[]='depart_ville';  $vals[]=':dv'; $params['dv']=$villes[array_rand($villes)]; }
            if ($hasArriveeVille) { $cols[]='arrivee_ville'; $vals[]=':av'; $params['av']=$villes[array_rand($villes)]; }
            if ($hasDateDepart)   { $cols[]='date_depart';   $vals[]=':dd'; $params['dd']=$d1->format('Y-m-d H:i:s'); }
            if ($hasDateArrivee)  { $cols[]='date_arrivee';  $vals[]=':da'; $params['da']=$d2->format('Y-m-d H:i:s'); }
            if ($hasStatut)       { $cols[]='statut';        $vals[]=':st'; $params['st']= (mt_rand(0,10)>1)?'termine':'annule'; }
            if ($hasCommission)   { $cols[]='commission';    $vals[]=':cm'; $params['cm']= 2; } // règle de l'énoncé

            $sql="INSERT INTO trajets (".implode(',',$cols).") VALUES (".implode(',',$vals).")";
            $pdo->prepare($sql)->execute($params);
            $trajInserted++;
            $trajIds[] = (int)$pdo->lastInsertId();
        }
        $pdo->commit();
    }
}
echo "Trajets insérés: $trajInserted\n";

// --- 4) Participations (si table) ---
$partInserted = 0;
if (tableExists($pdo,'participations') && colExists($pdo,'participations','utilisateur_id') && !empty($trajIds)) {
    $pdo->beginTransaction();
    foreach ($trajIds as $tid) {
        // 0..2 passagers
        $np = mt_rand(0,2);
        for ($j=0; $j<$np; $j++) {
            $passager = $userIds[array_rand($userIds)];
            $pdo->prepare("INSERT INTO participations (utilisateur_id, covoiturage_id) VALUES (:u, :t)")
                ->execute(['u'=>$passager, 't'=>$tid]);
            $partInserted++;
        }
    }
    $pdo->commit();
}
echo "Participations insérées: $partInserted\n";

// --- 5) Avis (si table) ---
$avisInserted = 0;
if (tableExists($pdo,'avis')) {
    $hasNote = colExists($pdo,'avis','note');
    $hasComment = colExists($pdo,'avis','commentaire');
    $hasTrajet = colExists($pdo,'avis','trajet_id');
    $hasAuteur = colExists($pdo,'avis','auteur_id');
    $hasStatutAvis = colExists($pdo,'avis','statut');

    if ($hasNote && $hasComment && $hasTrajet && $hasAuteur) {
        $texts = ['Super trajet','Ponctuel','Tout s’est bien passé','Conducteur sympa','Trajet confortable','RAS','À l’heure','Bonne ambiance'];
        $pdo->beginTransaction();
        // 60% des trajets ont un avis
        foreach ($trajIds as $tid) {
            if (mt_rand(0,9) <= 5) {
                $auteur = $userIds[array_rand($userIds)];
                $note = mt_rand(3,5);
                $comm = $texts[array_rand($texts)].' (DEMO)';
                $stat = (mt_rand(0,1)===1)?'en attente':'valide';

                $cols=['trajet_id','auteur_id','note','commentaire'];
                $vals=[':t',':u',':n',':c'];
                $params=['t'=>$tid,'u'=>$auteur,'n'=>$note,'c'=>$comm];
                if ($hasStatutAvis) { $cols[]='statut'; $vals[]=':s'; $params['s']=$stat; }

                $sql="INSERT INTO avis (".implode(',',$cols).") VALUES (".implode(',',$vals).")";
                $pdo->prepare($sql)->execute($params);
                $avisInserted++;
            }
        }
        $pdo->commit();
    }
}
echo "Avis insérés: $avisInserted\n";

echo "\nOK. Astuces:\n";
echo "- Connexions demo: user1@demo.local … user10@demo.local / mdp: demo123\n";
echo "- Nettoyer: /seed_demo_data.php?drop_demo=1 (supprime toutes les données 'demo')\n";
