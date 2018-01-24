<?php

declare(strict_types = 1);

namespace Docky\Autogen\Generator;

use Nette\PhpGenerator\PhpNamespace;

class Factory extends BaseGenerator
{

	public const NAME = 'Factory';

	/**
	 * @param string $filePath
	 * @param string $namespace
	 * @param string $className
	 * @param mixed[] $properties
	 */
	public function __construct(string $filePath, string $namespace, string $className, array $properties)
	{
		parent::__construct($filePath, $namespace, $className, $properties);
	}

	private function getFileName(): string
	{
		return $this->className . self::NAME;
	}

	public function create(): void
	{
		$php = new PhpNamespace($this->namespace);

		$class = $php->addClass($this->getFileName());

		$method = $class->addMethod('create');
		$method->setReturnType($this->namespace . '\\' . $this->className);

		foreach ($this->properties as $property) {
			$method->addParameter($property['name'])
				->setTypeHint($property['settings']['type']);
		}

		$method->setBody('return new ' . $this->className . '(' . $this->toParameters() . ');');

		$this->createPhpFile($php, $this->filePath);
	}

}
