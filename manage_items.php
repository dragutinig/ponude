<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $stmt = $pdo->prepare("INSERT INTO stavke (naziv_stavke, default_jedinica, default_cena) VALUES (?, ?, ?)");
        $stmt->execute([trim($_POST['naziv']), trim($_POST['jedinica']), (float)$_POST['cena']]);
    }

    if ($_POST['action'] === 'update') {
        $stmt = $pdo->prepare("UPDATE stavke SET naziv_stavke=?, default_jedinica=?, default_cena=? WHERE id=?");
        $stmt->execute([trim($_POST['naziv']), trim($_POST['jedinica']), (float)$_POST['cena'], (int)$_POST['id']]);
    }

    if ($_POST['action'] === 'duplicate') {
        $stmt = $pdo->prepare("INSERT INTO stavke (naziv_stavke, default_jedinica, default_cena) SELECT CONCAT(naziv_stavke, ' (kopija)'), default_jedinica, default_cena FROM stavke WHERE id=?");
        $stmt->execute([(int)$_POST['id']]);
    }

    if ($_POST['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM stavke WHERE id=?");
        $stmt->execute([(int)$_POST['id']]);
    }

    header("Location: manage_items.php");
    exit;
}

$stavke = $pdo->query("SELECT * FROM stavke ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upravljanje stavkama</title>
    <style>
        body {
            margin: 0;
            font-family: Inter, Segoe UI, Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a
        }

        .wrap {
            max-width: 1150px;
            margin: 20px auto;
            padding: 0 16px
        }

        .card {
            background: #fff;
            border: 1px solid #dbe3ef;
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .05)
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        .t td,
        .t th {
            border: 1px solid #dbe3ef;
            padding: 8px
        }

        .t th {
            background: #f8fafc;
            text-align: left
        }

        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 8px
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 8px 10px;
            cursor: pointer;
            font-weight: 600
        }

        .p {
            background: #2563eb;
            color: #fff
        }

        .g {
            background: #16a34a;
            color: #fff
        }

        .y {
            background: #f59e0b;
            color: #fff
        }

        .r {
            background: #dc2626;
            color: #fff
        }

        .row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 10px
        }
    </style>
</head>

<body>
    <?php include 'menu.php'; ?>
    <div class="wrap">
        <div class="card">
            <h2>Biblioteka stavki za ponude</h2>
            <form method="post" class="row" style="margin:12px 0 18px;">
                <input type="hidden" name="action" value="add">
                <input name="naziv" placeholder="Naziv stavke" required>
                <input name="jedinica" placeholder="Jedinica (kom, m2...)" required>
                <input name="cena" type="number" step="0.01" min="0" placeholder="Default cena" required>
                <button class="btn g" type="submit">Dodaj</button>
            </form>

            <table class="t">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Naziv</th>
                        <th>Jedinica</th>
                        <th>Cena</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stavke as $s): ?>
                        <tr>
                            <form method="post">
                                <td><?= $s['id'] ?><input type="hidden" name="id" value="<?= $s['id'] ?>"></td>
                                <td><input name="naziv" value="<?= htmlspecialchars($s['naziv_stavke']) ?>"></td>
                                <td><input name="jedinica" value="<?= htmlspecialchars($s['default_jedinica']) ?>"></td>
                                <td><input type="number" step="0.01" min="0" name="cena" value="<?= $s['default_cena'] ?>">
                                </td>
                                <td style="white-space:nowrap;display:flex;gap:6px;">
                                    <button class="btn p" type="submit" name="action" value="update">Sačuvaj</button>
                                    <button class="btn y" type="submit" name="action" value="duplicate">Dupliraj</button>
                                    <button class="btn r" type="submit" name="action" value="delete"
                                        onclick="return confirm('Obrisati stavku?')">Obriši</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>