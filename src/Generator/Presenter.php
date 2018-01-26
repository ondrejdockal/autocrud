<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Nette\PhpGenerator\PhpNamespace;

class Presenter extends BaseGenerator
{

	public const NAME = 'Presenter';

	private function getFileName(): string
	{
		return $this->autocrudService->getClassName() . self::NAME;
	}

	public function create(): void
	{
		$namespace = $this->autocrudService->getNamespace();
		$php = new PhpNamespace($namespace . '\Admin');

		$php->addUse('App\UI\Admin\AdminPresenter');
		$php->addUse('Nette\Application\UI\Form');
		$php->addUse('Ublaboo\DataGrid\DataGrid');

		$className = $this->autocrudService->getClassName();
		$php->addUse('App\\' . $className . '\Admin\\' . $className . Facade::NAME);
		$php->addUse('App\\' . $className . '\Admin\\' . $className . FormFactory::NAME);
		$php->addUse('App\\' . $className . '\Admin\\' . $className . GridFactory::NAME);
		$php->addUse('App\\' . $className . '\\' . $className . Repository::NAME);

		$class = $php->addClass($this->getFileName());
		$class->addExtend('App\UI\Admin\AdminPresenter');

		$class->addComment('@presenterModule Admin');

		$nameLower = $this->autocrudService->getClassNameLower();
		$class->addProperty($nameLower . Repository::NAME)
			->setVisibility('private')
			->addComment('@var ' . $className . Repository::NAME);

		$class->addProperty($nameLower . Facade::NAME)
			->setVisibility('private')
			->addComment('@var ' . $className . Facade::NAME);

		$class->addProperty($nameLower . FormFactory::NAME)
			->setVisibility('private')
			->addComment('@var ' . $className . FormFactory::NAME);

		$class->addProperty($nameLower . GridFactory::NAME)
			->setVisibility('private')
			->addComment('@var ' . $className . GridFactory::NAME);

		$method = $class->addMethod('__construct');
		$method->addParameter($nameLower . Repository::NAME)
			->setTypeHint($namespace . '\\' . $className . Repository::NAME);
		$method->addParameter($nameLower . Facade::NAME)
			->setTypeHint($namespace . '\Admin\\' . $className . Facade::NAME);
		$method->addParameter($nameLower . FormFactory::NAME)
			->setTypeHint($namespace . '\Admin\\' . $className . FormFactory::NAME);
		$method->addParameter($nameLower . GridFactory::NAME)
			->setTypeHint($namespace . '\Admin\\' . $className . GridFactory::NAME);

		$body = 'parent::__construct();' . PHP_EOL;
		$body .= '$this->' . $nameLower . Repository::NAME . ' = $' . $nameLower . Repository::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '$this->' . $nameLower . Facade::NAME . ' = $' . $nameLower . Facade::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '$this->' . $nameLower . FormFactory::NAME . ' = $' . $nameLower . FormFactory::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '$this->' . $nameLower . GridFactory::NAME . ' = $' . $nameLower . GridFactory::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine

		$method->setBody($body);

		$method = $class->addMethod('startUp');
		$method->setReturnType('void');
		$body = 'parent::startUp();' . PHP_EOL;
		$body .= '$this->navigation->add(\'' . $className . '\', $this->link(\'' . $className . ':\'));';
		$method->setBody($body);

		$method = $class->addMethod('renderDefault');
		$method->setReturnType('void');
		$body = '$this->template->heading = \'' . $className . '\';';
		$method->setBody($body);

		$method = $class->addMethod('renderEdit');
		$method->addParameter('id')
			->setTypeHint('int');
		$method->setReturnType('void');
		$body = '$this->template->heading = \'Úprava\';' . PHP_EOL;
		$body .= '$this->navigation->add(\'' . $className . '\', $this->link(\'this\'));' . PHP_EOL;
		$body .= '$this->setView(\'add\');' . PHP_EOL;
		$method->setBody($body);

		$method = $class->addMethod('renderAdd');
		$method->setReturnType('void');
		$body = '$this->template->heading = \'Přidání\';' . PHP_EOL;
		$method->setBody($body);

		$method = $class->addMethod('createComponentDataGrid');
		$method->setReturnType('Ublaboo\DataGrid\DataGrid');
		$body = 'return $this->' . $nameLower . GridFactory::NAME.'->create($this->' . $nameLower . Repository::NAME.'->getDataSourceForDataGrid());' . PHP_EOL; // @codingStandardsIgnoreLine
		$method->setBody($body);

		$method = $class->addMethod('handleDelete');
		$method->setReturnType('void');
		$method->addParameter('id')
			->setTypeHint('int');
		$body = '$this->' . $nameLower . 'Facade->delete($id);' . PHP_EOL;
		$body .= '$this->flashMessage(\'Smazáno\');' . PHP_EOL;
		$body .= '$this->redirect(\'this\');' . PHP_EOL;
		$method->setBody($body);

		$method = $class->addMethod('createComponentAddForm');
		$method->setReturnType('Nette\Application\UI\Form');
		$body = '$id = $this->getParameter(\'id\') !== null ? (int) $this->getParameter(\'id\') : null;' . PHP_EOL;
		$body .= 'if ($id !== null) {' . PHP_EOL;
		$body .= '	$entity = $this->' . $nameLower . Repository::NAME . '->getById($id);' . PHP_EOL;
		$body .= '} else {' . PHP_EOL;
		$body .= '	$entity = null;' . PHP_EOL;
		$body .= '}' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= 'return $this->' . $nameLower . FormFactory::NAME . '->create(' . PHP_EOL;
		$body .= '	$entity,' . PHP_EOL;

		$body .= '	function (' . $this->autocrudService->toParameters() . ') use ($id): void {' . PHP_EOL;
		$body .= '		if ($id !== null) {' . PHP_EOL;
		$body .= '			$this->' . $nameLower . Facade::NAME . '->update($id, ' . $this->autocrudService->toParameters() . ');' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '			$flash = \'Upraveno\';' . PHP_EOL;
		$body .= '		} else {' . PHP_EOL;
		$body .= '			$this->' . $nameLower . Facade::NAME . '->create(' . $this->autocrudService->toParameters() . ');' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '			$flash = \'Vytvořeno\';' . PHP_EOL;
		$body .= '		}' . PHP_EOL;
		$body .= '		$this->flashMessage($flash);' . PHP_EOL;
		$body .= '		$this->redirect(\'default\');' . PHP_EOL;
		$body .= '	}' . PHP_EOL;
		$body .= ');' . PHP_EOL;

		$method->setBody($body);

		$filePath = $this->autocrudService->getPath() . 'Admin/' . $className . self::NAME . '.php';
		$this->autocrudService->createPhpFile($php, $filePath);
	}

}
