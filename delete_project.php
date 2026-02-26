<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];

    // Obriši sve stavke projekta
    $stmt = $pdo->prepare("DELETE FROM projekat_stavke WHERE projekat_id = ?");
    $stmt->execute([$id]);

    // Obriši sam projekat
    $stmt = $pdo->prepare("DELETE FROM projekti WHERE id = ?");
    $stmt->execute([$id]);

    // Vrati na početnu stranu
    header("Location: projects.php");
    exit;
}
