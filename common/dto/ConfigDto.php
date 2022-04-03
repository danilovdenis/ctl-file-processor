<?php

declare(strict_types=1);

namespace dto\service;

/**
 * Config model.
 */
class ConfigDto {
	/** @var string|null|false $user */
	public $user;
	/** @var string|null|false $password */
	public $password;
	/** @var string|null|false $host */
	public $host;
	/** @var string|null|false $dbName */
	public $dbName;
	/** Table name */
	public string $tableName;
}
