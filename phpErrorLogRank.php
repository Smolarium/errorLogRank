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
$deprecateds = $redis->zRevRange($iniConf['REDIS_KEY_PARSED_DEPRECATED'], 0, $iniConf['SHOW_THIS_MANY_DEPRECATEDS'], true);
?>
<table border="1"></table>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script>
    function unsetLog(type, key) {
        if (confirm('Are you sure?')) {
            $.ajax({
                dataType: "json",
                type: "post",
                url: 'deleteRepairedFromPhpErrorLog.php',
                data: {
                    type: type,
                    key: key
                }
            });
        }
    }
    
    var fatals = <?= json_encode($fatals); ?>;
    var warnings = <?= json_encode($warnings); ?>;
    var notices = <?= json_encode($notices); ?>;
    var deprecateds = <?= json_encode($deprecateds); ?>;
    if (Object.keys(fatals).length > 0) {
        $("table").append('<tr style="background-color: red; color: white;"><th colspan="3">Errors</th></tr>');
        $.each(fatals, function(index, value) {
            var row = $('<tr style="background-color: red; color: white;"><td>' + index + '</td><td>' + value + '</td><td><input type="button" value="Unset" /></td></tr>');
            row.find("input").click(function(){
                unsetLog(<?= json_encode($iniConf['REDIS_KEY_PARSED_FATAL']); ?>, index);
            });
            $("table").append(row);
        });
    }
    
    if (Object.keys(warnings).length > 0) {
        $("table").append('<tr style="background-color: orange; color: black;"><th colspan="3">Warnings</th></tr>');
        $.each(warnings, function(index, value) {
            var row = $('<tr style="background-color: orange; color: black;"><td>' + index + '</td><td>' + value + '</td><td><input type="button" value="Unset" /></td></tr>');
            row.find("input").click(function(){
                unsetLog(<?= json_encode($iniConf['REDIS_KEY_PARSED_WARNING']); ?>, index);
            });
            $("table").append(row);
        });
    }
    
    if (Object.keys(notices).length > 0) {
        $("table").append('<tr><th colspan="3">Notices</th></tr>');
        $.each(notices, function(index, value) {
            var row = $('<tr><td>' + index + '</td><td>' + value + '</td><td><input type="button" value="Unset" /></td></tr>');
            row.find("input").click(function(){
                unsetLog(<?= json_encode($iniConf['REDIS_KEY_PARSED_NOTICE']); ?>, index);
            });
            $("table").append(row);
        });
    }
    
    if (Object.keys(deprecateds).length > 0) {
        $("table").append('<tr style="color: blue;"><th colspan="3">Deprecateds</th></tr>');
        $.each(deprecateds, function(index, value) {
            var row = $('<tr style="color: blue;"><td>' + index + '</td><td>' + value + '</td><td><input type="button" value="Unset" /></td></tr>');
            row.find("input").click(function(){
                unsetLog(<?= json_encode($iniConf['REDIS_KEY_PARSED_NOTICE']); ?>, index);
            });
            $("table").append(row);
        });
    }
    
   setTimeout("location.reload(true);", 5000);
</script>