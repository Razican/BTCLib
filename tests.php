<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASEPATH', TRUE);

function log_message($type, $message){echo strtoupper($type).' - '.$message.PHP_EOL;}
function show_error($message, $code){echo $message.PHP_EOL; exit(1);}

require('Bitcoin.php');

$bitcoin = new Bitcoin();

$config = array(
			'bitcoin_ssl' => FALSE,
			'bitcoin_user' => 'test',
			'bitcoin_pass' => '12345',
			'bitcoin_server' => 'localhost',
			'bitcoin_port' => 18332
			);

$bitcoin->initialize($config);

echo PHP_EOL.PHP_EOL;

switch (count($argv)-2)
{
	case 0:
		var_dump($bitcoin->$argv[1]());
	break;
	case 1:
		var_dump($bitcoin->$argv[1]($argv[2]));
	break;
	case 2:
		var_dump($bitcoin->$argv[1]($argv[2], $argv[3]));
	break;
	case 3:
		var_dump($bitcoin->$argv[1]($argv[2], $argv[3], $argv[4]));
	break;
	case 4:
		var_dump($bitcoin->$argv[1]($argv[2], $argv[3], $argv[4], $argv[5]));
	break;
	case 5:
		var_dump($bitcoin->$argv[1]($argv[2], $argv[3], $argv[4], $argv[5], $argv[6]));
	break;
	case 6:
		var_dump($bitcoin->$argv[1]($argv[2], $argv[3], $argv[4], $argv[5], $argv[6], $argv[7]));
	break;
	default:
		// TODO run default tests
}

echo PHP_EOL.PHP_EOL;