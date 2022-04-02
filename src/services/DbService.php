<?php

declare(strict_types=1);

namespace src\service;

use dto\service\UsersDto;
use Exception;
use src\components\DBConnection;
use Throwable;

/**
 * Service to work with database.
 */
class DbService {

	/** Connection */
	public DBConnection $connection;

	/**
	 * @param DBConnection $connection
	 */
	public function connect(DBConnection $connection) {
		$this->connection = $connection;
	}

	/**
	 * Batch insert users into db.
	 *
	 * @param UsersDto[] $data
	 */
	public function batchInsertUsers(array $data) {
		$this->connection::getInstance()->begin_transaction();

		try {
			foreach ($data as $row) {
				$this->insertUser($row);
			}
		}
		catch (Throwable $e) {
			$this->connection::getInstance()->rollback();

			echo $e->getMessage() . PHP_EOL;
		}

		$this->connection::getInstance()->commit();
	}

	/**
	 * Insert user data into table 'users'.
	 *
	 * @param UsersDto $user
	 *
	 * @return int
	 */
	public function insertUser(UsersDto $user): int {
		$sql = 'INSERT INTO test(username, surname, email) VALUES ("' . $user->name . '", "' . $user->surname . '", "' . $user->email . '");';
		$this->connection::getInstance()->query($sql);

		if ('' !== $this->connection::getInstance()->error) {
			echo 'Insert Error: ' . $this->connection::getInstance()->error;

			throw new Exception();
		}

		return $this->connection::getInstance()->insert_id;
	}

	/**
	 * Create table in database.
	 *
	 * @param string $tableName
	 * @param array  $columns
	 */
	public function createTable(string $tableName, array $columns) {
		$columnsString = implode(',', $columns);

		try {
			//@todo pass key
			$this->connection::getInstance()->query('
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
	 * Drop the table.
	 *
	 * @param string $tableName Name of table
	 */
	public function dropTable(string $tableName) {
		$this->connection::getInstance()->query('DROP TABLE IF EXISTS ' . $tableName);
	}
}
