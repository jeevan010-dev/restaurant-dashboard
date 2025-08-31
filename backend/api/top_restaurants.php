<?php
// backend/api/top_restaurants.php
require_once __DIR__ . '/../helpers.php';

// GET params: start, end, min_amount, max_amount, min_hour, max_hour, n
$params = [
    'start' => $_GET['start'] ?? '',
    'end' => $_GET['end'] ?? '',
    'min_amount' => $_GET['min_amount'] ?? '',
    'max_amount' => $_GET['max_amount'] ?? '',
    'min_hour' => $_GET['min_hour'] ?? '',
    'max_hour' => $_GET['max_hour'] ?? '',
];

$n = max(1, min(10, (int)($_GET['n'] ?? 3)));

$orders = apply_filters(load_orders(), $params);
$restaurants = load_restaurants();

$revenueByRestaurant = [];
foreach ($orders as $o) {
    $rid = $o['restaurant_id'];
    if (!isset($revenueByRestaurant[$rid])) $revenueByRestaurant[$rid] = 0.0;
    $revenueByRestaurant[$rid] += (float)$o['order_amount'];
}

$result = [];
foreach ($revenueByRestaurant as $rid => $rev) {
    // try find restaurant info
    $rest = array_values(array_filter($restaurants, fn($r) => (string)$r['id'] === (string)$rid));
    $info = $rest ? $rest[0] : ['id' => $rid, 'name' => "Unknown #$rid", 'cuisine' => 'Unknown', 'location' => ''];
    $result[] = [
        'id' => $info['id'],
        'name' => $info['name'],
        'cuisine' => $info['cuisine'] ?? '',
        'location' => $info['location'] ?? '',
        'revenue' => round($rev, 2)
    ];
}

// sort and slice
usort($result, fn($a,$b) => $b['revenue'] <=> $a['revenue']);
$result = array_slice($result, 0, $n);

send_json(['data' => $result]);
