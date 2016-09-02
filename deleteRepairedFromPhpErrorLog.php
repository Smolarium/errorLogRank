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

if (
    $_POST['type'] == $iniConf['REDIS_KEY_PARSED_FATAL']
    || $_POST['type'] == $iniConf['REDIS_KEY_PARSED_WARNING']
    || $_POST['type'] == $iniConf['REDIS_KEY_PARSED_NOTICE']
    || $_POST['type'] == $iniConf['REDIS_KEY_PARSED_DEPRECATED']
) {
    $redis->zRem($_POST['type'], $_POST['key']);
}
