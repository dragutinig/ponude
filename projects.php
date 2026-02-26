<?php
include 'config.php';

$projekti = $pdo->query("SELECT * FROM projekti ORDER BY datum DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sr">

<head>
    <meta charset="UTF-8">
    <title>Lista projekata</title>
</head>

<body style="font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f6f9; color:#333;">

    <?php include 'menu.php'; ?>
    <div style="padding:20px;"></div>

    <div style="max-width:1000px; margin:auto; padding:20px;">
        <h1 style="text-align:center; background:#007BFF; color:white; padding:15px; border-radius:8px;">
            📂 Sačuvani projekti
        </h1>

        <table
            style="width:100%; border-collapse:collapse; margin-top:20px; background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
            <thead>
                <tr style="background:#007BFF; color:white; text-align:left;">
                    <th style="padding:12px; border:1px solid #ddd;">📌 Naziv</th>
                    <th style="padding:12px; border:1px solid #ddd;">📅 Datum</th>
                    <th style="padding:12px; border:1px solid #ddd;">💰 Ukupno</th>
                    <th style="padding:12px; border:1px solid #ddd; text-align:center;">➡ Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projekti as $p): ?>
                    <tr style="background:#fafafa;">
                        <td style="padding:12px; border:1px solid #ddd;"><?= htmlspecialchars($p['naziv_projekta']) ?></td>
                        <td style="padding:12px; border:1px solid #ddd;"><?= $p['datum'] ?></td>
                        <td style="padding:12px; border:1px solid #ddd; font-weight:bold;"><?= $p['ukupna_cena'] ?> RSD</td>
                        <td style="padding:12px; border:1px solid #ddd; text-align:center;">
                            <a href="project_details.php?id=<?= $p['id'] ?>"
                                style="background:#28a745; color:white; padding:6px 12px; text-decoration:none; border-radius:6px; font-size:14px; margin-right:6px;">
                                🔍 Pregledaj
                            </a>
                            <form method="post" action="delete_project.php" style="display:inline;"
                                onsubmit="return confirm('Da li ste sigurni da želite da obrišete ovu ponudu?');">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit"
                                    style="background:red; color:white; padding:6px 12px; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
                                    🗑 Obriši
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="text-align:center; margin-top:20px;">
            <a href="index.php"
                style="background:#007BFF; color:white; padding:12px 20px; text-decoration:none; border-radius:6px; font-size:16px;">
                ➕ Kreiraj novu ponudu
            </a>
        </div>
    </div>
</body>

</html>