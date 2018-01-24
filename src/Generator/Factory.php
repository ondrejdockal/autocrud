<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Nette\PhpGenerator\PhpNamespace;

class Factory extends BaseGenerator
{

	public const NAME = 'Factory';

	private function getFileName(): string
	{
		return $this->getClassName() . self::NAME;
	}

	public function create(): void
	{
		$php = new PhpNamespace($this->getNamespace());

		$class = $php->addClass($this->getFileName());

		$method = $class->addMethod('create');
		$method->setReturnType($this->getNamespace() . '\\' . $this->getClassName());

		foreach ($this->getProperties() as $property) {
			$method->addParameter($property['name'])
				->setTypeHint($property['settings']['type']);
		}

		$method->setBody('return new ' . $this->getClassName() . '(' . $this->toParameters() . ');');

		$filePath = $this->getPath() . $this->getClassName() . self::NAME . '.php';
		$this->createPhpFile($php, $filePath);
	}

}
