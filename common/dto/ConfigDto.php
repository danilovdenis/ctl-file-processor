<?php

declare(strict_types=1);

namespace dto\service;

/**
 * Config model.
 */
class ConfigDto {

	public string|null|false $user;
	public string|null|false $password;
	public string|null|false $host;
	public string|null|false $dbName;
	public string            $tableName;
}
