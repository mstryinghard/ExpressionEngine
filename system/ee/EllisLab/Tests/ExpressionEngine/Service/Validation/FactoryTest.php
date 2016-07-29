<?php

namespace EllisLab\Tests\ExpressionEngine\Service\Validation;

use EllisLab\ExpressionEngine\Service\Validation\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$this->factory = new Factory();
	}

	public function testCheck()
	{
		$result = $this->factory->check('required', '');
		$this->assertFalse($result);

		$result = $this->factory->check('email', 'hello@ellislab.com');
		$this->assertTrue($result);

		$result = $this->factory->check('integer', 1);
		$this->assertTrue($result);
	}
}

// EOF