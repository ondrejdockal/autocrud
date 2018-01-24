<?php

declare(strict_types = 1);

namespace Docky\Autogen;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Autogen
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
