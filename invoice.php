<?php
include 'config.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM projekti WHERE id = ?");
$stmt->execute([$id]);
$projekat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$projekat) {
    http_response_code(404);
    echo 'Ponuda nije pronađena.';
    exit;
}

$stmt = $pdo->prepare("SELECT ps.*, s.naziv_stavke, s.default_jedinica FROM projekat_stavke ps JOIN stavke s ON s.id = ps.stavka_id WHERE ps.projekat_id = ? ORDER BY ps.id ASC");
$stmt->execute([$id]);
$stavke = $stmt->fetchAll(PDO::FETCH_ASSOC);

$metaPath = __DIR__ . '/storage/meta/project_' . $id . '.json';
$meta = [];
if (is_file($metaPath)) {
    $meta = json_decode((string)file_get_contents($metaPath), true) ?: [];
}

$showQty = isset($_GET['show_qty']) ? (int)$_GET['show_qty'] : (int)($meta['show_kolicina'] ?? 1);
$showPrice = isset($_GET['show_price']) ? (int)$_GET['show_price'] : (int)($meta['show_cena'] ?? 1);
$kupacNaziv = trim($_GET['kupac_naziv'] ?? ($meta['kupac_naziv'] ?? ''));
$kupacKontakt = trim($_GET['kupac_kontakt'] ?? ($meta['kupac_kontakt'] ?? ''));
$kupacAdresa = trim($_GET['kupac_adresa'] ?? ($meta['kupac_adresa'] ?? ''));
$note = trim($_GET['note'] ?? ($meta['napomena'] ?? ''));
$logo = trim($_GET['logo'] ?? '');
$accent = trim($_GET['accent'] ?? '#2563eb');
$materialTotal = (float)($meta['material_total'] ?? 0);
$profitPercent = (float)($meta['profit_percent'] ?? 0);
$profitValue = (float)($meta['profit_value'] ?? 0);
?>
<!DOCTYPE html>
<html lang="sr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Štampa ponude #<?= $projekat['id'] ?></title>
    <style>
        :root {
            --accent: <?= htmlspecialchars($accent) ?>
        }

        body {
            margin: 0;
            background: #eef2f7;
            font-family: Inter, Segoe UI, Arial, sans-serif;
            color: #0f172a
        }

        .wrap {
            max-width: 1100px;
            margin: 16px auto;
            padding: 0 14px
        }

        .card {
            background: #fff;
            border: 1px solid #dbe3ef;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, .08)
        }

        .row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center
        }

        .txt {
            padding: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 8px
        }

        .btn {
            padding: 9px 12px;
            border: none;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            cursor: pointer;
            text-decoration: none
        }

        .top {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 12px
        }

        .tbl {
            width: 100%;
            border-collapse: collapse
        }

        .tbl th,
        .tbl td {
            border: 1px solid #dbe3ef;
            padding: 9px
        }

        .tbl th {
            background: #f8fafc;
            text-align: left;
            font-size: 13px
        }

        .sum {
            margin-top: 10px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 6px
        }

        .sum div {
            padding: 8px;
            background: #f8fafc;
            border-radius: 8px
        }

        @media print {

            .controls,
            .topnav {
                display: none
            }

            body {
                background: #fff
            }

            .card {
                border: none;
                box-shadow: none;
                padding: 0
            }
        }
    </style>
</head>

