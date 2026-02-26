<?php
// konekcija na bazu
$host = 'localhost';
$db   = 'iverali';
$user = 'root'; // promeni po potrebi
$pass = '';     // promeni po potrebi
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Ne mogu se povezati: " . $e->getMessage());
}

// upit za dekore i njihove cene
$sql = "SELECT e.sifra, e.naziv, e.slika, c.tip AS c_tip, c.cena, c.dimenzija
        FROM egger e
        LEFT JOIN cene_iverali c ON e.sifra = c.sifra
        ORDER BY e.sifra, c.tip";

$stmt = $pdo->query($sql);
$dekori = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="sr">

<head>
    <meta charset="UTF-8">
    <title>Lista dekora sa cenama</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }

        .dekor {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
            background: #fff;
            display: flex;
            align-items: center;
        }

        .dekor img {
            max-width: 120px;
            max-height: 120px;
            margin-right: 15px;
            border: 1px solid #ddd;
        }

        .info {
            display: flex;
            flex-direction: column;
        }

        .sifra {
            font-weight: bold;
            font-size: 18px;
        }

        .tip,
        .dimenzija,
        .cena {
            margin-top: 5px;
        }

        .cena {
            color: green;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1>Lista dekora sa cenama</h1>

    <?php
    $lastSifra = '';
    foreach ($dekori as $dekor):
        // grupisanje po sifri
        if ($dekor['sifra'] !== $lastSifra):
            if ($lastSifra !== '') echo "</div>"; // zatvori prethodni dekor
            $lastSifra = $dekor['sifra'];
    ?>
            <div class="dekor">
                <?php if (!empty($dekor['slika'])): ?>
                    <img src="slike/<?= htmlspecialchars($dekor['slika']) ?>" alt="<?= htmlspecialchars($dekor['sifra']) ?>">
                <?php else: ?>
                    <div
                        style="width:120px; height:120px; background:#eee; display:flex; align-items:center; justify-content:center;">
                        Nema slike</div>
                <?php endif; ?>
                <div class="info">
                    <div class="sifra"><?= htmlspecialchars($dekor['sifra']) ?> - <?= htmlspecialchars($dekor['naziv']) ?></div>
                <?php endif; ?>

                <?php if (!empty($dekor['cena'])): ?>
                    <div class="tip">Tip: <?= htmlspecialchars($dekor['c_tip']) ?></div>
                    <div class="dimenzija">Dimenzija: <?= htmlspecialchars($dekor['dimenzija']) ?></div>
                    <div class="cena">Cena: <?= number_format($dekor['cena'], 2, ',', '.') ?> RSD</div>
                <?php else: ?>
                    <div class="tip">Tip: -</div>
                    <div class="dimenzija">Dimenzija: -</div>
                    <div class="cena">Cena: -</div>
                <?php endif; ?>
                </div>

            <?php endforeach; ?>

</body>

</html>