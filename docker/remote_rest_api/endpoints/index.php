<?php
declare(strict_types=1);

$throttling = false;

error_reporting(0);

$path = trim($_REQUEST['__path'] ?? '', '/');

if('' === $path){
	$path = '_index';
}

if($throttling){
	switch($_SERVER['REQUEST_METHOD']){
		case 'GET':
			usleep(100000);
			break;
		case 'POST':
			usleep(400000);
			break;
		default:
			usleep(50000);
			break;
	}
}

if($path !== 'authenticate'){
	if(strpos($path, '/') !== false){
		$path = str_replace('/', '__', $path);
	}
	$filename = sprintf('response/%s.json', $path);

	$responseBody = file_get_contents($filename);

	if(false === $responseBody){
		http_response_code(404);
		exit;
	}
}else{
	try{
		/*$username = $_POST['username'] ?? '';
		$password = $_POST['password'] ?? '';

		switch($username){
			case '000002':
			case '000003':
				if('foo' !== $password){
					throw new InvalidArgumentException('', 403);
				}
				$roles = ['ROLE_' . strtoupper($username)];
				break;
			default:
				throw new RuntimeException('', 404);
		}*/

		$username = '000002';

		$responseBody = json_encode([
			'data'   => [
				'username' => $username,
				'token'    => $username,
			],
		]);
	}catch(Throwable $exception){
		http_response_code($exception->getCode());
		exit;
	}
}

header('Content-Type: application/json');
echo $responseBody;
