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

$id = $_POST['id'];
$type = null;
switch (substr($id, 0, 1)) {
    case 'f': $type = $iniConf['REDIS_KEY_PARSED_FATAL'];
        break;
    case 'w': $type = $iniConf['REDIS_KEY_PARSED_WARNING'];
        break;
    case 'n': $type = $iniConf['REDIS_KEY_PARSED_NOTICE'];
        break;
    case 'd': $type = $iniConf['REDIS_KEY_PARSED_DEPRECATED'];
        break;
}

$hash = substr($id, 2);
foreach ($redis->zRevRange($type, 0, -1, false) as $row) {
    if ($hash === hash('sha256', $row)) {
        $redis->zRem($type, $row);
        break;
    }
}
