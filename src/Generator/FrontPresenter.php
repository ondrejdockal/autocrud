<?php

declare(strict_types = 1);

namespace Docky\Autogen\Generator;

use Nette\PhpGenerator\PhpNamespace;

class FrontPresenter extends BaseGenerator
{

	public const NAME = 'Presenter';

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
		$php = new PhpNamespace($this->namespace . '\UI');

		$php->addUse('App\UI\Front\FrontPresenter');

		$php->addUse('App\\' . $this->className . '\\' . $this->className . Repository::NAME);

		$class = $php->addClass($this->getFileName());
		$class->addExtend('App\UI\Front\FrontPresenter');

		$class->addComment('@presenterModule Front');

		$class->addProperty($this->classNameLower . Repository::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->className . Repository::NAME);

		$method = $class->addMethod('__construct');
		$method->addParameter($this->classNameLower . Repository::NAME)
			->setTypeHint($this->namespace . '\\' . $this->className . Repository::NAME);

		$body = 'parent::__construct();' . PHP_EOL;
		$body .= '$this->' . $this->classNameLower . Repository::NAME . ' = $' . $this->classNameLower . Repository::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine

		$method->setBody($body);

		$method = $class->addMethod('renderDefault');
		$method->setReturnType('void');;

		$this->createPhpFile($php, $this->filePath);
	}

}
