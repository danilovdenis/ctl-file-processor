<?php

declare(strict_types=1);

namespace src\components;

use mysqli;
use Throwable;

class DBConnection {

	/** Connection */
	private static mysqli $connection;

	private static string $host;
	private static string $user;
	private static string $password;
	private static string $dbname;

	/**
	 * @param string $host     Host name or ip address
	 * @param string $user     Username
	 * @param string $password Password
	 * @param string $dbname   Database name
	 */
	public function __construct(string $host, string $user, string $password, string $dbname) {
		static::$host     = $host;
		static::$user     = $user;
		static::$password = $password;
		static::$dbname   = $dbname;

		try {
			static::$connection = new mysqli(static::$host, static::$user, static::$password, static::$dbname);
		}
		catch (Throwable $e) {
			echo PHP_EOL;
			echo 'Connection Error: ' . $e;
		}
	}

	/**
	 * Close connection
	 */
	public static function close() {
		static::$connection->close();
	}

	/**
	 * Return DB connection
	 *
	 * @return mysqli
	 */
	public static function getInstance(): mysqli {
		if (null === static::$connection) {
			new DBConnection(static::$host, static::$user, static::$password, static::$dbname);
		}

		return static::$connection;
	}
}
