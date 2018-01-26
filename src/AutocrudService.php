<?php

declare(strict_types = 1);

namespace Docky\Autocrud;

use Nette\PhpGenerator\PhpNamespace;

class AutocrudService
{

	/** @var string */
	private $namespace;

	/** @var string */
	private $className;

	/** @var mixed[] */
	private $properties;

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
		$namespace = str_replace('App', '', $this->getNamespace());
		$namespace = str_replace('\\', '', $namespace);

		$filePath = 'src/' . $namespace . '/';
		return $filePath;
	}

	private function createFile(string $filePath, string $data = ''): void
	{
		fopen($filePath, 'w');
		file_put_contents($filePath, $data);
	}

	public function generateFolders(): void
	{
		if (!file_exists($this->getPath() . 'Admin/templates')) {
			mkdir($this->getPath() . 'Admin/templates', 0777, true);
		}

		if (!file_exists($this->getPath() . 'UI/templates')) {
			mkdir($this->getPath() . 'UI/templates', 0777, true);
		}
	}

	public function generateTemplates(): void
	{

		$filePath = $this->getPath() . 'Admin/templates/default.latte';
		$this->createFile($filePath, file_get_contents(__DIR__ . '/Latte/default.latte'));

		$filePath = $this->getPath() . 'Admin/templates/add.latte';
		$this->createFile($filePath, file_get_contents(__DIR__ . '/Latte/add.latte'));

		$filePath = $this->getPath() . 'Admin/templates/form.latte';
		$this->createFile($filePath, file_get_contents(__DIR__ . '/Latte/form.latte'));

		$filePath = $this->getPath() . 'UI/templates/default.latte';
		$body = '{block #content}'. PHP_EOL;
		$body .= 'It works!';
		$this->createFile($filePath, $body);
	}

	public function appendToConfig(): void
	{
		$config = 'config/config.neon';

		$configContent = file_get_contents($config);

		$data = PHP_EOL;
		$data .= '	- ' . $this->getNamespace() . '\\' . $this->getClassName() . 'Factory' . PHP_EOL;
		$data .= '	- ' . $this->getNamespace() . '\Admin\\' . $this->getClassName() . 'Facade' . PHP_EOL;
		$data .= '	- ' . $this->getNamespace() . '\Admin\\' . $this->getClassName() . 'FormFactory' . PHP_EOL;
		$data .= '	- ' . $this->getNamespace() . '\Admin\\' . $this->getClassName() . 'GridFactory' . PHP_EOL;
		$data .= '	- ' . $this->getNamespace() . '\\' . $this->getClassName() . 'Repository(' . $this->namespace . '\\' . $this->className . ')' . PHP_EOL; // @codingStandardsIgnoreLine

		if (strpos($configContent, $data) !== false) {
			file_put_contents($config, '', FILE_APPEND);
		} else {
			file_put_contents($config, $data, FILE_APPEND);
		}
	}

	public function appendToMenu(): void
	{
		$menu = 'src/UI/Admin/menu.latte';

		$menuContent = file_get_contents($menu);

		$data = '{include #item link => \''.$this->getClassName().':\', name => \''.$this->getClassName().'\', icon => \'list\'}' . PHP_EOL; // @codingStandardsIgnoreLine
		$data .= '	<!-- AUTOGEN INCLUDE -->';

		if (strpos($menuContent, $this->getClassName()) !== false) {
			file_put_contents($menu, '', FILE_APPEND);
		} else {
			$menuContent = str_replace('<!-- AUTOGEN INCLUDE -->', $data, $menuContent);
			file_put_contents($menu, $menuContent);
		}
	}

	public function createPhpFile(PhpNamespace $php, string $filePath): void
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
