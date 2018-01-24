<?php

declare(strict_types = 1);

namespace Docky\Autogen\Generator;

use Nette\PhpGenerator\PhpNamespace;

class BaseGenerator
{

	/** @var string */
	protected $filePath;

	/** @var string */
	protected $namespace;

	/** @var string */
	protected $className;

	/** @var mixed[] */
	protected $properties;

	/** @var string */
	protected $classNameLower;

	/**
	 * @param string $filePath
	 * @param string $namespace
	 * @param string $className
	 * @param mixed[] $properties
	 */
	public function __construct(string $filePath, string $namespace, string $className, array $properties = [])
	{
		$this->filePath = $filePath;
		$this->namespace = $namespace;
		$this->className = $className;
		$this->properties = $properties;

		$this->classNameLower = mb_strtolower($className);
	}

	protected function createPhpFile(PhpNamespace $php, string $filePath): void
	{
		$code = '<?php' . PHP_EOL;
		$code .= PHP_EOL;
		$code .= 'declare(strict_types = 1);' . PHP_EOL;
		$code .= PHP_EOL;
		$code .= $php->__toString();

		fopen($filePath, 'w');
		file_put_contents($filePath, $code);
	}

	public function toParameters(): string
	{
		$array = [];
		foreach ($this->properties as $property) {
			$array[] = '$' . $property['name'];
		}

		return implode(', ', $array);
	}

	public function toSetter(string $property): string
	{
		return '$entity->set' . ucfirst($property) . '($' . $property . ');';
	}

}
