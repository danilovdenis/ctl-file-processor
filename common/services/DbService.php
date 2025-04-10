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
	public function connect(DBConnection $connection): void {
		$this->connection = $connection;
	}

	/**
	 * Batch insert users into db.
	 *
	 * @param string     $tableName
	 * @param UsersDto[] $data
	 *
	 * @throws Throwable
	 */
	public function batchInsertUsers(string $tableName, array $data): void {
		$this->connection::getInstance()->begin_transaction();

		try {
			foreach ($data as $row) {
				$this->insertUser($tableName, $row);
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
	 * @param string   $tableName
	 * @param UsersDto $user
	 *
	 * @return int
	 * @throws Throwable
	 */
	public function insertUser(string $tableName, UsersDto $user): int {
		$sql = 'INSERT INTO ' . $tableName . '(username, surname, email) VALUES ("' . $user->name . '", "' . $user->surname . '", "' . $user->email . '");';
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
	public function createTable(string $tableName, array $columns): void {
		$columnsString = implode(',', $columns);

		$this->connection::getInstance()->query('DROP TABLE IF EXISTS ' . $tableName);

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
	public function addKeyUnique(string $tableName, string $keyName): void {
		$this->connection::getInstance()->query('
				ALTER TABLE ' . $tableName . ' ADD UNIQUE unique_' . $keyName . ' (' . $keyName . ');');

		if (0 !== $this->connection::getInstance()->errno) {
			throw new Exception($this->connection::getInstance()->error);
		}

		Message::output('Unique key ' . $keyName . ' created on table ' . $tableName, Message::CODE_INFO);
	}
}
