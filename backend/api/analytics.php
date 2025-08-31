<?php
// backend/api/analytics.php
require_once __DIR__ . '/../helpers.php';

// restaurant_id required
if (empty($_GET['restaurant_id'])) {
    send_json(['error' => 'restaurant_id required'], 400);
}

$params = [
    'restaurant_id' => $_GET['restaurant_id'] ?? null,
    'start' => $_GET['start'] ?? '',
    'end' => $_GET['end'] ?? '',
    'min_amount' => $_GET['min_amount'] ?? '',
    'max_amount' => $_GET['max_amount'] ?? '',
    'min_hour' => $_GET['min_hour'] ?? '',
    'max_hour' => $_GET['max_hour'] ?? '',
];

$orders = apply_filters(load_orders(), $params);

// aggregate per day
$days = [];
foreach ($orders as $o) {
    $dt = new DateTimeImmutable($o['order_time']);
    $dKey = $dt->format('Y-m-d');
    if (!isset($days[$dKey])) {
        $days[$dKey] = ['date' => $dKey, 'orders_count' => 0, 'revenue' => 0, 'hours' => []];
    }
    $days[$dKey]['orders_count']++;
    $days[$dKey]['revenue'] += (float)$o['order_amount'];
    $h = (int)$dt->format('G');
    $days[$dKey]['hours'][$h] = ($days[$dKey]['hours'][$h] ?? 0) + 1;
}

$res = [];
foreach ($days as $d => $v) {
    arsort($v['hours']);
    $peak_hour = !empty($v['hours']) ? (int)key($v['hours']) : null;
    $avg = $v['orders_count'] > 0 ? round($v['revenue'] / $v['orders_count'], 2) : 0.0;
    $res[] = [
        'date' => $d,
        'orders_count' => $v['orders_count'],
        'revenue' => round($v['revenue'], 2),
        'avg_order_value' => $avg,
        'peak_hour' => $peak_hour
    ];
}

// sort ascending by date (so charts are ordered)
usort($res, function($a, $b) { return strcmp($a['date'], $b['date']); });

send_json(['data' => $res]);
