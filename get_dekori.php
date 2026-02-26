<?php
include 'config.php';

$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = max(1, min(100, (int)($_GET['limit'] ?? 24)));
$offset = ($page - 1) * $limit;

$where = '';
$params = [];
if ($q !== '') {
    $where = ' WHERE sifra LIKE ? OR naziv LIKE ? ';
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
}

$sql = 'SELECT id, sifra, naziv, slika FROM egger' . $where . ' ORDER BY id ASC LIMIT ' . $limit . ' OFFSET ' . $offset;
$stmt = $pdoDekor->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$countSql = 'SELECT COUNT(*) FROM egger' . $where;
$countStmt = $pdoDekor->prepare($countSql);
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'items' => $items,
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'has_more' => ($offset + count($items)) < $total,
], JSON_UNESCAPED_UNICODE);
