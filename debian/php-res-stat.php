#!/usr/bin/php -q
<?php
define('DEFAULT_URL', 'http://localhost:8811');

echo 'PHP Resource Stat', PHP_EOL;
echo 'Starting @ ', date('Y-m-d H:i:s'), PHP_EOL;

echo "\tFetch CPU Usage ... ";	// {{{
$top = shell_exec("top -bn1 | sed -n '/Cpu/p'");
if (preg_match('/^%Cpu\S+\s+(\d+\.\d+)\sus\S+\s+(\d+\.\d+)\ssy/', $top, $m)) {
	if (isset($m[1]) && isset($m[2])) {
		$cpu = $m[1] + $m[2];
		echo sprintf("%0.2f",$cpu), '%', PHP_EOL;
	}
	else echo 'FAIL !', PH_EOL;
}
else echo 'FAIL !', PHP_EOL;
// }}}
echo "\tFetch RAM Usage ... ";	// {{{
$ram = rtrim(shell_exec("free | awk 'FNR == 3 {print $3/($3+$4)*100}'"));
echo sprintf("%0.2f",$ram), '%', PHP_EOL;
// }}}
echo "\tFetch HDD Usage ... ";	// {{{
$df = explode("\n",shell_exec('df -ht ext4'));
if (! empty($df[1])) {
	if (preg_match('/^\/dev\/sda\d\s+\S+\s+\S+\s+\S+\s+(\d+)\%/', $df[1],$m)) {
		$hdd = $m[1];
		echo sprintf("%0.2f",$hdd), '%', PHP_EOL;
	}
	else echo 'FAIL !', PHP_EOL;
}
else echo 'FAIL !', PHP_EOL;
// }}}

if (! isset($argv[1])) die('NO Server-Key, then Terminate'.PHP_EOL);
$server_key = $argv[1];
echo '* Found Server-Key = ', $server_key, PHP_EOL;

$get_url = DEFAULT_URL;
if (! empty($argv[2])) $get_url = $argv[2];
echo '* Found Get-URL = ', $get_url, PHP_EOL;

$r1 = rand(1,5);	sleep($r1);
echo "\tSubmit CPU Usage ... ";
$c = file_get_contents($get_url.'/?'.$server_key.'-CPU='.$cpu);
echo $c, PHP_EOL;

$r2 = rand(1,5);	sleep($r2);
echo "\tSubmit RAM Usage ... ";
$r = file_get_contents($get_url.'/?'.$server_key.'-RAM='.$ram);
echo $r, PHP_EOL;

$r3 = rand(1,5);	sleep($r3);
echo "\tSubmit HDD Usage ... ";
$h = file_get_contents($get_url.'/?'.$server_key.'-HDD='.$hdd);
echo $h, PHP_EOL;

echo 'Finish @ ', date('Y-m-d H:i:s'), PHP_EOL;
?>
