<?php
// backend/api/restaurants.php
require_once __DIR__ . '/../helpers.php';

// GET params: q (search), sort (name|location|cuisine), order (asc|desc), page, per_page
$q = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? 'name';
$order = strtolower($_GET['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = min(100, max(5, (int)($_GET['per_page'] ?? 20)));

$all = load_restaurants();

// search
if ($q !== '') {
    $all = array_values(array_filter($all, function($r) use ($q) {
        $q = mb_strtolower($q);
        $name = mb_strtolower($r['name'] ?? '');
        $cuisine = mb_strtolower($r['cuisine'] ?? '');
        $location = mb_strtolower($r['location'] ?? '');
        return mb_strpos($name, $q) !== false || mb_strpos($cuisine, $q) !== false || mb_strpos($location, $q) !== false;
    }));
}

// sort
$allowedSort = ['name', 'location', 'cuisine'];
if (!in_array($sort, $allowedSort)) $sort = 'name';
usort($all, function($a, $b) use ($sort, $order) {
    $va = strtolower($a[$sort] ?? '');
    $vb = strtolower($b[$sort] ?? '');
    if ($va === $vb) return 0;
    return ($va < $vb ? -1 : 1) * ($order === 'asc' ? 1 : -1);
});

// pagination
$total = count($all);
$offset = ($page - 1) * $per_page;
$items = array_slice($all, $offset, $per_page);

// send meta too
send_json(['data' => array_values($items), 'meta' => ['page' => $page, 'per_page' => $per_page, 'total' => $total]]);
