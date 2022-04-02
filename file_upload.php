<?php
require __DIR__ . '/autoloader.php';

(new \src\service\ApplicationRunner(
		new \src\service\FileService(),
		new \src\service\DbService()
	)
)->run();
