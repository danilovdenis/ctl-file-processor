<?php

/**
 * Autoload classes
 *
 * @param $class
 */
function myAutoloader($class) {
	include_once './src/services/FileService.php';
	include_once './src/services/DbService.php';
	include_once './src/components/DBConnection.php';
	include_once './src/dto/UsersDto.php';
	include_once './src/ApplicationRunner.php';
}

spl_autoload_register('myAutoloader');
