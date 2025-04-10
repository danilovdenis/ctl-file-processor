<?php

declare(strict_types=1);

namespace common\service;

use common\components\message\Message;
use dto\service\ConfigDto;
use common\controller\OptionController;
use Exception;
use Throwable;

/**
 * Main Class of Application.
 */
class ApplicationRunner {

	const string COMMAND_FILE         = 'file';
	const string COMMAND_CREATE_TABLE = 'create_table';
	const string COMMAND_DROP_TABLE = 'drop_table';
	const string COMMAND_DRY_RUN = 'dry_run';
	const string COMMAND_HELP           = 'help';
	const string COMMAND_TRUNCATE_TABLE = 'truncate_table';

	const string COMMAND_USER     = 'u';
	const string COMMAND_PASSWORD = 'p';
	const string COMMAND_HOST     = 'h';
	const string COMMAND_DATABASE = 'd';

	const string TYPE_OPTIONAL = '::';

	const string TABLE_USERS = 'users';

	/** Commands */
	protected array $opts;

	/** @var OptionController Controller */
	protected OptionController $controller;

	/**
	 * Application Constructor.
	 */
	public function __construct() {
		$this->controller = new OptionController(new FileService(), new DbService());
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
			if (1 === $e->getCode()) {
				Message::output($e->getMessage(), Message::CODE_WARNING);
			}
			else {
				Message::output($e->getMessage(), Message::CODE_ERROR);
			}
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
			static::COMMAND_DATABASE . static::TYPE_OPTIONAL,
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

		$optionsDto = new ConfigDto();

		$optionsDto->user      = $this->opts[static::COMMAND_USER]     ?? null;
		$optionsDto->password  = $this->opts[static::COMMAND_PASSWORD] ?? null;
		$optionsDto->host      = $this->opts[static::COMMAND_HOST]     ?? null;
		$optionsDto->dbName    = $this->opts[static::COMMAND_DATABASE] ?? null;
		$optionsDto->tableName = static::TABLE_USERS;

		$this->controller->setConfig($optionsDto);
	}

	/**
	 * Processed set command.
	 *
	 * @throws Throwable
	 */
	protected function processCommand() {
		if (array_key_exists(static::COMMAND_HELP, $this->opts)) {
			$this->controller->actionHelp();

			return;
		}

		try {
			if (array_key_exists(static::COMMAND_CREATE_TABLE, $this->opts)) {
				$this->controller->actionCreateTable();

				return;
			}

			if (array_key_exists(static::COMMAND_FILE, $this->opts) && !array_key_exists(static::COMMAND_DRY_RUN, $this->opts)) {
				$this->controller->actionFile($this->opts[static::COMMAND_FILE] ?? null);

				return;
			}

			if (array_key_exists(static::COMMAND_FILE, $this->opts) && array_key_exists(static::COMMAND_DRY_RUN, $this->opts)) {
				$this->controller->actionDryRun($this->opts[static::COMMAND_FILE] ?? null);

				return;
			}
		}
		catch (Throwable $e) {
			throw new Exception($e->getMessage(), $e->getCode());
		}

		echo implode(PHP_EOL, [
			'Unknown command',
			'Type --help to see help information' . PHP_EOL
		]);
	}
}
