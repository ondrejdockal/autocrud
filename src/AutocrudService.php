<?php

declare(strict_types = 1);

namespace Docky\Autocrud;

use Nette\PhpGenerator\PhpNamespace;

class AutocrudService
{

	public const ADMIN = 'Admin';
	public const UI = 'UI';

	/** @var string */
	private $dir;

	/** @var string */
	private $name;

	/** @var string */
	private $namespace;

	/** @var string */
	private $className;

	/** @var mixed[] */
	private $properties;

	public function __construct(string $dir)
	{
		$this->dir = $dir;
	}

	public function getDir(): string
	{
		return $this->dir;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getNamespace(): string
	{
		return $this->namespace;
	}

	public function setNamespace(string $namespace): void
	{
		$this->namespace = $namespace;
	}

	public function getClassName(): string
	{
		return $this->className;
	}

	public function getClassNameLower(): string
	{
		return mb_strtolower($this->getClassName());
	}

	public function setClassName(string $className): void
	{
		$this->className = $className;
	}

	public function getProperties(): array
	{
		return $this->properties;
	}

	public function setProperties(array $properties): void
	{
		$this->properties = $properties;
	}

	public function getPath(): string
	{
		$namespace = str_replace('App\\', '', $this->getNamespace());
		$filePath = $this->getDir(). '/' . $namespace . '/';
		return $filePath;
	}

	private function createFile(string $filePath, string $data = ''): void
	{
		fopen($filePath, 'w');
		file_put_contents($filePath, $data);
	}

	public function generateFolders(): void
	{
		if (!file_exists($this->getPath() . self::ADMIN. '/templates')) {
			mkdir($this->getPath() . self::ADMIN. '/templates', 0777, true);
		}

		if (!file_exists($this->getPath() . self::UI. '/templates')) {
			mkdir($this->getPath() . self::UI. '/templates', 0777, true);
		}
	}

	public function generateTemplates(): void
	{

		$filePath = $this->getPath() . self::ADMIN. '/templates/default.latte';
		$this->createFile($filePath, file_get_contents(__DIR__ . '/Latte/default.latte'));

		$filePath = $this->getPath() . self::ADMIN. '/templates/add.latte';
		$this->createFile($filePath, file_get_contents(__DIR__ . '/Latte/add.latte'));

		$filePath = $this->getPath() . self::ADMIN. '/templates/form.latte';
		$this->createFile($filePath, file_get_contents(__DIR__ . '/Latte/form.latte'));

		$filePath = $this->getPath() . self::UI. '/templates/default.latte';
		$body = '{block #content}'. PHP_EOL;
		$body .= 'It works!';
		$this->createFile($filePath, $body);
	}

	public function createPhpFile(PhpNamespace $php, string $filePath): void
	{
		$code = '<?php' . PHP_EOL;
		$code .= PHP_EOL;
		$code .= 'declare(strict_types = 1);' . PHP_EOL;
		$code .= PHP_EOL;
		$code .= $php->__toString();

		$this->createFile($filePath, $code);
	}

	public function toParameters(): string
	{
		$array = [];
		foreach ($this->getProperties() as $property) {
			$array[] = '$' . $property['name'];
		}

		return implode(', ', $array);
	}

	public function toSetter(string $property): string
	{
		return '$entity->set' . ucfirst($property) . '($' . $property . ');';
	}

}
