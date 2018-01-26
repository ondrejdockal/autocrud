<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Autocrud
{

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $typehint;

	/**
	 * @var string
	 */
	public $input;

	/**
	 * @var string
	 */
	public $label;

	/**
	 * @var string
	 */
	public $grid;

}
