<?php

declare(strict_types = 1);

namespace src\service;

use dto\service\UsersDto;

/**
 * Service to prepare file
 */
class FileService {

	/**
	 * Prepare data to processing
	 *
	 * @param string $filename Name of file
	 *
	 * @return UsersDto[]
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

			$dto          = new UsersDto();
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
	 * @param UsersDto $fileRow File row model
	 *
	 * @return UsersDto|null
	 */
	public function prepareRow(UsersDto $fileRow): ?UsersDto {
		if (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $fileRow->email)) {
			echo 'Warning: Email ' . $fileRow->email . ' is not valid' . PHP_EOL;

			return null;
		}

		$fileRow->name    = ucfirst(strtolower(trim($fileRow->name)));
		$fileRow->surname = ucfirst(strtolower(trim($fileRow->surname)));
		$fileRow->email   = strtolower(trim($fileRow->email));

		return $fileRow;
	}
}
