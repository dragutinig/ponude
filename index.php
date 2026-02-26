<?php
include 'config.php';

$stavke = $pdo->query("SELECT * FROM stavke ORDER BY naziv_stavke ASC")->fetchAll(PDO::FETCH_ASSOC);
$projekti = $pdo->query("SELECT * FROM projekti ORDER BY id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova ponuda</title>
    <style>
        :root {
            --bg: #f1f5f9;
            --card: #fff;
            --line: #dbe3ef;
            --txt: #0f172a;
            --muted: #64748b;
            --acc: #2563eb
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: Inter, Segoe UI, Arial, sans-serif;
            background: var(--bg);
            color: var(--txt)
        }

        .wrap {
            max-width: 1400px;
            margin: 18px auto;
            padding: 0 16px
        }

        .grid {
            display: grid;
            grid-template-columns: 1.25fr 1.75fr;
            gap: 14px
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .06)
        }

        .pad {
            padding: 14px
        }

        .title {
            margin: 0 0 10px;
            font-size: 20px
        }

        input,
        button,
        select {
            font: inherit
        }

        .txt {
            width: 100%;
            padding: 9px;
            border: 1px solid #cbd5e1;
            border-radius: 10px
        }

        .btn {
            border: none;
            border-radius: 10px;
            padding: 9px 11px;
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

        .r {
            background: #dc2626;
            color: #fff
        }

        .m {
            background: #e2e8f0
        }

        .tbl {
            width: 100%;
            border-collapse: collapse
        }

        .tbl th,
        .tbl td {
            border: 1px solid var(--line);
            padding: 8px
        }

        .tbl th {
            background: #f8fafc;
            text-align: left;
            font-size: 12px;
            letter-spacing: .03em;
            text-transform: uppercase;
            color: #334155
        }

        .muted {
            color: var(--muted);
            font-size: 12px
        }

        .stack {
            display: flex;
            flex-direction: column;
            gap: 8px
        }

        .row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center
        }

        .sum {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 10px;
            font-weight: 700
        }

        .popup {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .5);
            align-items: center;
            justify-content: center;
            z-index: 1000
        }

        .popup-box {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #dbe3ef;
            width: min(920px, 95vw);
            max-height: 80vh;
            overflow: auto;
            padding: 12px
        }

        .dekor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 8px
        }

        .dekor-item {
            border: 1px solid #dbe3ef;
            border-radius: 10px;
            padding: 8px;
            display: flex;
            gap: 8px;
            align-items: center;
            cursor: pointer
        }

        .dekor-item img {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #dbe3ef
        }

        @media (max-width:1100px) {
            .grid {
                grid-template-columns: 1fr
            }
        }