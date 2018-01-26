<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Docky\Autocrud\AutocrudService;

class BaseGenerator
{

	/** @var AutocrudService */
	protected $autocrudService;

	public function __construct(AutocrudService $autocrudService)
	{
		$this->autocrudService = $autocrudService;
	}

}
