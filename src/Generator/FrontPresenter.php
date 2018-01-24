<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Nette\PhpGenerator\PhpNamespace;

class FrontPresenter extends BaseGenerator
{

	public const NAME = 'Presenter';

	private function getFileName(): string
	{
		return $this->getClassName() . self::NAME;
	}

	public function create(): void
	{
		$php = new PhpNamespace($this->getNamespace() . '\UI');

		$php->addUse('App\UI\Front\FrontPresenter');

		$php->addUse('App\\' . $this->getClassName() . '\\' . $this->getClassName() . Repository::NAME);

		$class = $php->addClass($this->getFileName());
		$class->addExtend('App\UI\Front\FrontPresenter');

		$class->addComment('@presenterModule Front');

		$class->addProperty($this->getClassNameLower() . Repository::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->getClassName() . Repository::NAME);

		$method = $class->addMethod('__construct');
		$method->addParameter($this->getClassNameLower() . Repository::NAME)
			->setTypeHint($this->getNamespace() . '\\' . $this->getClassName() . Repository::NAME);

		$body = 'parent::__construct();' . PHP_EOL;
		$body .= '$this->' . $this->getClassNameLower() . Repository::NAME . ' = $' . $this->getClassNameLower() . Repository::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine

		$method->setBody($body);

		$method = $class->addMethod('renderDefault');
		$method->setReturnType('void');

		$filePath = $this->getPath() . 'UI/' . $this->getClassName() . self::NAME . '.php';
		$this->createPhpFile($php, $filePath);
	}

}
