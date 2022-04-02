<?php

declare(strict_types=1);

namespace src\components\validators;

/**
 * Email validator
 */
class EmailValidator {

	/**
	 * Validate method
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	public static function validate(string $email): bool {
		if (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) {
			return false;
		}

		return true;
	}
}
