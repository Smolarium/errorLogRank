<?php

$iniConf = parse_ini_file("errorLoger.ini");
$redis = new \Redis();
$redis->connect($iniConf['REDIS_HOST'], $iniConf['REDIS_PORT']);
if (isset($iniConf['REDIS_PASS'])) {
    $redis->auth($iniConf['REDIS_PASS']);
}

if (isset($iniConf['REDIS_DATABASE_INDEX'])) {
    $redis->select($iniConf['REDIS_DATABASE_INDEX']);
}

$fatals = $redis->zRevRange($iniConf['REDIS_KEY_PARSED_FATAL'], 0, $iniConf['SHOW_THIS_MANY_FATALS'], true);
$warnings = $redis->zRevRange($iniConf['REDIS_KEY_PARSED_WARNING'], 0, $iniConf['SHOW_THIS_MANY_WARNINGS'], true);
$notices = $redis->zRevRange($iniConf['REDIS_KEY_PARSED_NOTICE'], 0, $iniConf['SHOW_THIS_MANY_NOTICES'], true);
$deprecateds = $redis->zRevRange(
    $iniConf['REDIS_KEY_PARSED_DEPRECATED'],
    0,
    $iniConf['SHOW_THIS_MANY_DEPRECATEDS'],
    true
);

$res = ['fatals' => [], 'warnings' => [], 'notices' => [], 'deprecateds' => []];
foreach ($fatals as $text => $count) {
    $res['fatals'][] = [
        'id' => 'f-' . hash('sha256', $text),
        'content' => $text,
        'count' => $count
    ];
}

foreach ($warnings as $text => $count) {
    $res['warnings'][] = [
        'id' => 'w-' . hash('sha256', $text),
        'content' => $text,
        'count' => $count
    ];
}

foreach ($notices as $text => $count) {
    $res['notices'][] = [
        'id' => 'n-' . hash('sha256', $text),
        'content' => $text,
        'count' => $count
    ];
}

foreach ($deprecateds as $text => $count) {
    $res['deprecateds'][] = [
        'id' => 'd-' . hash('sha256', $text),
        'content' => $text,
        'count' => $count
    ];
}

echo json_encode($res);
