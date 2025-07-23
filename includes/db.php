<?php
if (getenv("JAWSDB_URL")) {
    // 🌐 Environnement Heroku (prod)
    $url = parse_url(getenv("JAWSDB_URL"));
    $host = $url["host"];
    $dbname = substr($url["path"], 1);
    $user = $url["user"];
    $pass = $url["pass"];
} else {
    // 🐳 Environnement Docker (local)
    $host = 'db';
    $dbname = 'EcoRide';
    $user = 'ecoride_user';
    $pass = 'ecoride_pass';
}

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";


try {
    $pdo = new PDO('mysql:host=db;dbname=EcoRide;charset=utf8mb4', 'root', 'root');
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
