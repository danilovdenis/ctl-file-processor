<?php

/**
 * Autoload classes
 *
 * @param $class
 */
function myAutoloader($class) {
	include_once './src/services/FileService.php';
	include_once './src/dto/FileRowsDto.php';
	include_once './src/ApplicationRunner.php';
}

spl_autoload_register('myAutoloader');
