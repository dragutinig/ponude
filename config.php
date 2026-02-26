<?php

function qm_connect_db(string $dbName): PDO
{
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';

    $hosts = [];
    $envHost = getenv('DB_HOST');
    if ($envHost) {
        $hosts[] = $envHost;
    }
    $hosts[] = '127.0.0.1';
    $hosts[] = 'localhost';

    $lastError = null;
    foreach (array_unique($hosts) as $host) {
        try {
            $pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            $lastError = $e->getMessage();
        }
    }

    throw new RuntimeException($lastError ?: 'Nepoznata greška konekcije na bazu.');
}

function qm_boot_error(string $message): void
{
    http_response_code(500);
    echo '<!doctype html><html lang="sr"><head><meta charset="utf-8"><title>Greška konekcije</title>';
    echo '<style>body{font-family:Segoe UI,Arial,sans-serif;background:#f1f5f9;margin:0;padding:32px;color:#0f172a}.box{max-width:780px;margin:0 auto;background:#fff;border:1px solid #dbe3ef;border-radius:12px;padding:18px}.muted{color:#64748b}</style>';
    echo '</head><body><div class="box"><h2>Aplikacija trenutno ne može da se poveže sa bazom.</h2>';
    echo '<p>Proveri da li je MySQL pokrenut i da li su parametri konekcije tačni.</p>';
    echo '<p class="muted">Detalj: ' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p></div></body></html>';
    exit;
}

try {
    $pdo = qm_connect_db('ponuda_db');
} catch (RuntimeException $e) {
    qm_boot_error('Ponuda DB: ' . $e->getMessage());
}

try {
    $pdoDekor = qm_connect_db('iverali');
} catch (RuntimeException $e) {
    qm_boot_error('Dekor DB: ' . $e->getMessage());
}
