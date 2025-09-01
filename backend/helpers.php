<?php

function load_orders(): array {
    $path = __DIR__ . '/../data/orders.json';
    if (!file_exists($path)) return [];
    $json = file_get_contents($path);
    $arr = json_decode($json, true);
    return is_array($arr) ? $arr : [];
}

function load_restaurants(): array {
    $path = __DIR__ . '/../data/restaurants.json';
    if (!file_exists($path)) return [];
    $json = file_get_contents($path);
    $arr = json_decode($json, true);
    return is_array($arr) ? $arr : [];
}

function send_json($payload, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function apply_filters(array $orders, array $params): array {
    return array_values(array_filter($orders, function($o) use ($params) {
        // validate fields exist
        if (!isset($o['order_time']) || !isset($o['order_amount']) || !isset($o['restaurant_id'])) return false;

        // normalize
        $dt = new DateTimeImmutable($o['order_time']);
        $date = $dt->format('Y-m-d');
        $hour = (int)$dt->format('G'); // 0-23
        $amount = (float)$o['order_amount'];

        if (!empty($params['restaurant_id']) && (string)$o['restaurant_id'] !== (string)$params['restaurant_id']) {
            return false;
        }
        if (!empty($params['start']) && $date < $params['start']) {
            return false;
        }
        if (!empty($params['end']) && $date > $params['end']) {
            return false;
        }
        if (isset($params['min_amount']) && $params['min_amount'] !== '' && $amount < (float)$params['min_amount']) {
            return false;
        }
        if (isset($params['max_amount']) && $params['max_amount'] !== '' && $amount > (float)$params['max_amount']) {
            return false;
        }
        if (isset($params['min_hour']) && $params['min_hour'] !== '' && $hour < (int)$params['min_hour']) {
            return false;
        }
        if (isset($params['max_hour']) && $params['max_hour'] !== '' && $hour > (int)$params['max_hour']) {
            return false;
        }
        return true;
    }));
}
