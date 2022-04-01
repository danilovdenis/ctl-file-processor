<?php

declare(strict_types = 1);

namespace src\service;

use dto\service\FileRowsDto;
use Throwable;

/**
 * Main Class of Application
 */
class ApplicationRunner {

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
				'file::',
				'create_table',
				'dry_run',
				'help',
			])
		;

		if (array_key_exists('file', $opts)) {
			if (!isset($opts['file'])) {
				echo PHP_EOL;
				echo "Value Not Found" . PHP_EOL;
				echo "PLease, Set Filename" . PHP_EOL;
				echo "Type --help to see help information" . PHP_EOL;

				return;
			}

			$this->rowData = $this->fileService->prepareData($opts['file']);

			var_dump($this->rowData);
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
}
