<?php
include 'config.php';

$id = (int)$_GET['id'];

// Uzmi projekat
$stmt = $pdo->prepare("SELECT * FROM projekti WHERE id = ?");
$stmt->execute([$id]);
$projekat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$projekat) {
    die("Projekat nije pronađen!");
}

// Uzmi stavke iz baze
$stmt = $pdo->prepare("
    SELECT ps.*, s.naziv_stavke, s.default_jedinica 
    FROM projekat_stavke ps
    JOIN stavke s ON ps.stavka_id = s.id
    WHERE ps.projekat_id = ?
    ORDER BY ps.id
");
$stmt->execute([$id]);
$stavke = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="sr">

<head>
    <meta charset="UTF-8">
    <title>Detalji ponude - <?= htmlspecialchars($projekat['naziv_projekta']) ?></title>
</head>

<body style="font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f6f9; color:#333;">

    <?php include 'menu.php'; ?>
    <div style="padding:20px;"></div>

    <div style="max-width:1000px; margin:auto; padding:20px;">
        <h1 style="text-align:center; background:#007BFF; color:white; padding:15px; border-radius:8px;">
            📋 Ponuda: <?= htmlspecialchars($projekat['naziv_projekta']) ?>
        </h1>
        <p style="text-align:center; font-size:14px; color:#555; margin-top:8px;">
            📅 Datum: <?= $projekat['datum'] ?>
        </p>

        <form method="post" action="update_project.php"
            style="background:white; padding:20px; border-radius:8px; margin-top:20px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
            <input type="hidden" name="id" value="<?= $projekat['id'] ?>">

            <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                <thead>
                    <tr style="background:#007BFF; color:white; text-align:left;">
                        <th style="padding:10px; border:1px solid #ddd;">Stavka</th>
                        <th style="padding:10px; border:1px solid #ddd; width:120px;">Količina</th>
                        <th style="padding:10px; border:1px solid #ddd; width:150px;">Cena po jedinici (RSD)</th>
                        <th style="padding:10px; border:1px solid #ddd; width:120px;">Ukupno (RSD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stavke as $s): ?>
                        <tr style="background:#fafafa;">
                            <td style="padding:10px; border:1px solid #ddd;"><?= htmlspecialchars($s['naziv_stavke']) ?>
                                (<?= $s['default_jedinica'] ?>)</td>
                            <td style="padding:10px; border:1px solid #ddd;">
                                <input type="number" name="kolicina[<?= $s['stavka_id'] ?>]" value="<?= $s['kolicina'] ?>"
                                    min="0" style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;">
                            </td>
                            <td style="padding:10px; border:1px solid #ddd;">
                                <input type="number" name="cena[<?= $s['stavka_id'] ?>]"
                                    value="<?= $s['cena_po_jedinici'] ?>" step="0.01"
                                    style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px;">
                            </td>
                            <td style="padding:10px; border:1px solid #ddd; font-weight:bold; text-align:right;">
                                <?= $s['ukupno'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3 style="text-align:right; margin-bottom:20px; font-size:18px;">Ukupno:
                <span style="color:#007BFF; font-weight:bold;"><?= $projekat['ukupna_cena'] ?></span> RSD
            </h3>

            <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                <button type="submit"
                    style="background:#007BFF; color:white; padding:12px 20px; border:none; border-radius:6px; cursor:pointer; font-size:16px;">
                    💾 Sačuvaj izmene
                </button>
                <a href="invoice.php?id=<?= $projekat['id'] ?>"
                    style="background:#28a745; color:white; padding:12px 20px; text-decoration:none; border-radius:6px; font-size:16px;">
                    🖨 Ispiši
                </a>
            </div>
        </form>
    </div>
</body>

</html>