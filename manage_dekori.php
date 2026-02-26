<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $stmt = $pdoDekor->prepare("INSERT INTO egger (sifra, naziv, slika) VALUES (?, ?, ?)");
        $stmt->execute([trim($_POST['sifra']), trim($_POST['naziv']), trim($_POST['slika'])]);
    }

    if ($_POST['action'] === 'update') {
        $stmt = $pdoDekor->prepare("UPDATE egger SET sifra=?, naziv=?, slika=? WHERE id=?");
        $stmt->execute([trim($_POST['sifra']), trim($_POST['naziv']), trim($_POST['slika']), (int)$_POST['id']]);
    }

    if ($_POST['action'] === 'duplicate') {
        $stmt = $pdoDekor->prepare("INSERT INTO egger (sifra, naziv, slika) SELECT CONCAT(sifra, '-K'), CONCAT(naziv, ' (kopija)'), slika FROM egger WHERE id=?");
        $stmt->execute([(int)$_POST['id']]);
    }

    if ($_POST['action'] === 'delete') {
        $stmt = $pdoDekor->prepare("DELETE FROM egger WHERE id=?");
        $stmt->execute([(int)$_POST['id']]);
    }

    header('Location: manage_dekori.php');
    exit;
}

$dekori = $pdoDekor->query("SELECT * FROM egger ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upravljanje dekorima</title>
    <style>
        body {
            margin: 0;
            font-family: Inter, Segoe UI, Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a
        }

        .wrap {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 16px
        }

        .card {
            background: #fff;
            border: 1px solid #dbe3ef;
            border-radius: 14px;
            padding: 16px
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        th,
        td {
            border: 1px solid #dbe3ef;
            padding: 8px
        }

        th {
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
            font-weight: 600;
            cursor: pointer
        }

        .b {
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

        .grid {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr auto;
            gap: 8px;
            margin-bottom: 14px
        }
    </style>
</head>

<body>
    <?php include 'menu.php'; ?>
    <div class="wrap">
        <div class="card">
            <h2>Dekori / repro materijali</h2>
            <form method="post" class="grid">
                <input type="hidden" name="action" value="add">
                <input name="sifra" placeholder="Šifra" required>
                <input name="naziv" placeholder="Naziv" required>
                <input name="slika" placeholder="Naziv slike (npr. dekor.jpg)">
                <button class="btn g" type="submit">Dodaj</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Šifra</th>
                        <th>Naziv</th>
                        <th>Slika fajl</th>
                        <th>Preview</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dekori as $d): ?>
                        <tr>
                            <form method="post">
                                <td><?= $d['id'] ?><input type="hidden" name="id" value="<?= $d['id'] ?>"></td>
                                <td><input name="sifra" value="<?= htmlspecialchars($d['sifra']) ?>"></td>
                                <td><input name="naziv" value="<?= htmlspecialchars($d['naziv']) ?>"></td>
                                <td><input name="slika" value="<?= htmlspecialchars($d['slika']) ?>"></td>
                                <td><?php if (!empty($d['slika'])): ?><img src="slike/<?= htmlspecialchars($d['slika']) ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid #dbe3ef;"><?php endif; ?></td>
                                <td style="white-space:nowrap;display:flex;gap:6px;">
                                    <button class="btn b" type="submit" name="action" value="update">Sačuvaj</button>
                                    <button class="btn y" type="submit" name="action" value="duplicate">Dupliraj</button>
                                    <button class="btn r" type="submit" name="action" value="delete" onclick="return confirm('Obrisati dekor?')">Obriši</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>