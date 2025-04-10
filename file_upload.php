<?php
require __DIR__ . '/autoloader.php';

try {
	(new \common\service\ApplicationRunner())->run();
}
catch (Throwable $e) {
	throw new Exception();
}
