<?php

declare(strict_types=1);

namespace src\service;

use dto\service\FileRowsDto;
use Exception;
use Throwable;

/**
 * Main Class of Application
 */
class ApplicationRunner {

	const COMMAND_FILE         = 'file';
	const COMMAND_CREATE_TABLE = 'create_table';
	const COMMAND_DROP_TABLE   = 'drop_table';
	const COMMAND_DRY_RUN      = 'dry_run';
	const COMMAND_HELP         = 'help';

	const TYPE_OPTIONAL = '::';

	/** Service  */
	protected FileService $fileService;

	/** @var FileRowsDto[] */
	protected array $rowsData;

	/** Commands */
	protected array $opts;

	/**
	 * @param FileService $fileService
	 */
	public function __construct(FileService $fileService) {
		$this->fileService = $fileService;
	}

	/**
	 * Main action
	 *
	 * @throws Throwable
	 */
	public function run() {
		try {
			$this->processCommand();
		}
		catch (Throwable $e) {
			echo $e->getMessage() . PHP_EOL;
		}
	}

	/**
	 * Processed set command
	 */
	protected function processCommand() {
		$this->opts = getopt(
			'u,p,h',
			[
				static::COMMAND_FILE . static::TYPE_OPTIONAL,
				static::COMMAND_CREATE_TABLE . static::TYPE_OPTIONAL,
				static::COMMAND_DROP_TABLE . static::TYPE_OPTIONAL,
				static::COMMAND_DRY_RUN . static::TYPE_OPTIONAL,
				static::COMMAND_HELP,
			]);

		try {
			if (array_key_exists(static::COMMAND_FILE, $this->opts)) {
				$this->actionFile();
			}

			if (array_key_exists(static::COMMAND_CREATE_TABLE, $this->opts)) {
				$this->actionCreateTable();
			}

			if (array_key_exists(static::COMMAND_DROP_TABLE, $this->opts)) {
				$this->actionDropTable();
			}
		}
		catch (Throwable $e) {
			echo $e->getMessage() . PHP_EOL;
		}

		if (array_key_exists('help', $this->opts)) {
			$this->showHelp();
		}
	}

	/**
	 * Processing file action
	 *
	 * @throws Exception
	 */
	private function actionFile() {
		if (false === $this->opts[static::COMMAND_FILE]) {
			$this->showNotFoundError('File Name');

			throw new Exception();
		}

		$this->rowsData = $this->fileService->prepareData($this->opts['file']);

		echo PHP_EOL;
		echo 'DATA PREPARED:' . PHP_EOL;
		echo '---------------' . PHP_EOL;
		foreach ($this->rowsData as $key => $row) {
			echo $key . '. ' . 'USERNAME: ' . $row->name . ' | SURNAME: ' . $row->surname . ' | EMAIL: ' . $row->email . PHP_EOL;
		}
		echo '---------------' . PHP_EOL;
	}

	/**
	 * Drop table action
	 *
	 * @throws Exception
	 */
	private function actionDropTable() {
		if (false === $this->opts[static::COMMAND_DROP_TABLE]) {
			$this->showNotFoundError('Table Name');

			throw new Exception();
		}

		// @todo at params
		$db = new DbService('localhost', 'user', 'password', 'db_users');

		$db->dropTable($this->opts[static::COMMAND_DROP_TABLE]);
	}

	/**
	 * Create table action
	 *
	 * @throws Exception
	 */
	private function actionCreateTable() {
		if (false === $this->opts[static::COMMAND_CREATE_TABLE]) {
			$this->showNotFoundError('Table Name');

			throw new Exception();
		}

		// @todo at params
		$db = new DbService('localhost', 'user', 'password', 'db_users');

		$db->createTable($this->opts[static::COMMAND_CREATE_TABLE],
			[
				'username VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Username"',
				'surname  VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Surname"',
				'email    VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Email"',
			]
		);
	}

	/**
	 *
	 */
	private function showHelp() {
		// -- help output
	}

	/**
	 * Output error message
	 *
	 * @param string $parameter Name of parameter
	 */
	private function showNotFoundError(string $parameter) {
		echo PHP_EOL;
		echo 'Value Not Found' . PHP_EOL;
		echo 'PLease, Set ' . $parameter . PHP_EOL;
		echo 'Type --help to see help information' . PHP_EOL;
	}
}
