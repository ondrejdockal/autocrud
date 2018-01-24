<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Nette\PhpGenerator\PhpNamespace;

class Presenter extends BaseGenerator
{

	public const NAME = 'Presenter';

	private function getFileName(): string
	{
		return $this->getClassName() . self::NAME;
	}

	public function create(): void
	{
		$php = new PhpNamespace($this->getNamespace() . '\Admin');

		$php->addUse('App\UI\Admin\AdminPresenter');
		$php->addUse('Nette\Application\UI\Form');
		$php->addUse('Ublaboo\DataGrid\DataGrid');

		$php->addUse('App\\' . $this->getClassName() . '\Admin\\' . $this->getClassName() . Facade::NAME);
		$php->addUse('App\\' . $this->getClassName() . '\Admin\\' . $this->getClassName() . FormFactory::NAME);
		$php->addUse('App\\' . $this->getClassName() . '\Admin\\' . $this->getClassName() . GridFactory::NAME);
		$php->addUse('App\\' . $this->getClassName() . '\\' . $this->getClassName() . Repository::NAME);

		$class = $php->addClass($this->getFileName());
		$class->addExtend('App\UI\Admin\AdminPresenter');

		$class->addComment('@presenterModule Admin');

		$class->addProperty($this->getClassNameLower() . Repository::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->getClassName() . Repository::NAME);

		$class->addProperty($this->getClassNameLower() . Facade::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->getClassName() . Facade::NAME);

		$class->addProperty($this->getClassNameLower() . FormFactory::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->getClassName() . FormFactory::NAME);

		$class->addProperty($this->getClassNameLower() . GridFactory::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->getClassName() . GridFactory::NAME);

		$method = $class->addMethod('__construct');
		$method->addParameter($this->getClassNameLower() . Repository::NAME)
			->setTypeHint($this->getNamespace() . '\\' . $this->getClassName() . Repository::NAME);
		$method->addParameter($this->getClassNameLower() . Facade::NAME)
			->setTypeHint($this->getNamespace() . '\Admin\\' . $this->getClassName() . Facade::NAME);
		$method->addParameter($this->getClassNameLower() . FormFactory::NAME)
			->setTypeHint($this->getNamespace() . '\Admin\\' . $this->getClassName() . FormFactory::NAME);
		$method->addParameter($this->getClassNameLower() . GridFactory::NAME)
			->setTypeHint($this->getNamespace() . '\Admin\\' . $this->getClassName() . GridFactory::NAME);

		$body = 'parent::__construct();' . PHP_EOL;
		$body .= '$this->' . $this->getClassNameLower() . Repository::NAME . ' = $' . $this->getClassNameLower() . Repository::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '$this->' . $this->getClassNameLower() . Facade::NAME . ' = $' . $this->getClassNameLower() . Facade::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '$this->' . $this->getClassNameLower() . FormFactory::NAME . ' = $' . $this->getClassNameLower() . FormFactory::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '$this->' . $this->getClassNameLower() . GridFactory::NAME . ' = $' . $this->getClassNameLower() . GridFactory::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine

		$method->setBody($body);

		$method = $class->addMethod('startUp');
		$method->setReturnType('void');
		$body = 'parent::startUp();' . PHP_EOL;
		$body .= '$this->navigation->add(\'' . $this->getClassName() . '\', $this->link(\'' . $this->getClassName() . ':\'));';
		$method->setBody($body);

		$method = $class->addMethod('renderDefault');
		$method->setReturnType('void');
		$body = '$this->template->heading = \'' . $this->getClassName() . '\';';
		$method->setBody($body);

		$method = $class->addMethod('renderEdit');
		$method->addParameter('id')
			->setTypeHint('int');
		$method->setReturnType('void');
		$body = '$this->template->heading = \'Úprava\';' . PHP_EOL;
		$body .= '$this->navigation->add(\'' . $this->getClassName() . '\', $this->link(\'this\'));' . PHP_EOL;
		$body .= '$this->setView(\'add\');' . PHP_EOL;
		$method->setBody($body);

		$method = $class->addMethod('renderAdd');
		$method->setReturnType('void');
		$body = '$this->template->heading = \'Přidání\';' . PHP_EOL;
		$method->setBody($body);

		$method = $class->addMethod('createComponentDataGrid');
		$method->setReturnType('Ublaboo\DataGrid\DataGrid');
		$body = 'return $this->' . $this->getClassNameLower() . GridFactory::NAME.'->create($this->' . $this->getClassNameLower() . Repository::NAME.'->getDataSourceForDataGrid());' . PHP_EOL; // @codingStandardsIgnoreLine
		$method->setBody($body);

		$method = $class->addMethod('handleDelete');
		$method->setReturnType('void');
		$method->addParameter('id')
			->setTypeHint('int');
		$body = '$this->' . $this->getClassNameLower() . 'Facade->delete($id);' . PHP_EOL;
		$body .= '$this->flashMessage(\'Smazáno\');' . PHP_EOL;
		$body .= '$this->redirect(\'this\');' . PHP_EOL;
		$method->setBody($body);

		$method = $class->addMethod('createComponentAddForm');
		$method->setReturnType('Nette\Application\UI\Form');
		$body = '$id = $this->getParameter(\'id\') !== null ? (int) $this->getParameter(\'id\') : null;' . PHP_EOL;
		$body .= 'if ($id !== null) {' . PHP_EOL;
		$body .= '	$entity = $this->' . $this->getClassNameLower(). Repository::NAME . '->getById($id);' . PHP_EOL;
		$body .= '} else {' . PHP_EOL;
		$body .= '	$entity = null;' . PHP_EOL;
		$body .= '}' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= 'return $this->' . $this->getClassNameLower() . FormFactory::NAME . '->create(' . PHP_EOL;
		$body .= '	$entity,' . PHP_EOL;

		$body .= '	function (' . $this->toParameters() . ') use ($id): void {' . PHP_EOL;
		$body .= '		if ($id !== null) {' . PHP_EOL;
		$body .= '			$this->' . $this->getClassNameLower() . Facade::NAME . '->update($id, ' . $this->toParameters() . ');' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '			$flash = \'Upraveno\';' . PHP_EOL;
		$body .= '		} else {' . PHP_EOL;
		$body .= '			$this->' . $this->getClassNameLower() . Facade::NAME . '->create(' . $this->toParameters() . ');' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '			$flash = \'Vytvořeno\';' . PHP_EOL;
		$body .= '		}' . PHP_EOL;
		$body .= '		$this->flashMessage($flash);' . PHP_EOL;
		$body .= '		$this->redirect(\'default\');' . PHP_EOL;
		$body .= '	}' . PHP_EOL;
		$body .= ');' . PHP_EOL;

		$method->setBody($body);

		$filePath = $this->getPath() . 'Admin/' . $this->getClassName() . self::NAME . '.php';
		$this->createPhpFile($php, $filePath);
	}

}
