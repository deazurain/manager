<?php

echo '<pre>', print_r($_SERVER, true), '</pre>';

function request_path() {

	if(isset($_SERVER['PATH_INFO'])) {
		return $_SERVER['PATH_INFO'];
	}

	if(isset($_SERVER['REQUEST_URI'])) {
		$request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

		if($request_path) {
			return rawurldecode($request_path);
		}

	}

	return null;

}

echo "<h1>Request path: ".request_path()."</h1>";

$manager_directory = dirname(__FILE__);
$module_directory = dirname($manager_directory);

require 'core/module.php';
require 'core/path.php';
require 'core/manager.php';

Manager::boot($manager_directory, $module_directory);