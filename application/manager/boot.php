<?php

$request_path = $_SERVER['PATH_INFO'];
$request_query = $_SERVER['QUERY_STRING'];

$manager_directory = dirname(__FILE__);
$module_directory = dirname($manager_directory);

require 'core/module.php';
require 'core/path.php';
require 'core/manager.php';

Manager::boot($manager_directory, $module_directory);

?>
