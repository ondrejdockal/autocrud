<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Nette\PhpGenerator\PhpNamespace;

class FrontPresenter extends BaseGenerator
{

	public const NAME = 'Presenter';

	private function getFileName(): string
	{
		return $this->autocrudService->getClassName() . self::NAME;
	}

	public function create(): void
	{
		$namespace = $this->autocrudService->getNamespace();
		$php = new PhpNamespace($namespace . '\UI');

		$php->addUse('App\UI\Front\FrontPresenter');

		$className = $this->autocrudService->getClassName();
		$php->addUse('App\\' . $className . '\\' . $className . Repository::NAME);

		$class = $php->addClass($this->getFileName());
		$class->addExtend('App\UI\Front\FrontPresenter');

		$class->addComment('@presenterModule Front');

		$nameLower = $this->autocrudService->getClassNameLower();
		$class->addProperty($nameLower . Repository::NAME)
			->setVisibility('private')
			->addComment('@var ' . $className . Repository::NAME);

		$method = $class->addMethod('__construct');
		$method->addParameter($nameLower . Repository::NAME)
			->setTypeHint($namespace . '\\' . $className . Repository::NAME);

		$body = 'parent::__construct();' . PHP_EOL;
		$body .= '$this->' . $nameLower . Repository::NAME . ' = $' . $nameLower . Repository::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine

		$method->setBody($body);

		$method = $class->addMethod('renderDefault');
		$method->setReturnType('void');

		$filePath = $this->autocrudService->getPath() . 'UI/' . $className . self::NAME . '.php';
		$this->autocrudService->createPhpFile($php, $filePath);
	}

}
