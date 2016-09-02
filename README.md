# errorLogRank
Store php error log as sorted list in redis and displays it as rank

1. Redis server is required
2. Edit ini file to set redis configuration and redis keys, where logs will be stored
3. You need to add errorLogRank.sh file to run it at system start
4. Copy php files into your www directory 
 - deleteRepairedFromPhpErrorLog.php
 - phpErrorLogParser.php
 - phpErrorLogRank.php
5. Run sh file (it will start tailing into script)
6. Open http://localhost/phpErrorLogRank.php in your web browser

Now you should see nice rank. Have a nice debugging :)
