<?php

declare(strict_types=1);

namespace common\service;

use common\components\message\Message;
use dto\service\UsersDto;
use Exception;
use common\components\DBConnection;
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
	 * @throws Throwable
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

		Message::output('Inserted ' . count($data) . ' rows.', Message::CODE_INFO);
	}

	/**
	 * Insert user data into table 'users'.
	 *
	 * @param UsersDto $user
	 *
	 * @return int
	 * @throws Throwable
	 */
	public function insertUser(UsersDto $user): int {
		$sql = 'INSERT INTO test(username, surname, email) VALUES ("' . $user->name . '", "' . $user->surname . '", "' . $user->email . '");';
		$this->connection::getInstance()->query($sql);

		if ('' !== $this->connection::getInstance()->error) {
			throw new Exception($this->connection::getInstance()->error);
		}

		return $this->connection::getInstance()->insert_id;
	}

	/**
	 * Create table in database.
	 *
	 * @param string $tableName
	 * @param array  $columns
	 *
	 * @throws Throwable
	 */
	public function createTable(string $tableName, array $columns) {
		$columnsString = implode(',', $columns);

		$this->connection::getInstance()->query('
				CREATE TABLE ' . $tableName . ' (
					' . $columnsString . '
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			');

		if (0 !== $this->connection::getInstance()->errno) {
			throw new Exception($this->connection::getInstance()->error);
		}

		Message::output('Table ' . $tableName . ' created.', Message::CODE_INFO);
	}

	/**
	 * Add unique key
	 *
	 * @param string $tableName
	 * @param string $keyName
	 *
	 * @throws Throwable
	 */
	public function addKeyUnique(string $tableName, string $keyName) {
		$this->connection::getInstance()->query('
				ALTER TABLE ' . $tableName . ' ADD UNIQUE unique_' . $keyName . ' (' . $keyName . ');');

		if (0 !== $this->connection::getInstance()->errno) {
			throw new Exception($this->connection::getInstance()->error);
		}

		Message::output('Unique key ' . $keyName . ' created on table ' . $tableName, Message::CODE_INFO);
	}

	/**
	 * Drop the table.
	 *
	 * @param string $tableName Name of table
	 *
	 * @throws Throwable
	 */
	public function dropTable(string $tableName) {
		$this->connection::getInstance()->query('DROP TABLE ' . $tableName);

		if (0 !== $this->connection::getInstance()->errno) {
			throw new Exception($this->connection::getInstance()->error);
		}

		Message::output('Table ' . $tableName . ' dropped.', Message::CODE_INFO);
	}

	/**
	 * Drop the table.
	 *
	 * @param string $tableName Name of table
	 *
	 * @throws Throwable
	 */
	public function truncateTable(string $tableName) {
		$this->connection::getInstance()->query('TRUNCATE TABLE ' . $tableName);

		if (0 !== $this->connection::getInstance()->errno) {
			throw new Exception($this->connection::getInstance()->error);
		}

		Message::output('Table ' . $tableName . ' truncated.', Message::CODE_INFO);
	}

	/**
	 * Create database.
	 *
	 * @param string $dbName Name of database
	 *
	 * @throws Throwable
	 */
	public function createDatabase(string $dbName) {
		$this->connection::getInstance()->query('CREATE DATABASE ' . $dbName);

		if (0 !== $this->connection::getInstance()->errno) {
			throw new Exception($this->connection::getInstance()->error);
		}
	}

	/**
	 * Create database.
	 *
	 * @param string $dbName     Name of database
	 * @param string $userString Full user String (user@localhost)
	 *
	 * @throws Throwable
	 */
	public function addPrivileges(string $dbName, string $userString) {
		$this->connection::getInstance()->query('GRANT ALL PRIVILEGES ON ' . $dbName . '.* to ' . $userString . ';');
		$this->connection::getInstance()->query('FLUSH PRIVILEGES');

		if (0 !== $this->connection::getInstance()->errno) {
			throw new Exception($this->connection::getInstance()->error);
		}
	}
}
