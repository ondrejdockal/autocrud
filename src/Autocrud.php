<?php

declare(strict_types = 1);

namespace Docky\Autocrud;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Autocrud
{

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	public $inputType;

	/**
	 * @var string
	 */
	public $inputLabel;

	/**
	 * @var string
	 */
	public $gridType;

}
