<?php
require 'vendor/autoload.php';
include 'config.php';

use Dompdf\Dompdf;

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM projekti WHERE id = ?");
$stmt->execute([$id]);
$projekat = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$projekat) {
    die('Ponuda nije pronađena.');
}

$stmt = $pdo->prepare("SELECT ps.*, s.naziv_stavke, s.default_jedinica FROM projekat_stavke ps JOIN stavke s ON s.id = ps.stavka_id WHERE ps.projekat_id = ? ORDER BY ps.id ASC");
$stmt->execute([$id]);
$stavke = $stmt->fetchAll(PDO::FETCH_ASSOC);

$metaPath = __DIR__ . '/storage/meta/project_' . $id . '.json';
$meta = is_file($metaPath) ? (json_decode((string)file_get_contents($metaPath), true) ?: []) : [];

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

$logoHtml = '';
if ($logo !== '') {
    $logoHtml = '<img src="' . htmlspecialchars($logo, ENT_QUOTES, 'UTF-8') . '" style="width:62px;height:62px;object-fit:contain;border:1px solid #dbe3ef;border-radius:8px;" alt="logo">';
}

$html = '<html><head><meta charset="UTF-8"><style>
body{font-family:DejaVu Sans,Arial,sans-serif;color:#0f172a;font-size:12px}
.wrap{border:1px solid #dbe3ef;border-radius:10px;padding:18px}
.top{width:100%;margin-bottom:10px}
th,td{border:1px solid #dbe3ef;padding:7px}table{width:100%;border-collapse:collapse}th{background:#f8fafc;text-align:left}
.acc{color:' . htmlspecialchars($accent, ENT_QUOTES, 'UTF-8') . '}
.sum td{background:#f8fafc}
</style></head><body><div class="wrap">';

$html .= '<table class="top" border="0"><tr><td width="72">' . $logoHtml . '</td><td><h2 class="acc" style="margin:0">Ponuda / Predračun</h2><div>Broj: #' . (int)$projekat['id'] . ' · Datum: ' . htmlspecialchars((string)$projekat['datum'], ENT_QUOTES, 'UTF-8') . '</div>';
if ($kupacNaziv !== '') {
    $html .= '<div><strong>Kupac:</strong> ' . htmlspecialchars($kupacNaziv, ENT_QUOTES, 'UTF-8') . '</div>';
}
if ($kupacKontakt !== '') {
    $html .= '<div><strong>Kontakt:</strong> ' . htmlspecialchars($kupacKontakt, ENT_QUOTES, 'UTF-8') . '</div>';
}
if ($kupacAdresa !== '') {
    $html .= '<div><strong>Adresa:</strong> ' . htmlspecialchars($kupacAdresa, ENT_QUOTES, 'UTF-8') . '</div>';
}
$html .= '</td><td align="right"><strong>' . htmlspecialchars($projekat['naziv_projekta'], ENT_QUOTES, 'UTF-8') . '</strong></td></tr></table>';

$html .= '<table><tr><th>Stavka</th>';
if ($showQty) {
    $html .= '<th>Količina</th>';
}
$html .= '<th>Jedinica</th>';
if ($showPrice) {
    $html .= '<th>Cena po jedinici</th>';
}
$html .= '<th>Ukupno</th></tr>';

foreach ($stavke as $s) {
    $html .= '<tr><td>' . htmlspecialchars($s['naziv_stavke'], ENT_QUOTES, 'UTF-8') . '</td>';
    if ($showQty) {
        $html .= '<td>' . (float)$s['kolicina'] . '</td>';
    }
    $html .= '<td>' . htmlspecialchars($s['default_jedinica'], ENT_QUOTES, 'UTF-8') . '</td>';
    if ($showPrice) {
        $html .= '<td>' . number_format((float)$s['cena_po_jedinici'], 2) . ' RSD</td>';
    }
    $html .= '<td>' . number_format((float)$s['ukupno'], 2) . ' RSD</td></tr>';
}

$colspan = 2 + ($showQty ? 1 : 0) + ($showPrice ? 1 : 0);
$html .= '<tr><td colspan="' . $colspan . '" align="right"><strong>Konačna cena:</strong></td><td><strong>' . number_format((float)$projekat['ukupna_cena'], 2) . ' RSD</strong></td></tr>';
$html .= '</table>';

$html .= '<table class="sum" style="margin-top:10px"><tr><td>Materijal</td><td>' . number_format($materialTotal, 2) . ' RSD</td></tr><tr><td>Zarada (' . number_format($profitPercent, 2) . '%)</td><td>' . number_format($profitValue, 2) . ' RSD</td></tr></table>';
if ($note !== '') {
    $html .= '<p><strong>Napomena:</strong> ' . htmlspecialchars($note, ENT_QUOTES, 'UTF-8') . '</p>';
}

$html .= '</div></body></html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('ponuda_' . $id . '.pdf', ['Attachment' => true]);
exit;
