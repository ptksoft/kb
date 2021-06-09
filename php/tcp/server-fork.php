#!/usr/bin/php -q
<?php
ini_set('error_reporting', E_ALL);
ini_set('display_error', 1);
if (!extension_loaded('sockets')) die('Sockets Extension is not loaded'.PHP_EOL);
set_time_limit(0);
ob_implicit_flush();

echo 'Hello World Test Server Multi-connection-non-blocking', PHP_EOL;

$ip = '0.0.0.0';
$port = 7769;
echo 'Set ip/port success', PHP_EOL;

echo 'before create socket', PHP_EOL;
$server = socket_create(AF_INET, SOCK_STREAM, 0);
if (FALSE === $server) die('Failin socket_create'.PHP_EOL);
echo 'socket_create success', PHP_EOL;
@socket_bind($server, $ip, $port) OR die('! Fail in bind to address'.PHP_EOL);
@socket_listen($server) OR die('! Fail in listen'.PHP_EOL);
//@socket_set_nonblock($sock) OR die('! Fail in set NonBlock'.PHP_EOL);
echo 'Begin Main Loop', PHP_EOL;
$pid=0;
$child_num = 0;
while (TRUE) {
	// clear Zombie <defunc> process list
	while (pcntl_wait($status, WNOHANG)>0);

	if (
		($client = @socket_accept($server)) &&
		(is_resource($client))
	) {
		$child_num++;
		$pid = pcntl_fork();
		if ($pid == -1) {
			die('could not fork');
		}
		else if ($pid) {
			echo 'Success fork child client pid=',$pid, PHP_EOL;
			// release pointer to socket of parent
			$client = null;	
			// continue waiting new connection
			continue;
		}
		else {
			echo 'Child client #', $child_num, ' starting', PHP_EOL;
			break;
		}
	}	
}
@socket_write($client, "Server fork main version 1.0.0\r\n");
@socket_write($client, "Client Num = {$child_num}\r\n");
$myPID = getmypid();
@socket_write($client, "Client PID = {$myPID}\r\n");

while (true) {
	$line = @socket_read($client, 1024);
	if (rtrim($line)=='quit') break;
	echo 'child[', $child_num, '] => ', $line;
}
if ($pid) {
	echo 'Server Terminate', PHP_EOL;
	@socket_close($server);
}
else {
	echo 'Client Terminate', PHP_EOL;
	socket_close($client);
}
?>
