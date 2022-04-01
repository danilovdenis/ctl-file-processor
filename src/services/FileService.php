<?php

declare(strict_types = 1);

namespace src\service;

use dto\service\FileRowsDto;

/**
 * Service to prepare file
 */
class FileService {

	/**
	 * Prepare data to processing
	 *
	 * @param string $filename Name of file
	 *
	 * @return FileRowsDto[]
	 */
	public function prepareData(string $filename): array {
		$fileContent = file_get_contents($filename);

		$rows = preg_split('/\n|\r\n?/', $fileContent);

		$data = [];
		foreach ($rows as $row) {
			$rowData = explode(',', $row);

			// @todo
			if (3 !== count($rowData)) {
				continue;
			}

			$dto          = new FileRowsDto();
			$dto->name    = $rowData[0];
			$dto->surname = $rowData[1];
			$dto->email   = $rowData[2];

			$preparedRow = $this->prepareRow($dto);
			if (null !== $preparedRow) {
				$data[] = $preparedRow;
			}
		}

		return $data;
	}

	/**
	 * Prepare and validate row
	 *
	 * @param FileRowsDto $fileRow File row model
	 *
	 * @return FileRowsDto|null
	 */
	public function prepareRow(FileRowsDto $fileRow): ?FileRowsDto {
		// @todo need recheck - mo'connor@cat.net.nz?
		if (false === filter_var($fileRow->email, FILTER_VALIDATE_EMAIL)) {
			echo 'Warning: Email ' . $fileRow->email . ' is not valid' . PHP_EOL;

			return null;
		}

		$fileRow->name    = ucfirst(strtolower($fileRow->name));
		$fileRow->surname = ucfirst(strtolower($fileRow->surname));
		$fileRow->email   = strtolower($fileRow->email);

		return $fileRow;
	}
}
