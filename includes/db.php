<?php
// includes/db.php — s'adapte automatiquement : Heroku (ClearDB/JawsDB) OU Docker local
if (!isset($pdo) || !($pdo instanceof PDO)) {

    // 1) Cherche un URL MySQL d’add-on Heroku
    $url = getenv('CLEARDB_DATABASE_URL') ?: getenv('JAWSDB_URL') ?: '';

    // 2) Si rien trouvé mais tu as configuré des variables manuelles :
    if ($url === '' && getenv('MYSQL_HOST') && getenv('MYSQL_DATABASE') && getenv('MYSQL_USER')) {
        $host = getenv('MYSQL_HOST');
        $db   = getenv('MYSQL_DATABASE');
        $user = getenv('MYSQL_USER');
        $pass = getenv('MYSQL_PASSWORD') ?: '';
        $port = getenv('MYSQL_PORT') ?: 3306;
    }
    // 3) Si on a un URL (ClearDB/JawsDB) -> le parser
    elseif ($url !== '') {
        $parts = parse_url($url); // ex: mysql://user:pass@host:port/db?reconnect=true
        if (!isset($parts['scheme']) || stripos($parts['scheme'], 'mysql') === false) {
            throw new RuntimeException('DATABASE_URL fourni mais non-MySQL.');
        }
        $host = $parts['host'] ?? 'localhost';
        $port = $parts['port'] ?? 3306;
        $user = $parts['user'] ?? '';
        $pass = $parts['pass'] ?? '';
        $db   = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
    }
    // 4) Sinon, **fallback Docker local**
    else {
        $host = 'db';
        $db   = 'EcoRide';
        $user = 'ecoride_user';
        $pass = 'ecoride_pass';
        $port = 3306;
    }

    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

