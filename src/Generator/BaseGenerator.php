<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Docky\Autocrud\AutocrudService;
use Nette\PhpGenerator\PhpNamespace;

class BaseGenerator
{

	/** @var AutocrudService */
	private $autocrudService;

	public function __construct(AutocrudService $autocrudService)
	{
		$this->autocrudService = $autocrudService;
	}

	public function getNamespace(): string
	{
		return $this->autocrudService->getNamespace();
	}

	public function getClassName(): string
	{
		return $this->autocrudService->getClassName();
	}

	public function getProperties(): array
	{
		return $this->autocrudService->getProperties();
	}

	public function getClassNameLower(): string
	{
		return mb_strtolower($this->getClassName());
	}

	protected function getPath(): string
	{
		$namespace = str_replace('App', '', $this->getNamespace());
		$namespace = str_replace('\\', '', $namespace);

		$filePath = 'src/' . $namespace . '/';
		return $filePath;
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
