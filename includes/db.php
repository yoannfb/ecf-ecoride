<?php
// Connexion à la base JawsDB sur Heroku
$url = parse_url(getenv("JAWSDB_URL"));

$host = $url["host"];
$dbname = substr($url["path"], 1);
$user = $url["user"];
$pass = $url["pass"];

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    // echo "Connexion réussie à la base de données !";
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>


