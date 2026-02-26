<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$naziv = trim($_POST['naziv_projekta'] ?? '');
$stavkaIds = $_POST['stavka_id'] ?? [];
$kolicine = $_POST['kolicina'] ?? [];
$cene = $_POST['cena'] ?? [];
$dekorIds = $_POST['dekor_id'] ?? [];

if ($naziv === '' || empty($stavkaIds)) {
    header('Location: index.php');
    exit;
}

$materialTotal = 0;
$rows = [];
for ($i = 0; $i < count($stavkaIds); $i++) {
    $sid = (int)$stavkaIds[$i];
    $qty = (float)($kolicine[$i] ?? 0);
    $price = (float)($cene[$i] ?? 0);
    if ($sid <= 0 || $qty <= 0) {
        continue;
    }
    $rowTotal = $qty * $price;
    $materialTotal += $rowTotal;
    $rows[] = [
        'stavka_id' => $sid,
        'kolicina' => $qty,
        'cena' => $price,
        'ukupno' => $rowTotal,
        'dekor_id' => !empty($dekorIds[$i]) ? (int)$dekorIds[$i] : null
    ];
}

if (empty($rows)) {
    header('Location: index.php');
    exit;
}

$profitPercent = max(0, (float)($_POST['profit_percent'] ?? 0));
$profitValue = $materialTotal * $profitPercent / 100;
$finalTotal = $materialTotal + $profitValue;

$stmt = $pdo->prepare("INSERT INTO projekti (naziv_projekta, ukupna_cena) VALUES (?, ?)");
$stmt->execute([$naziv, $finalTotal]);
$projekatId = (int)$pdo->lastInsertId();

$ins = $pdo->prepare("INSERT INTO projekat_stavke (projekat_id, stavka_id, kolicina, cena_po_jedinici, ukupno, dekor_id) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($rows as $r) {
    $ins->execute([$projekatId, $r['stavka_id'], $r['kolicina'], $r['cena'], $r['ukupno'], $r['dekor_id']]);
}

$metaDir = __DIR__ . '/storage/meta';
if (!is_dir($metaDir)) {
    @mkdir($metaDir, 0777, true);
}
$meta = [
    'kupac_naziv' => trim($_POST['kupac_naziv'] ?? ''),
    'kupac_kontakt' => trim($_POST['kupac_kontakt'] ?? ''),
    'kupac_adresa' => trim($_POST['kupac_adresa'] ?? ''),
    'napomena' => trim($_POST['napomena'] ?? ''),
    'show_kolicina' => isset($_POST['show_kolicina']) ? 1 : 0,
    'show_cena' => isset($_POST['show_cena']) ? 1 : 0,
    'profit_percent' => $profitPercent,
    'material_total' => $materialTotal,
    'profit_value' => $profitValue,
    'final_total' => $finalTotal,
];
@file_put_contents($metaDir . '/project_' . $projekatId . '.json', json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

header('Location: project_details.php?id=' . $projekatId);
exit;
