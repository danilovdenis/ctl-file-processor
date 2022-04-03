<?php

declare(strict_types = 1);

namespace common\service;

use common\components\message\Message;
use dto\service\UsersDto;
use common\components\validators\EmailValidator;

/**
 * Service to prepare file.
 */
class FileService {

	/**
	 * Prepare data to processing.
	 *
	 * @param string $filename Name of file
	 *
	 * @return UsersDto[]
	 */
	public function prepareData(string $filename): array {
		$fileContent = file_get_contents($filename);

		if (!$fileContent) {
			Message::output('File not found', Message::CODE_WARNING);

			return [];
		}

		$rows = preg_split('/\n|\r\n?/', $fileContent);

		$data = [];
		foreach ($rows as $row) {
			$rowData = explode(',', $row);

			if (0 === count($rowData)) {
				continue;
			}

			if (3 !== count($rowData)) {
				Message::output('Data is incorrect', Message::CODE_WARNING);

				continue;
			}

			$dto          = new UsersDto();
			$dto->name    = $rowData[0];
			$dto->surname = $rowData[1];
			$dto->email   = $rowData[2];

			$preparedRow = $this->prepareRow($dto);
			if (null === $preparedRow) {
				Message::output('Email ' . $dto->email . ' is not valid', Message::CODE_WARNING);
			}
			else {
				$data[] = $preparedRow;
			}
		}

		return $data;
	}

	/**
	 * Prepare and validate row.
	 *
	 * @param UsersDto $fileRow File row model
	 *
	 * @return UsersDto|null
	 */
	public function prepareRow(UsersDto $fileRow): ?UsersDto {
		if (false === (EmailValidator::validate($fileRow->email))) {
			return null;
		}

		$preparedRow = new UsersDto();

		$preparedRow->name    = ucfirst(strtolower(trim($fileRow->name)));
		$preparedRow->surname = ucfirst(strtolower(trim($fileRow->surname)));
		$preparedRow->email   = strtolower(trim($fileRow->email));

		return $preparedRow;
	}
}
