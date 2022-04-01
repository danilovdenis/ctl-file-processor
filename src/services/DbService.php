<?php

declare(strict_types=1);

namespace src\service;

use dto\service\FileRowsDto;
use mysqli;
use Throwable;

/**
 * Service to work with database
 */
class DbService {

	/** Connection */
	public mysqli $connection;

	/**
	 * @param string $host     Host name or ip address
	 * @param string $user     Username
	 * @param string $password Password
	 * @param string $dbname   Database name
	 */
	public function __construct(string $host, string $user, string $password, string $dbname) {
		try {
			$this->connection = new mysqli($host, $user, $password, $dbname);
		}
		catch (Throwable $e) {
			echo PHP_EOL;
			echo 'Connection Error: ' . $e;
		}
	}

	/**
	 * @param $data
	 *
	 * @todo
	 * Batch insert
	 *
	 */
	public function batchInsert(FileRowsDto $data) {
		$this->connection->begin_transaction();

		try {
			$this->insert();
		}
		catch (Throwable $e) {
			$this->connection->rollback();

			echo $e . PHP_EOL;
		}

		$this->connection->commit();
	}

	/**
	 * @todo
	 */
	public function insert() {
		// insert operation
	}

	/**
	 * Create table in database
	 *
	 * @param string $tableName
	 * @param array  $columns
	 */
	public function createTable(string $tableName, array $columns) {
		$columnsString = implode(',', $columns);

		try {
			$this->connection->query('
				CREATE TABLE IF NOT EXISTS ' . $tableName . ' (
					' . $columnsString . ',
					UNIQUE KEY unique_email (email)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			');
		}
		catch (Throwable $e) {
			echo $e . PHP_EOL;
		}
	}

	/**
	 * Drop the table
	 *
	 * @param string $tableName
	 */
	public function dropTable(string $tableName) {
		$this->connection->query('DROP TABLE IF EXISTS ' . $tableName);
	}
}
