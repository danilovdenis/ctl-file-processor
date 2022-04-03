<?php

/**
 * Scanning directory and include file
 *
 * @param $root
 */
function scandirRec($root) {
	$output = [];
	$dirs = scandir($root);
	foreach ($dirs as $dir) {
		if ($dir == '.' || $dir == '..') {
			continue;
		}

		$path = $root . '/' . $dir;
		if (is_file($path)) {
			include_once __DIR__ . '/' . $path;

		}
		else if (is_dir($path)) {
			scandirRec($path);
		}
	}
}

/**
 * Autoload classes.
 *
 * @param $class
 */
function myAutoloader($class) {
	scandirRec('common');
}

spl_autoload_register('myAutoloader');
