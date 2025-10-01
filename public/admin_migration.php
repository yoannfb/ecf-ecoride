<?php
// public/admin_migration.php — migration sûre pour MySQL 5.x / 8.x
require __DIR__ . '/../includes/db.php';
header('Content-Type: text/plain; charset=utf-8');

function colonneExiste(PDO $pdo, string $table, string $col): bool {
    $sql = "SELECT COUNT(*) FROM information_schema.columns
            WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c";
    $st = $pdo->prepare($sql);
    $st->execute(['t'=>$table, 'c'=>$col]);
    return (bool)$st->fetchColumn();
}

function indexExiste(PDO $pdo, string $table, string $index): bool {
    $sql = "SELECT COUNT(*) FROM information_schema.statistics
            WHERE table_schema = DATABASE() AND table_name = :t AND index_name = :i";
    $st = $pdo->prepare($sql);
    $st->execute(['t'=>$table, 'i'=>$index]);
    return (bool)$st->fetchColumn();
}

echo "== Migration admin ==\n";

// 1) utilisateurs.suspendu (TINYINT(1) NOT NULL DEFAULT 0)
if (!colonneExiste($pdo, 'utilisateurs', 'suspendu')) {
    echo "- Ajout colonne utilisateurs.suspendu ... ";
    $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN suspendu TINYINT(1) NOT NULL DEFAULT 0");
    echo "OK\n";
} else {
    echo "- Colonne utilisateurs.suspendu déjà présente.\n";
}

// 2) index sur utilisateurs.suspendu
if (!indexExiste($pdo, 'utilisateurs', 'idx_suspendu')) {
    echo "- Ajout index idx_suspendu ... ";
    $pdo->exec("CREATE INDEX idx_suspendu ON utilisateurs (suspendu)");
    echo "OK\n";
} else {
    echo "- Index idx_suspendu déjà présent.\n";
}

// (Optionnel) Index utiles sur trajets si la table existe
try {
    // index sur date si la colonne existe
    foreach (['date_depart','created_at','date'] as $dateCol) {
        if (colonneExiste($pdo, 'trajets', $dateCol) && !indexExiste($pdo, 'trajets', "idx_trajets_$dateCol")) {
            echo "- Ajout index idx_trajets_$dateCol ... ";
            $pdo->exec("CREATE INDEX idx_trajets_$dateCol ON trajets ($dateCol)");
            echo "OK\n";
            break;
        }
    }
    // index sur statut si présent
    if (colonneExiste($pdo, 'trajets', 'statut') && !indexExiste($pdo, 'trajets', 'idx_trajets_statut')) {
        echo "- Ajout index idx_trajets_statut ... ";
        $pdo->exec("CREATE INDEX idx_trajets_statut ON trajets (statut)");
        echo "OK\n";
    }
} catch (Throwable $e) {
    echo "[INFO] Indices trajets ignorés: ".$e->getMessage()."\n";
}

echo "\nMigration terminée.\n";
