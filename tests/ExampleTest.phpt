<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Test;

use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

/** @testCase */
class ExampleTest extends TestCase
{

	public function testNothing(): void
	{
		Assert::true(true);
	}

}

$test = new ExampleTest();
$test->run();
