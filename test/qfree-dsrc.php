<?php
// Log file setting {{{
define('LOG_FILE','/tmp/qhack.log');
function debug($data) { 
	error_log(
		date('[his:')
		.str_pad(gettimeofday()['usec'],6,'0')
		.'] '
		.$data
		.PHP_EOL,3,LOG_FILE); 
}
// }}}
// Verify request method {{{
if ($_SERVER['REQUEST_METHOD']!='POST') {
	echo '<h1>Must Call with POST METHOD</h1>';
	debug('NOT POST Method');
	die();
}
// }}}
// Verify content type {{{
if ($_SERVER['CONTENT_TYPE']!='text/xml') {
	echo '<h1>Invalid Content-Type</h1>';
	debug('NOT Content-Type text/xml');
	die();
}
// }}}

function __blank() {/*{{{*/
	global $sid;
echo <<<XML
<?xml version="1.0"?>
<stream id="{$sid}">
</stream>
XML;
	die();
}/*}}}*/
function __balance() {/*{{{*/
	global $sid, $lid;
echo <<<XML
<?xml version="1.0"?>
<stream id="{$sid}">
<dsrc_frame lid="{$lid}" type="3">
<tapdu type="SetRequest" mode="1" eid="1" access_credentials="F7 2D 06 FA">
<attribute id="108">00 00 00 A2 1C</attribute>
<attribute id="89">35 59 1A 4F 09 7A 3A 00 33 03 01 00</attribute>
<attribute id="107">02</attribute>
</tapdu>
</dsrc_frame>
</stream>
XML;
	die();
}/*}}}*/
function __mmi() {/*{{{*/
	global $sid, $lid;
echo <<<XML
<?xml version="1.0"?>
<stream id="{$sid}">
<dsrc_frame lid="{$lid}" type="3">
<tapdu type="ActionRequest" mode="1" eid="0" action_type="SetMMI">
<param type="DsrcInteger">0</param>
</tapdu>
</dsrc_frame>
</stream>
XML;
	die();
}/*}}}*/
function __release() {/*{{{*/
	global $sid, $lid;
echo <<<XML
<?xml version="1.0"?>
<stream id="{$sid}">
<sap lid="{$lid}" request="release"/>
</stream>
XML;
	die();
}/*}}}*/

debug('Process Input Start:');
$xml = file_get_contents('php://input');
$obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
$json = json_encode($obj);
$a = json_decode($json,TRUE);
header('Content-Type: text/xml');

// Message monitoring idle {{{
$sid = $a['@attributes']['id'];
debug('... stream-id '.$sid);
if (! isset($a['dsrc_frame'])) {
	debug('... monitoring idle message');
	__blank();
}
// }}}

$lid = $a['dsrc_frame']['@attributes']['lid'];
$atype = $a['dsrc_frame']['tapdu']['@attributes']['type'];
debug('... lid: '.$lid.' tapdu-type: '.$atype);

switch($atype) {
	case 'VST':	__blank();	break;				// #1
	case 'GetResponse': __blank();	break;		// #2
	/*
		199	OBE
		783 Lookup Status List
		858 Check Black list
		034 Status Balance, Cur OBU Balance, New OBU Balance
	*/
	case 'SetResponse': __balance();	break;		// #3
	//case 'SetResponse': __mmi();	break;			// #4
	case 'ActionResponse': __release();	break;		// #5
}

echo '<hr>';
echo '<pre>';
debug(print_r($a,TRUE));
echo '</pre>';
echo '</hr>';
?>
