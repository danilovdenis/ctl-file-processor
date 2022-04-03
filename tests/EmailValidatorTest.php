<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use common\components\validators\EmailValidator;

/**
 * @see \common\components\validators\EmailValidator
 *
 */
class EmailValidatorTest extends TestCase {

	/**
	 * Test if emil is valid
	 */
	public function testValidateEmail() {
		$email = 'test@test.ru';

		$this->assertTrue(EmailValidator::validate($email));
	}

	/**
	 * Test if emil is not valid
	 */
	public function testValidateNotEmail() {
		$email1 = "tes't@test.ru";
		$email2 = "test!@test.ru";
		$email3 = "test!@test@test.ru";

		$this->assertFalse(EmailValidator::validate($email1));
		$this->assertFalse(EmailValidator::validate($email2));
		$this->assertFalse(EmailValidator::validate($email3));
	}
}
