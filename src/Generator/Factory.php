<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Nette\PhpGenerator\PhpNamespace;

class Factory extends BaseGenerator
{

	public const NAME = 'Factory';

	private function getFileName(): string
	{
		return $this->autocrudService->getClassName() . self::NAME;
	}

	public function create(): void
	{
		$namespace = $this->autocrudService->getNamespace();
		$php = new PhpNamespace($namespace);

		$class = $php->addClass($this->getFileName());

		$method = $class->addMethod('create');
		$className = $this->autocrudService->getClassName();
		$method->setReturnType($namespace . '\\' . $className);

		foreach ($this->autocrudService->getProperties() as $property) {
			$method->addParameter($property['name'])
				->setTypeHint($property['settings']['type']);
		}

		$method->setBody('return new ' . $className . '(' . $this->autocrudService->toParameters() . ');');

		$filePath = $this->autocrudService->getPath() . $className . self::NAME . '.php';
		$this->autocrudService->createPhpFile($php, $filePath);
	}

}
