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
	 *
	 * @throws Exception
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

			throw new Exception($e->getMessage());
		}

		$this->connection::getInstance()->commit();

		echo 'Inserted ' . count($data) . ' rows.' . PHP_EOL;
	}

	/**
	 * Insert user data into table 'users'.
	 *
	 * @param UsersDto $user
	 *
	 * @return int
	 * @throws Exception
	 */
	public function insertUser(UsersDto $user): int {
		$sql = 'INSERT INTO test(username, surname, email) VALUES ("' . $user->name . '", "' . $user->surname . '", "' . $user->email . '");';
		$this->connection::getInstance()->query($sql);

		if ('' !== $this->connection::getInstance()->error) {
			echo 'INSERT ERROR: ' . $this->connection::getInstance()->error;

			throw new Exception();
		}

		return $this->connection::getInstance()->insert_id;
	}

	/**
	 * Create table in database.
	 *
	 * @param string $tableName
	 * @param array  $columns
	 *
	 * @throws Exception
	 */
	public function createTable(string $tableName, array $columns) {
		$columnsString = implode(',', $columns);

		$this->connection::getInstance()->query('
				CREATE TABLE ' . $tableName . ' (
					' . $columnsString . '
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			');

		if (0 !== $this->connection::getInstance()->errno) {
			echo 'CREATE ERROR: ' . $this->connection::getInstance()->error;

			throw new Exception();
		}

		echo 'Table: ' . $tableName . ' created.' . PHP_EOL;
	}

	/**
	 * Add unique key
	 *
	 * @param string $tableName
	 * @param string $keyName
	 *
	 * @throws Exception
	 */
	public function addKeyUnique(string $tableName, string $keyName) {
		$this->connection::getInstance()->query('
				ALTER TABLE ' . $tableName . ' ADD UNIQUE unique_' . $keyName . ' (' . $keyName . ');');

		if (0 !== $this->connection::getInstance()->errno) {
			echo 'ADD KEY ERROR: ' . $this->connection::getInstance()->error;

			throw new Exception();
		}

		echo 'Unique key: ' . $keyName . ' created on table ' . $tableName . PHP_EOL;
	}

	/**
	 * Drop the table.
	 *
	 * @param string $tableName Name of table
	 *
	 * @throws Exception
	 */
	public function dropTable(string $tableName) {
		$this->connection::getInstance()->query('DROP TABLE ' . $tableName);

		if (0 !== $this->connection::getInstance()->errno) {
			echo 'DROP ERROR: ' . $this->connection::getInstance()->error;

			throw new Exception();
		}

		echo 'Table: ' . $tableName . ' dropped.' . PHP_EOL;
	}

	/**
	 * Drop the table.
	 *
	 * @param string $tableName Name of table
	 *
	 * @throws Exception
	 */
	public function truncateTable(string $tableName) {
		$this->connection::getInstance()->query('TRUNCATE TABLE ' . $tableName);

		if (0 !== $this->connection::getInstance()->errno) {
			echo 'TRUNCATE ERROR: ' . $this->connection::getInstance()->error;

			throw new Exception();
		}

		echo 'Table: ' . $tableName . ' truncated.' . PHP_EOL;
	}
}
