<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Docky\Autocrud\AutocrudService;
use Nette\PhpGenerator\PhpNamespace;

class GridFactory extends BaseGenerator
{

	public const NAME = 'GridFactory';

	private function getFileName(): string
	{
		return $this->autocrudService->getClassName() . self::NAME;
	}

	public function create(): void
	{
		$namespace = $this->autocrudService->getNamespace();
		$php = new PhpNamespace($namespace . '\\' . AutocrudService::ADMIN);

		$php->addUse('App\DataGrid\DataGridFactory');
		$php->addUse('Kdyby\Doctrine\QueryBuilder');
		$php->addUse('Ublaboo\DataGrid\DataGrid');

		$class = $php->addClass($this->getFileName());

		$class->addProperty('dataGridFactory')
			->setVisibility('private')
			->addComment('@var DataGridFactory');

		$method = $class->addMethod('__construct');
		$method->addParameter('dataGridFactory')
			->setTypeHint('App\DataGrid\DataGridFactory');

		$body = '$this->dataGridFactory = $dataGridFactory;' . PHP_EOL;
		$method->setBody($body);

		$method = $class->addMethod('create');
		$method->setReturnType('Ublaboo\DataGrid\DataGrid');

		$method->addParameter('dataSource')
			->setTypeHint('Kdyby\Doctrine\QueryBuilder');

		$body = '$grid = $this->dataGridFactory->create();' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= '$grid->setDataSource($dataSource);' . PHP_EOL;
		$body .= '$grid->setItemsPerPageList([100, 50]);' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= '$grid->addToolbarButton(\'add\', \'Přidat\')' . PHP_EOL;
		$body .= '	->setClass(\'btn btn-xs btn-success\');' . PHP_EOL;

		$body .= PHP_EOL;

		$properties = $this->autocrudService->getProperties();
		foreach ($properties as $property) {
			$body .= '$grid->addColumn' . ucfirst($property['settings']['grid']) . '(\'' . $property['name'] . '\', \'' . $property['settings']['label'] . '\');' . PHP_EOL; // @codingStandardsIgnoreLine
		}

		$body .= PHP_EOL;
		$body .= '$grid->addAction(\'edit\', \'Upravit\');' . PHP_EOL;
		$body .= '$grid->addAction(\'delete\', \'Smazat\', \'delete!\')' . PHP_EOL;
		$body .= '	->setClass(\'btn btn-xs btn-danger\')' . PHP_EOL;
		$body .= '	->setConfirm(\'Opravdu chcete smazat záznam: %s?\', \'id\');' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= 'return $grid;' . PHP_EOL;

		$method->setBody($body);

		$className = $this->autocrudService->getClassName();
		$filePath = $this->autocrudService->getPath() . AutocrudService::ADMIN. '/' . $className . self::NAME . '.php';
		$this->autocrudService->createPhpFile($php, $filePath);
	}

}
