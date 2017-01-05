#!/usr/bin/php -q
<?php
/*
Script Delete Old Log Files of Postgresql PG_LOG folder
in current working directory

Wed Jan 20 09:00:39 ICT 2016	- first create	//pitak
Thu Jan  5 10:17:06 ICT 2017	- add feature move file to sub-folder	//pitak

*/
echo 'Clear old Logs below Today', PHP_EOL;
echo 'Starting @', date('Y-m-d H:i:s'), PHP_EOL;
$files = scandir(__DIR__);
if (! is_array($files)) die('ScanDir ['.__DIR__.'] error !!!'.PHP_EOL.PHP_EOL);
$logs = [];
foreach ($files as $file)
	if (strpos($file, '.log') && strpos($file, 'sql'))
		$logs[] = $file;
echo 'Found log-file = ', count($logs), ' file(s)', PHP_EOL;
sort($logs);
if (count($logs)<2) die('No older logs, left the last one file'.PHP_EOL);
unset($logs[count($logs)-1]);
$count = 0;
foreach ($logs as $log)
	if (preg_match('/\d{4}\-\d{2}\-\d{2}/', $log, $m)) {
		$folder = __DIR__.'/'.substr(str_replace('-','',$m[0]),2);
		if (! file_exists($folder)) 
			if (false===mkdir($folder)) die('Cannot create sub-folder ['.$folder.']');
		if (false===rename(__DIR__.'/'.$log, $folder.'/'.$log))
			die('Cannot move file ['.$log.'] to sub-folder ['.$folder.']');
		echo 'Move old log file ... ', $log, ' => ', $folder, PHP_EOL;
		$count++;
	}
	else {
		if (unlink(__DIR__.'/'.$log)) {
			echo 'Delete old log file ... ', $log, PHP_EOL;
			$count++;
		}
	}
echo 'Finish clear ', $count, ' file(s)', PHP_EOL, PHP_EOL;
?>
