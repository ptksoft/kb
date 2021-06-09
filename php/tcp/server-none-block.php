#!/usr/bin/php -q
<?php
ini_set('error_reporting', E_ALL);
ini_set('display_error', 1);
if (!extension_loaded('sockets')) die('Sockets Extension is not loaded'.PHP_EOL);
set_time_limit(0);
ob_implicit_flush();

echo 'Hello World Test Server Multi-connection-non-blocking', PHP_EOL;

$ip = '127.0.0.1';
$port = 7769;
echo 'Set ip/port success', PHP_EOL;

echo 'before create socket', PHP_EOL;
$sock = socket_create(AF_INET, SOCK_STREAM, 0);
if (FALSE === $sock) die('Failin socket_create'.PHP_EOL);
echo 'socket_create success', PHP_EOL;
@socket_bind($sock, $ip, $port) OR die('! Fail in bind to address'.PHP_EOL);
@socket_listen($sock) OR die('! Fail in listen'.PHP_EOL);
@socket_set_nonblock($sock) OR die('! Fail in set NonBlock'.PHP_EOL);
$clients = array();
$usec = 0;
$uping = 100000;
echo 'Begin Main Loop', PHP_EOL;
while (TRUE) {
	if (
		($request = @socket_accept($sock)) &&
		(is_resource($request))
	) {
		@socket_write($request, "Server main version 1.0.0\r\n");
		@socket_set_nonblock($request);
		echo 'New Client connected', PHP_EOL;
		$clients[] = $request;
	}	
	if (count($clients)) {
		foreach ($clients as $k=>$v) {
			$line = '';
			if ($line = @socket_read($v, 1024)) {
				echo "$k: $line";
			}
			else {
				if (
				$usec >= $uping &&
				(FALSE===@socket_write($v,"PING\r\n"))
				) {
					@socket_close($clients[$k]);
					unset($clients[$k]);
					echo 'Client Disconnected, total = ', count($clients), PHP_EOL;
				}
			}
		}
	}
	usleep(1);
	if ($usec >= $uping) $usec = 0;
	else $usec++;
}
echo 'Terminate', PHP_EOL;
@socket_close($sock);
?>
