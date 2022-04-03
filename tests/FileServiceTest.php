<?php

namespace tests;

use dto\service\UsersDto;
use PHPUnit\Framework\TestCase;
use common\service\FileService;

/**
 * Test of File Service
 *
 * @see \common\components\validators\EmailValidator
 */
class FileServiceTest extends TestCase {

	/**
	 * Check dto if email is valid
	 */
	public function testPrepareRow() {
		$user = new UsersDto();

		$user->name    = 'john';
		$user->surname = 'xohnsOn';
		$user->email   = 'test@mail.ru';

		$service = new FileService();

		$row = $service->prepareRow($user);

		$this->assertNotNull($row, 'Row is empty');
	}

	/**
	 * Check case of value
	 */
	public function testPrepareRowValue() {
		$user = new UsersDto();

		$user->name    = 'john';
		$user->surname = 'xohnsOn';
		$user->email   = 'test@mail.ru';

		$service = new FileService();

		$row = $service->prepareRow($user);

		$this->assertMatchesRegularExpression('/^[A-Z][a-z]*/', $row->name, 'Name is not valid');
		$this->assertMatchesRegularExpression('/^[A-Z][a-z]*/', $row->surname, 'Surname is not valid');
	}

	/**
	 * Check dto if email is not valid
	 */
	public function testPrepareRowWithInvalidEmail() {
		$user = new UsersDto();

		$user->name    = 'john';
		$user->surname = 'johnsOn';
		$user->email   = "tes't@mail.ru";

		$service = new FileService();

		$this->assertEmpty($service->prepareRow($user), 'Row is not correct');
	}
}