<body>
    <?php include 'menu.php'; ?>
    <div class="wrap">
        <form class="card controls" method="get" style="margin-bottom:10px;">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="row">
                <label><input type="checkbox" name="show_qty" value="1" <?= $showQty ? 'checked' : '' ?>>
                    Količina</label>
                <label><input type="checkbox" name="show_price" value="1" <?= $showPrice ? 'checked' : '' ?>>
                    Cena</label>
                <input class="txt" name="kupac_naziv" value="<?= htmlspecialchars($kupacNaziv) ?>" placeholder="Kupac">
                <input class="txt" name="kupac_kontakt" value="<?= htmlspecialchars($kupacKontakt) ?>"
                    placeholder="Kontakt">
                <input class="txt" name="kupac_adresa" value="<?= htmlspecialchars($kupacAdresa) ?>"
                    placeholder="Adresa">
                <input class="txt" name="logo" value="<?= htmlspecialchars($logo) ?>" placeholder="URL logo/foto">
                <input class="txt" name="accent" value="<?= htmlspecialchars($accent) ?>" placeholder="#2563eb"
                    style="width:110px;">
                <button class="btn" type="submit">Primeni</button>
            </div>
        </form>

        <div class="card">
            <div class="top">
                <div class="row">
                    <?php if ($logo !== ''): ?><img src="<?= htmlspecialchars($logo) ?>"
                            style="width:62px;height:62px;object-fit:contain;border:1px solid #dbe3ef;border-radius:8px"
                            alt="logo"><?php endif; ?>
                    <div>
                        <h2 style="margin:0;color:var(--accent)">Ponuda / Predračun</h2>
                        <div>Broj: #<?= $projekat['id'] ?> · Datum: <?= htmlspecialchars($projekat['datum']) ?></div>
                        <?php if ($kupacNaziv !== ''): ?><div><strong>Kupac:</strong>
                                <?= htmlspecialchars($kupacNaziv) ?></div><?php endif; ?>
                        <?php if ($kupacKontakt !== ''): ?><div><strong>Kontakt:</strong>
                                <?= htmlspecialchars($kupacKontakt) ?></div><?php endif; ?>
                        <?php if ($kupacAdresa !== ''): ?><div><strong>Adresa:</strong>
                                <?= htmlspecialchars($kupacAdresa) ?></div><?php endif; ?>
                    </div>
                </div>
                <div><strong><?= htmlspecialchars($projekat['naziv_projekta']) ?></strong></div>
            </div>

            <table class="tbl">
                <tr>
                    <th>Stavka</th>
                    <?php if ($showQty): ?><th>Količina</th><?php endif; ?>
                    <th>Jedinica</th>
                    <?php if ($showPrice): ?><th>Cena po jedinici</th><?php endif; ?>
                    <th>Ukupno</th>
                </tr>
                <?php foreach ($stavke as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['naziv_stavke']) ?></td>
                        <?php if ($showQty): ?><td><?= (float)$s['kolicina'] ?></td><?php endif; ?>
                        <td><?= htmlspecialchars($s['default_jedinica']) ?></td>
                        <?php if ($showPrice): ?><td><?= number_format((float)$s['cena_po_jedinici'], 2) ?> RSD</td>
                        <?php endif; ?>
                        <td><?= number_format((float)$s['ukupno'], 2) ?> RSD</td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <div class="sum">
                <div>Materijal</div>
                <div><?= number_format($materialTotal, 2) ?> RSD</div>
                <div>Zarada (<?= number_format($profitPercent, 2) ?>%)</div>
                <div><?= number_format($profitValue, 2) ?> RSD</div>
                <div><strong>Konačna cena</strong></div>
                <div><strong><?= number_format((float)$projekat['ukupna_cena'], 2) ?> RSD</strong></div>
            </div>

            <?php if ($note !== ''): ?><p><strong>Napomena:</strong> <?= htmlspecialchars($note) ?></p><?php endif; ?>

            <div class="row" style="margin-top:14px">
                <button class="btn" onclick="window.print();return false;">Štampaj</button>
                <a class="btn"
                    href="invoice_pdf.php?id=<?= $id ?>&show_qty=<?= $showQty ?>&show_price=<?= $showPrice ?>&kupac_naziv=<?= urlencode($kupacNaziv) ?>&kupac_kontakt=<?= urlencode($kupacKontakt) ?>&kupac_adresa=<?= urlencode($kupacAdresa) ?>&note=<?= urlencode($note) ?>&logo=<?= urlencode($logo) ?>&accent=<?= urlencode($accent) ?>">Sačuvaj
                    PDF</a>
            </div>
        </div>
    </div>
</body>

</html>