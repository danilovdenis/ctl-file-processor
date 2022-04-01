<?php

declare(strict_types = 1);

namespace src\service;

use dto\service\FileRowsDto;
use Throwable;

/**
 * Main Class of Application
 */
class ApplicationRunner {

	const COMMAND_FILE         = 'file';
	const COMMAND_CREATE_TABLE = 'create_table';

	const TYPE_OPTIONAL = '::';

	/** Service  */
	protected FileService $fileService;

	/** @var FileRowsDto[] */
	protected array $rowData;

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
		$opts = getopt(
			'u,p,h',
			[
				static::COMMAND_FILE . static::TYPE_OPTIONAL,
				static::COMMAND_CREATE_TABLE . static::TYPE_OPTIONAL,
				'dry_run::',
				'help',
			])
		;

		if (array_key_exists(static::COMMAND_FILE, $opts)) {
			if (false === $opts[static::COMMAND_FILE]) {
				$this->showNotFoundError('File Name');

				return;
			}

			$this->rowData = $this->fileService->prepareData($opts['file']);
		}

		if (array_key_exists(static::COMMAND_CREATE_TABLE, $opts)) {
			if (false === $opts[static::COMMAND_CREATE_TABLE]) {
				$this->showNotFoundError('Table Name');

				return;
			}

			// @todo up
			$db = new DbService('localhost', 'user', 'password', 'db_users');

			$db->createTable($opts[static::COMMAND_CREATE_TABLE],
				[
					'username VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Username"',
					'surname  VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Surname"',
					'email    VARCHAR(128) DEFAULT "" NOT NULL COMMENT "Email"',
				]
			);
		}

		if (array_key_exists('help', $opts)) {
			$this->showHelp();
		}
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
		echo 'PLease, Set ' . $parameter  . PHP_EOL;
		echo 'Type --help to see help information' . PHP_EOL;
	}
}
