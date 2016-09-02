<?php
$iniConf = parse_ini_file("errorLoger.ini");
$file_handle = fopen("php://stdin", "r");
$redis = new \Redis();
$redis->connect($iniConf['REDIS_HOST'], $iniConf['REDIS_PORT']);
if (isset($iniConf['REDIS_PASS'])) {
    $redis->auth($iniConf['REDIS_PASS']);
}

if (isset($iniConf['REDIS_DATABASE_INDEX'])) {
    $redis->select($iniConf['REDIS_DATABASE_INDEX']);
}

while (!feof($file_handle)) {
    $line = fgets($file_handle);
    if (preg_match("/\[[^]]*\] PHP Fatal error: (.*)/", $line) === 1) {
        $redis->zIncrBy($iniConf['REDIS_KEY_PARSED_FATAL'], 1, preg_replace("/\[[^]]*\] /", "", $line));
    }
	
    if (preg_match("/\[[^]]*\] PHP Catchable fatal error: (.*)/", $line) === 1) {
        $redis->zIncrBy($iniConf['REDIS_KEY_PARSED_FATAL'], 1, preg_replace("/\[[^]]*\] /", "", $line));
    }
    
    if (preg_match("/\[[^]]*\] PHP Warning: (.*)/", $line) === 1) {
        $redis->zIncrBy($iniConf['REDIS_KEY_PARSED_WARNING'], 1, preg_replace("/\[[^]]*\] /", "", $line));
    }
    
    if (preg_match("/\[[^]]*\] PHP Notice: (.*)/", $line) === 1) {
        $redis->zIncrBy($iniConf['REDIS_KEY_PARSED_NOTICE'], 1, preg_replace("/\[[^]]*\] /", "", $line));
    }
    
    if (preg_match("/\[[^]]*\] PHP Deprecated: (.*)/", $line) === 1) {
        $redis->zIncrBy($iniConf['REDIS_KEY_PARSED_DEPRECATED'], 1, preg_replace("/\[[^]]*\] /", "", $line));
    }
    
    echo $line;
}

fclose($file_handle);
