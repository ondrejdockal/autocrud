<?php

declare(strict_types = 1);

namespace Docky\Autogen;

class AutogenService
{

	/** @var string */
	private $namespace;

	/** @var string */
	private $className;

	public function setNamespace(string $namespace): void
	{
		$this->namespace = $namespace;
	}

	public function setClassName(string $className): void
	{
		$this->className = $className;
	}

	public function getPath(): string
	{
		$namespace = str_replace('App', '', $this->namespace);
		$namespace = str_replace('\\', '', $namespace);

		$filePath = 'src/' . $namespace . '/';
		return $filePath;
	}

	public function generateTemplates(): void
	{
		if (!file_exists($this->getPath() . 'Admin/templates')) {
			mkdir($this->getPath() . 'Admin/templates', 0777, true);
		}

		if (!file_exists($this->getPath() . 'UI/templates')) {
			mkdir($this->getPath() . 'UI/templates', 0777, true);
		}

		$filePath = $this->getPath() . 'Admin/templates/default.latte';
		$this->createFile($filePath);

		file_put_contents($filePath, file_get_contents(__DIR__ . '/Latte/default.latte'));

		$filePath = $this->getPath() . 'Admin/templates/edit.latte';
		$this->createFile($filePath);

		file_put_contents($filePath, file_get_contents(__DIR__ . '/Latte/edit.latte'));

		$filePath = $this->getPath() . 'UI/templates/default.latte';
		$this->createFile($filePath);

		$body = '{block #content}'. PHP_EOL;
		$body .= 'It works!';

		file_put_contents($filePath, $body);
	}

	private function createFile(string $filePath, string $data = ''): void
	{
		fopen($filePath, 'w');
		file_put_contents($filePath, $data);
	}

	public function appendToConfig(): void
	{
		$config = 'config/config.neon';

		$configContent = file_get_contents($config);

		$data = PHP_EOL;
		$data .= '	- ' . $this->namespace . '\\' . $this->className . 'Factory' . PHP_EOL;
		$data .= '	- ' . $this->namespace . '\Admin\\' . $this->className . 'Facade' . PHP_EOL;
		$data .= '	- ' . $this->namespace . '\Admin\\' . $this->className . 'FormFactory' . PHP_EOL;
		$data .= '	- ' . $this->namespace . '\Admin\\' . $this->className . 'GridFactory' . PHP_EOL;
		$data .= '	- ' . $this->namespace . '\\' . $this->className . 'Repository(' . $this->namespace . '\\' . $this->className . ')' . PHP_EOL; // @codingStandardsIgnoreLine

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

		$data = '{include #item link => \''.$this->className.':\', name => \''.$this->className.'\', icon => \'list\'}' . PHP_EOL; // @codingStandardsIgnoreLine
		$data .= '	<!-- AUTOGEN INCLUDE -->';

		if (strpos($menuContent, $this->className) !== false) {
			file_put_contents($menu, '', FILE_APPEND);
		} else {
			$menuContent = str_replace('<!-- AUTOGEN INCLUDE -->', $data, $menuContent);
			file_put_contents($menu, $menuContent);
		}
	}

}
