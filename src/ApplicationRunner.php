<?php

declare(strict_types=1);

namespace src\service;

use dto\service\UsersDto;
use Exception;
use src\components\DBConnection;
use Throwable;

/**
 * Main Class of Application
 */
class ApplicationRunner {

	const COMMAND_FILE           = 'file';
	const COMMAND_CREATE_TABLE   = 'create_table';
	const COMMAND_DROP_TABLE     = 'drop_table';
	const COMMAND_DRY_RUN        = 'dry_run';
	const COMMAND_HELP           = 'help';
	const COMMAND_TRUNCATE_TABLE = 'truncate_table';

	const COMMAND_USER     = 'u';
	const COMMAND_PASSWORD = 'p';
	const COMMAND_HOST     = 'h';

	const TYPE_OPTIONAL = '::';

	/** File service  */
	protected FileService $fileService;

	/** DB service  */
	protected DbService $dbService;

	/** @var UsersDto[] */
	protected array $rowsData;

	/** Commands */
	protected array $opts;

	/** @var string|null|false $fileName */
	protected $fileName;
	/** @var string|null|false $user */
	protected $user;
	/** @var string|null|false $password */
	protected $password;
	/** @var string|null|false $host */
	protected $host;
	/** @var string|null|false $tableName */
	protected $tableName;

	/**
	 * @param FileService $fileService
	 * @param DbService   $dbService
	 */
	public function __construct(FileService $fileService, DbService $dbService) {
		$this->fileService = $fileService;
		$this->dbService   = $dbService;
	}

	/**
	 * Main action
	 *
	 * @throws Throwable
	 */
	public function run() {
		try {
			$this->initOpts();
			$this->processCommand();
		}
		catch (Throwable $e) {
			echo $e->getMessage() . PHP_EOL;
		}
	}

	/**
	 * Initialization options.
	 */
	protected function initOpts() {
		$shortOpts = implode('', [
			static::COMMAND_USER . static::TYPE_OPTIONAL,
			static::COMMAND_PASSWORD . static::TYPE_OPTIONAL,
			static::COMMAND_HOST . static::TYPE_OPTIONAL,
		]);

		$this->opts = getopt(
			$shortOpts,
			[
				static::COMMAND_FILE . static::TYPE_OPTIONAL,
				static::COMMAND_CREATE_TABLE . static::TYPE_OPTIONAL,
				static::COMMAND_DROP_TABLE . static::TYPE_OPTIONAL,
				static::COMMAND_TRUNCATE_TABLE . static::TYPE_OPTIONAL,
				static::COMMAND_DRY_RUN . static::TYPE_OPTIONAL,
				static::COMMAND_HELP,
			]);
	}

	/**
	 * Processed set command
	 */
	protected function processCommand() {
		try {
			$this->fileName = $this->opts[static::COMMAND_FILE]     ?? null;
			$this->user     = $this->opts[static::COMMAND_USER]     ?? null;
			$this->password = $this->opts[static::COMMAND_PASSWORD] ?? null;
			$this->host     = $this->opts[static::COMMAND_HOST]     ?? null;

			if (array_key_exists(static::COMMAND_FILE, $this->opts) && !array_key_exists(static::COMMAND_DRY_RUN, $this->opts)) {
				$this->actionFile();
			}

			if (array_key_exists(static::COMMAND_FILE, $this->opts) && array_key_exists(static::COMMAND_DRY_RUN, $this->opts)) {
				$this->actionDryRun();
			}

			if (array_key_exists(static::COMMAND_CREATE_TABLE, $this->opts)) {
				$this->tableName = $this->opts[static::COMMAND_CREATE_TABLE] ?? null;
				$this->actionCreateTable();
			}

			if (array_key_exists(static::COMMAND_DROP_TABLE, $this->opts)) {
				$this->tableName = $this->opts[static::COMMAND_DROP_TABLE] ?? null;
				$this->actionDropTable();
			}

			if (array_key_exists(static::COMMAND_TRUNCATE_TABLE, $this->opts)) {
				$this->tableName = $this->opts[static::COMMAND_TRUNCATE_TABLE] ?? null;
				$this->actionTruncateTable();
			}
		}
		catch (Throwable $e) {
			echo $e->getMessage() . PHP_EOL;
		}

		if (array_key_exists('help', $this->opts)) {
			$this->actionHelp();
		}
	}

