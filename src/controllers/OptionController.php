<?php

declare(strict_types=1);

namespace src\controller;

use dto\service\ConfigDto;
use Exception;
use src\components\DBConnection;
use src\service\DbService;
use src\service\FileService;

/**
 * Options controller.
 */
class OptionController {

	/** File service  */
	protected FileService $fileService;

	/** DB service  */
	protected DbService $dbService;

	/** Config  */
	protected ConfigDto $config;

	/**
	 * @param FileService $fileService
	 * @param DbService   $dbService
	 */
	public function __construct(FileService $fileService, DbService $dbService) {
		$this->fileService = $fileService;
		$this->dbService   = $dbService;
	}

	/**
	 * @param ConfigDto $configDto
	 */
	public function setConfig(ConfigDto $configDto) {
		$this->config = $configDto;
	}

	/**
	 * Processing file action.
	 *
	 * @param string|false|null $fileName
	 *
	 * @throws Exception
	 */
	public function actionFile($fileName) {
		if (!$fileName) {
			$this->showNotFoundError('File Name');

			throw new Exception();
		}

		if (!$this->config->host || !$this->config->user || !$this->config->password || !$this->config->host) {
			$this->showMissingConnectionError();

			throw new Exception();
		}

		$data = $this->fileService->prepareData($fileName);

		if (0 === count($data)) {
			echo "No users to upload" . PHP_EOL;

			throw new Exception();
		}

		$this->dbService->connect(new DBConnection(
			$this->config->host,
			$this->config->user,
			$this->config->password,
			$this->config->dbName
		));

		$this->dbService->batchInsertUsers($data);

		echo "File data uploaded" . PHP_EOL;
	}

	/**
	 * Drop table action.
	 *
	 * @param string|false|null $tableName
	 *
	 * @throws Exception
	 */
	public function actionDropTable($tableName) {
		if (!$tableName) {
			$this->showNotFoundError('Table Name');

			throw new Exception();
		}

		if (!$this->config->host || !$this->config->user || !$this->config->password || !$this->config->host) {
			$this->showMissingConnectionError();

			throw new Exception();
		}

		$this->dbService->connect(new DBConnection(
			$this->config->host,
			$this->config->user,
			$this->config->password,
			$this->config->dbName
		));

		$this->dbService->dropTable($tableName);
	}

	/**
	 * Create table action.
	 *
	 * @param string|false|null $tableName
	 *
	 * @throws Exception
	 */
	public function actionCreateTable(?string $tableName) {
		if (!$tableName) {
			$this->showNotFoundError('Table Name');

			throw new Exception();
		}

		if (!$this->config->host || !$this->config->user || !$this->config->password || !$this->config->host) {
			$this->showMissingConnectionError();

			throw new Exception();
		}

		$this->dbService->connect(new DBConnection(
			$this->config->host,
			$this->config->user,
			$this->config->password,
			$this->config->dbName
		));

		$this->dbService->createTable($tableName,
			[
				'username VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Username"',
				'surname  VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Surname"',
				'email    VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Email"',
			]
		);
		$this->dbService->addKeyUnique($tableName, 'email');
	}

	/**
	 * Truncate table action.
	 *
	  @param string|false|null $tableName
	 *
	 * @throws Exception
	 */
	public function actionTruncateTable(?string $tableName) {
		if (!$tableName) {
			$this->showNotFoundError('Table Name');

			throw new Exception();
		}

		if (!$this->config->host || !$this->config->user || !$this->config->password || !$this->config->host) {
			$this->showMissingConnectionError();

			throw new Exception();
		}

		$this->dbService->connect(new DBConnection($this->config->host, $this->config->user, $this->config->password, 'db_users'));

		$this->dbService->truncateTable($tableName);
	}

	/**
	 * Parse file without insert into database.
	 *
	 * @param string|false|null $filename
	 *
	 * @throws Exception
	 */
	public function actionDryRun($filename) {
		if (!$filename) {
			$this->showNotFoundError('File Name');

			throw new Exception();
		}

		$data = $this->fileService->prepareData($filename);

		echo PHP_EOL;
		echo 'DATA PREPARED:' . PHP_EOL;
		echo '---------------' . PHP_EOL;
		foreach ($data as $key => $row) {
			echo $key . '. ' . 'USERNAME: ' . $row->name . ' | SURNAME: ' . $row->surname . ' | EMAIL: ' . $row->email . PHP_EOL;
		}
		echo '---------------' . PHP_EOL;
	}

	/**
	 * Help information.
	 * Output information block into STDOUT.
	 */
	public function actionHelp() {
		echo PHP_EOL;
		echo 'USAGE' . PHP_EOL;
		echo PHP_EOL;
		echo '--file           [File name]        Parsing file and insert into table' . PHP_EOL;
		echo '--create_table   [Table name]       Create table in database' . PHP_EOL;
		echo '--drop_table     [Table name]       Drop table in database' . PHP_EOL;
		echo '--truncate_table [Table name]       Truncate table in database' . PHP_EOL;
		echo '--dry_run                           Parsing file and prepare to processing without insert' . PHP_EOL;
		echo '-u               [User name]        User name' . PHP_EOL;
		echo '-p               [Password]         User password' . PHP_EOL;
		echo '-h               [Host name or IP]  Database host' . PHP_EOL;
		echo '-d               [Database name]    Database name' . PHP_EOL;
		echo PHP_EOL;
	}

	/**
	 * Output error message.
	 *
	 * @param string $parameter Name of parameter
	 */
	private function showNotFoundError(string $parameter) {
		echo PHP_EOL;
		echo 'Value Not Found' . PHP_EOL;
		echo 'PLease, Set ' . $parameter . PHP_EOL;
		echo 'Type --help to see help information' . PHP_EOL;
	}

	/**
	 * Output missing error message.
	 */
	private function showMissingConnectionError() {
		echo PHP_EOL;
		echo 'Missing required connection parameters' . PHP_EOL;
		echo 'PLease, Set -u -p -h'  . PHP_EOL;
		echo 'Type --help to see help information' . PHP_EOL;
	}
}
