<?php

declare(strict_types=1);

namespace common\components;

use Exception;
use mysqli;
use Throwable;

/**
 * Db Connection class.
 */
class DBConnection {
	/** @var string $host Hostname or IP address */
	private static string $host;
	/** @var string $user Username */
	private static string $user;
	/** @var string $password Password */
	private static string $password;
	/** @var string $dbname Database name */
	private static string $dbname;

	/** Connection */
	private static mysqli $connection;

	/**
	 * @param string $host     Host name or ip address
	 * @param string $user     Username
	 * @param string $password Password
	 * @param string $dbname   Database name
	 *
	 * @throws Throwable
	 */
	public function __construct(string $host, string $user, string $password, string $dbname) {
		static::$host     = $host;
		static::$user     = $user;
		static::$password = $password;
		static::$dbname   = $dbname;

		static::$connection = new mysqli(static::$host, static::$user, static::$password, static::$dbname);

		if (0 !== static::$connection->connect_errno) {
			throw new Exception(static::$connection->connect_error);
		}
	}

	/**
	 * Close connection.
	 */
	public static function close() {
		static::$connection->close();
	}

	/**
	 * Return DB connection.
	 *
	 * @return mysqli
	 * @throws Throwable
	 */
	public static function getInstance(): mysqli {
		if (null === static::$connection) {
			new DBConnection(static::$host, static::$user, static::$password, static::$dbname);
		}

		return static::$connection;
	}
}
