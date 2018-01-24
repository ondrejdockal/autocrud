<?php

declare(strict_types = 1);

namespace Docky\Autogen\Test;

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