	/**
	 * Processing file action
	 *
	 * @throws Exception
	 */
	private function actionFile() {
		if (!$this->fileName) {
			$this->showNotFoundError('File Name');

			throw new Exception();
		}

		if (!$this->user) {
			$this->showNotFoundError('Username');

			throw new Exception();
		}

		if (!$this->password) {
			$this->showNotFoundError('Password');

			throw new Exception();
		}

		if (!$this->host) {
			$this->showNotFoundError('Host');

			throw new Exception();
		}

		$this->rowsData = $this->fileService->prepareData($this->fileName);

		if (0 === count($this->rowsData)) {
			// @todo
			echo "No users" . PHP_EOL;

			throw new Exception();
		}

		// @todo db opt?
		$this->dbService->connect(new DBConnection($this->host, $this->user, $this->password, 'db_users'));

		$this->dbService->batchInsertUsers($this->rowsData);
	}

	/**
	 * Drop table action
	 *
	 * @throws Exception
	 */
	private function actionDropTable() {
		if (!$this->tableName) {
			$this->showNotFoundError('Table Name');

			throw new Exception();
		}

		$this->dbService->connect(new DBConnection($this->host, $this->user, $this->password, 'db_users'));

		$this->dbService->dropTable($this->tableName);
	}

	/**
	 * Create table action
	 *
	 * @throws Exception
	 */
	private function actionCreateTable() {
		if (!$this->tableName) {
			$this->showNotFoundError('Table Name');

			throw new Exception();
		}

		$this->dbService->connect(new DBConnection($this->host, $this->user, $this->password, 'db_users'));

		$this->dbService->createTable($this->tableName,
			[
				'username VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Username"',
				'surname  VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Surname"',
				'email    VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Email"',
			]
		);
	}

	/**
	 * Truncate table action
	 *
	 * @throws Exception
	 */
	private function actionTruncateTable() {
		if (!$this->tableName) {
			$this->showNotFoundError('Table Name');

			throw new Exception();
		}

		$this->dbService->connect(new DBConnection($this->host, $this->user, $this->password, 'db_users'));

		$this->dbService->truncateTable($this->tableName);
	}

	/**
	 * Parse file without insert into database.
	 *
	 * @throws Exception
	 */
	private function actionDryRun() {
		if (!$this->fileName) {
			$this->showNotFoundError('File Name');

			throw new Exception();
		}

		$this->rowsData = $this->fileService->prepareData($this->fileName);

		echo PHP_EOL;
		echo 'DATA PREPARED:' . PHP_EOL;
		echo '---------------' . PHP_EOL;
		foreach ($this->rowsData as $key => $row) {
			echo $key . '. ' . 'USERNAME: ' . $row->name . ' | SURNAME: ' . $row->surname . ' | EMAIL: ' . $row->email . PHP_EOL;
		}
		echo '---------------' . PHP_EOL;
	}

	/**
	 * Help information.
	 * Output information block into STDOUT.
	 */
	private function actionHelp() {
		echo PHP_EOL;
		echo 'USAGE' . PHP_EOL;
		echo PHP_EOL;
		echo '--file           [File name]        Parsing file and insert into table' . PHP_EOL;
		echo '--create_table   [Table name]       Create table in database' . PHP_EOL;
		echo '--drop_table     [Table name]       Drop table in database' . PHP_EOL;
		echo '--truncate_table [Table name]       Truncate table in database' . PHP_EOL;
		echo '--dry_run                           Parsing file and prepare to processing without insert' . PHP_EOL;
		echo '-u               [User name]        Create table in database' . PHP_EOL;
		echo '-p               [Password]         Create table in database' . PHP_EOL;
		echo '-h               [host name or IP]  Create table in database' . PHP_EOL;
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
}
