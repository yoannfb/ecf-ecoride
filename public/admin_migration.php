<?php
require __DIR__ . '/../includes/db.php';
header('Content-Type: text/plain; charset=utf-8');

function col(PDO $pdo,string $t,string $c):bool{
  $s=$pdo->prepare("SELECT COUNT(*) FROM information_schema.columns
                    WHERE table_schema=DATABASE() AND table_name=:t AND column_name=:c");
  $s->execute(['t'=>$t,'c'=>$c]);return (bool)$s->fetchColumn();
}
function idx(PDO $pdo,string $t,string $i):bool{
  $s=$pdo->prepare("SELECT COUNT(*) FROM information_schema.statistics
                    WHERE table_schema=DATABASE() AND table_name=:t AND index_name=:i");
  $s->execute(['t'=>$t,'i'=>$i]);return (bool)$s->fetchColumn();
}
echo "== Migration admin ==\n";
if(!col($pdo,'utilisateurs','suspendu')){
  echo "- add utilisateurs.suspendu... "; $pdo->exec("ALTER TABLE utilisateurs ADD COLUMN suspendu TINYINT(1) NOT NULL DEFAULT 0"); echo "OK\n";
}else echo "- utilisateurs.suspendu déjà là\n";
if(!idx($pdo,'utilisateurs','idx_suspendu')){
  echo "- add index idx_suspendu... "; $pdo->exec("CREATE INDEX idx_suspendu ON utilisateurs(suspendu)"); echo "OK\n";
}else echo "- idx_suspendu déjà là\n";
echo "Terminé.\n";
