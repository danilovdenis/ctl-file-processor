<?php

declare(strict_types=1);

namespace common\controller;

use common\components\message\Message;
use dto\service\ConfigDto;
use Exception;
use common\components\DBConnection;
use common\service\DbService;
use common\service\FileService;
use Throwable;

/**
 * Options controller.
 */
class OptionController {
	/** Config  */
	protected ConfigDto $config;

	/**
	 * @param FileService $fileService
	 * @param DbService   $dbService
	 */
	public function __construct(private readonly FileService $fileService, private readonly DbService $dbService) {
	}

	/**
	 * @param ConfigDto $configDto
	 */
	public function setConfig(ConfigDto $configDto): void {
		$this->config = $configDto;
	}

	/**
	 * Processing file action.
	 *
	 * @param false|string|null $fileName
	 *
	 * @throws Throwable
	 */
	public function actionFile(false|string|null $fileName): void {
		if (!$fileName) {
			throw new Exception($this->getNotFoundError('File Name'));
		}

		if (pathinfo($fileName)['extension'] !== 'csv') {
			throw new Exception('Unsupported file format. Only csv');
		}

		if (!$this->hasDbParameters()) {
			throw new Exception($this->getMissingConnectionError());
		}

		$data = $this->fileService->prepareData($fileName);

		if (0 === count($data)) {
			throw new Exception('No users to upload', 1);
		}

		$this->dbService->connect(new DBConnection(
			$this->config->host,
			$this->config->user,
			$this->config->password,
			$this->config->dbName
		));

		$this->dbService->batchInsertUsers($this->config->tableName, $data);

		Message::output('File data uploaded', Message::CODE_INFO);
	}

	/**
	 * Create table action.
	 *
	 * @throws Throwable
	 */
	public function actionCreateTable(): void {
		if (!$this->config->tableName) {
			throw new Exception($this->getNotFoundError('Table Name'));
		}

		if (!$this->hasDbParameters()) {
			throw new Exception($this->getMissingConnectionError());
		}

		$this->dbService->connect(new DBConnection(
			$this->config->host,
			$this->config->user,
			$this->config->password,
			$this->config->dbName
		));

		$this->dbService->createTable($this->config->tableName,
			[
				'username VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Username"',
				'surname  VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Surname"',
				'email    VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Email"',
			]
		);
		$this->dbService->addKeyUnique($this->config->tableName, 'email');
	}

	/**
	 * Parse file without insert into database.
	 *
	 * @param false|string|null $filename
	 *
	 * @throws Throwable
	 */
	public function actionDryRun(false|string|null $filename): void {
		if (!$filename) {
			throw new Exception($this->getNotFoundError('File Name'));
		}

		$data = $this->fileService->prepareData($filename);

		if (0 === count($data)) {
			throw new Exception('No users to upload', 1);
		}

		Message::output('DATA PREPARED:', Message::CODE_INFO);
		Message::output('---------------', Message::CODE_INFO);
		foreach ($data as $key => $row) {
			Message::output($key . '. ' . 'USERNAME: ' . $row->name . ' | SURNAME: ' . $row->surname . ' | EMAIL: ' . $row->email, Message::CODE_INFO);
		}
		Message::output('---------------', Message::CODE_INFO);
	}

	/**
	 * Help information.
	 * Output information block into STDOUT.
	 */
	public function actionHelp(): void {
		echo PHP_EOL;
		echo 'USAGE:' . PHP_EOL;
		echo PHP_EOL;
		echo '--file              [File name]     Parsing file and insert into table' . PHP_EOL;
		echo '--create_table      [Table name]    Create/recreate table in database' . PHP_EOL;
		echo '--drop_table        [Table name]    Drop table in database' . PHP_EOL;
		echo '--truncate_table    [Table name]    Truncate table in database' . PHP_EOL;
		echo '--dry_run                           Parsing file and prepare to processing without insert' . PHP_EOL;
		echo PHP_EOL;
		echo '-u   [User name]          User name' . PHP_EOL;
		echo '-p   [Password]           User password' . PHP_EOL;
		echo '-h   [Host name or IP]    Database host' . PHP_EOL;
		echo '-d   [Database name]      Database name' . PHP_EOL;
		echo PHP_EOL;
		echo '--help    Help information' . PHP_EOL;
		echo PHP_EOL;
	}

	/**
	 * Output error message.
	 *
	 * @param string $parameter Name of parameter
	 *
	 * @return string
	 */
	private function getNotFoundError(string $parameter): string {
		return implode(PHP_EOL, [
			'Value Not Found',
			'PLease, Set ' . $parameter,
			'Type --help to see help information' . PHP_EOL
		]);
	}

	/**
	 * Output missing error message.
	 *
	 * @return string
	 */
	private function getMissingConnectionError(): string {
		return implode(PHP_EOL, [
			'Missing required connection parameters.',
			'PLease, Set -u -p -h -d.',
			'Type --help to see help information.' . PHP_EOL
		]);
	}

	/**
	 * Check if all db parameters exist.
	 *
	 * @return bool
	 */
	private function hasDbParameters(): bool {
		return ($this->config->host && $this->config->user && $this->config->password && $this->config->dbName);
	}
}
