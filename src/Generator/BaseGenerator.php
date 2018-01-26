<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Docky\Autocrud\AutocrudService;
use Nette\PhpGenerator\PhpNamespace;

class BaseGenerator
{

	/** @var AutocrudService */
	protected $autocrudService;

	public function __construct(AutocrudService $autocrudService)
	{
		$this->autocrudService = $autocrudService;
	}

}
