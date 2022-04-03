<?php

declare(strict_types=1);

namespace common\components\message;

/**
 * Message output.
 */
class Message {

	public const CODE_ERROR   = 0;
	public const CODE_WARNING = 1;
	public const CODE_INFO    = 2;

	/**
	 * Output message to STDOUT
	 *
	 * @param string   $message
	 * @param int|null $code
	 */
	public static function output(string $message, ?int $code = 0) {
		if (0 === $code) {
			echo 'ERROR: ' . $message . PHP_EOL;

			return;
		}

		if (1 === $code) {
			echo 'WARNING: ' . $message . PHP_EOL;

			return;
		}

		if (2 === $code) {
			echo 'INFO: ' . $message . PHP_EOL;

			return;
		}

		echo $message;
	}
}
