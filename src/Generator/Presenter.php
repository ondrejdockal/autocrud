<?php

declare(strict_types = 1);

namespace Docky\Autogen\Generator;

use Nette\PhpGenerator\PhpNamespace;

class Presenter extends BaseGenerator
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
		$php = new PhpNamespace($this->namespace . '\Admin');

		$php->addUse('App\UI\Admin\AdminPresenter');
		$php->addUse('Nette\Application\UI\Form');
		$php->addUse('Ublaboo\DataGrid\DataGrid');

		$php->addUse('App\\' . $this->className . '\Admin\\' . $this->className . Facade::NAME);
		$php->addUse('App\\' . $this->className . '\Admin\\' . $this->className . FormFactory::NAME);
		$php->addUse('App\\' . $this->className . '\Admin\\' . $this->className . GridFactory::NAME);
		$php->addUse('App\\' . $this->className . '\\' . $this->className . Repository::NAME);

		$class = $php->addClass($this->getFileName());
		$class->addExtend('App\UI\Admin\AdminPresenter');

		$class->addComment('@presenterModule Admin');

		$class->addProperty($this->classNameLower . Repository::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->className . Repository::NAME);

		$class->addProperty($this->classNameLower . Facade::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->className . Facade::NAME);

		$class->addProperty($this->classNameLower . FormFactory::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->className . FormFactory::NAME);

		$class->addProperty($this->classNameLower . GridFactory::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->className . GridFactory::NAME);

		$method = $class->addMethod('__construct');
		$method->addParameter($this->classNameLower . Repository::NAME)
			->setTypeHint($this->namespace . '\\' . $this->className . Repository::NAME);
		$method->addParameter($this->classNameLower . Facade::NAME)
			->setTypeHint($this->namespace . '\Admin\\' . $this->className . Facade::NAME);
		$method->addParameter($this->classNameLower . FormFactory::NAME)
			->setTypeHint($this->namespace . '\Admin\\' . $this->className . FormFactory::NAME);
		$method->addParameter($this->classNameLower . GridFactory::NAME)
			->setTypeHint($this->namespace . '\Admin\\' . $this->className . GridFactory::NAME);

		$body = 'parent::__construct();' . PHP_EOL;
		$body .= '$this->' . $this->classNameLower . Repository::NAME . ' = $' . $this->classNameLower . Repository::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '$this->' . $this->classNameLower . Facade::NAME . ' = $' . $this->classNameLower . Facade::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '$this->' . $this->classNameLower . FormFactory::NAME . ' = $' . $this->classNameLower . FormFactory::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '$this->' . $this->classNameLower . GridFactory::NAME . ' = $' . $this->classNameLower . GridFactory::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine

		$method->setBody($body);

		$method = $class->addMethod('startUp');
		$method->setReturnType('void');
		$body = 'parent::startUp();' . PHP_EOL;
		$body .= '$this->navigation->add(\'' . $this->className . '\', $this->link(\'' . $this->className . ':\'));';
		$method->setBody($body);

		$method = $class->addMethod('renderDefault');
		$method->setReturnType('void');
		$body = '$this->template->heading = \'' . $this->className . '\';';
		$method->setBody($body);

		$method = $class->addMethod('renderEdit');
		$method->addParameter('id')
			->setTypeHint('int');
		$method->setReturnType('void');
		$body = '$this->template->heading = \'Úprava\';' . PHP_EOL;
		$body .= '$this->navigation->add(\'' . $this->className . '\', $this->link(\'this\'));' . PHP_EOL;
		$method->setBody($body);

		$method = $class->addMethod('renderAdd');
		$method->setReturnType('void');
		$body = '$this->template->heading = \'Přidání\';' . PHP_EOL;
		$body .= '$this->setView(\'edit\');' . PHP_EOL;
		$method->setBody($body);

		$method = $class->addMethod('createComponentDataGrid');
		$method->setReturnType('Ublaboo\DataGrid\DataGrid');
		$body = 'return $this->' . $this->classNameLower . GridFactory::NAME.'->create($this->' . $this->classNameLower . Repository::NAME.'->getDataSourceForDataGrid());' . PHP_EOL; // @codingStandardsIgnoreLine
		$method->setBody($body);

		$method = $class->addMethod('handleDelete');
		$method->setReturnType('void');
		$method->addParameter('id')
			->setTypeHint('int');
		$body = '$this->' . $this->classNameLower . 'Facade->delete($id);' . PHP_EOL;
		$body .= '$this->flashMessage(\'Smazáno\');' . PHP_EOL;
		$body .= '$this->redirect(\'this\');' . PHP_EOL;
		$method->setBody($body);

		$method = $class->addMethod('createComponentEditForm');
		$method->setReturnType('Nette\Application\UI\Form');
		$body = '$id = $this->getParameter(\'id\') !== null ? (int) $this->getParameter(\'id\') : null;' . PHP_EOL;
		$body .= 'if ($id !== null) {' . PHP_EOL;
		$body .= '	$entity = $this->' . $this->classNameLower . Repository::NAME . '->getById($id);' . PHP_EOL;
		$body .= '} else {' . PHP_EOL;
		$body .= '	$entity = null;' . PHP_EOL;
		$body .= '}' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= 'return $this->' . $this->classNameLower . FormFactory::NAME . '->create(' . PHP_EOL;
		$body .= '	$entity,' . PHP_EOL;

		$body .= '	function (' . $this->toParameters() . ') use ($id): void {' . PHP_EOL;
		$body .= '		if ($id !== null) {' . PHP_EOL;
		$body .= '			$this->' . $this->classNameLower . Facade::NAME . '->update($id, ' . $this->toParameters() . ');' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '			$flash = \'Upraveno\';' . PHP_EOL;
		$body .= '		} else {' . PHP_EOL;
		$body .= '			$this->' . $this->classNameLower . Facade::NAME . '->create(' . $this->toParameters() . ');' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '			$flash = \'Vytvořeno\';' . PHP_EOL;
		$body .= '		}' . PHP_EOL;
		$body .= '		$this->flashMessage($flash);' . PHP_EOL;
		$body .= '		$this->redirect(\'default\');' . PHP_EOL;
		$body .= '	}' . PHP_EOL;
		$body .= ');' . PHP_EOL;

		$method->setBody($body);

		$this->createPhpFile($php, $this->filePath);
	}

}
