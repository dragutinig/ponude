<?php
include 'config.php';

$id = (int)$_POST['id'];
$kolicine = $_POST['kolicina'];
$cene = $_POST['cena'];

$ukupno = 0;

foreach ($kolicine as $stavka_id => $kolicina) {
    $cena = $cene[$stavka_id];
    $iznos = $kolicina * $cena;
    $ukupno += $iznos;

    $stmt = $pdo->prepare("
        UPDATE projekat_stavke
        SET kolicina = ?, cena_po_jedinici = ?, ukupno = ?
        WHERE projekat_id = ? AND stavka_id = ?
    ");
    $stmt->execute([$kolicina, $cena, $iznos, $id, $stavka_id]);
}

// ažuriraj ukupan iznos u projektu
$stmt = $pdo->prepare("UPDATE projekti SET ukupna_cena = ? WHERE id = ?");
$stmt->execute([$ukupno, $id]);

header("Location: project_details.php?id=$id");
exit;
