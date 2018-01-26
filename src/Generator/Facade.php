<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Docky\Autocrud\AutocrudService;
use Nette\PhpGenerator\PhpNamespace;

class Facade extends BaseGenerator
{

	public const NAME = 'Facade';

	private function getFileName(): string
	{
		return $this->autocrudService->getClassName() . self::NAME;
	}

	public function create(): void
	{
		$namespace = $this->autocrudService->getNamespace();
		$php = new PhpNamespace($namespace . '\\' . AutocrudService::ADMIN);
		$php->addUse('Doctrine\ORM\EntityManager');

		$className = $this->autocrudService->getClassName();
		$php->addUse('App\\' . $className . '\\' . $className . Factory::NAME);
		$php->addUse('App\\' . $className . '\\' . $className . Repository::NAME);
		$php->addUse('App\\' . $className . '\\' . $className);

		$class = $php->addClass($this->getFileName());

		$class->addProperty('entityManager')
			->setVisibility('private')
			->addComment('@var EntityManager');

		$nameLower = $this->autocrudService->getClassNameLower();
		$class->addProperty($nameLower . Factory::NAME)
			->setVisibility('private')
			->addComment('@var ' . $className . Factory::NAME);

		$class->addProperty($nameLower . Repository::NAME)
			->setVisibility('private')
			->addComment('@var ' . $className . Repository::NAME);

		$method = $class->addMethod('__construct');
		$method->addParameter('entityManager')
			->setTypeHint('Doctrine\ORM\EntityManager');
		$method->addParameter($nameLower . Factory::NAME)
			->setTypeHint($namespace . '\\' . $className . Factory::NAME);
		$method->addParameter($nameLower . Repository::NAME)
			->setTypeHint($namespace . '\\' . $className . Repository::NAME);

		$body = '$this->entityManager = $entityManager;' . PHP_EOL;
		$body .= '$this->' . $nameLower . Factory::NAME . ' = $' . $nameLower . Factory::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '$this->' . $nameLower . Repository::NAME . ' = $' . $nameLower . Repository::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine

		$method->setBody($body);

		$method = $class->addMethod('create');
		$method->setReturnType($namespace . '\\' . $className);

		foreach ($this->autocrudService->getProperties() as $property) {
			$method->addParameter($property['name'])
				->setTypeHint($property['settings']['typehint']);
		}

		$body = '$entity = $this->' . $nameLower . Factory::NAME . '->create(' . $this->autocrudService->toParameters() . ');' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= PHP_EOL;
		$body .= '$this->entityManager->persist($entity);' . PHP_EOL;
		$body .= '$this->entityManager->flush($entity);' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= 'return $entity;';

		$method->setBody($body);

		$method = $class->addMethod('update');
		$method->setReturnType($namespace . '\\' . $className);
		$method->addParameter('id')
			->setTypeHint('int');

		foreach ($this->autocrudService->getProperties() as $property) {
			$method->addParameter($property['name'])
				->setTypeHint($property['settings']['typehint']);
		}

		$body = '$entity = $this->' . $nameLower . Repository::NAME . '->getById($id);' . PHP_EOL;
		$body .= PHP_EOL;

		foreach ($this->autocrudService->getProperties() as $property) {
			$body .= $this->autocrudService->toSetter($property['name']) . PHP_EOL;
		}

		$body .= PHP_EOL;

		$body .= '$this->entityManager->flush($entity);' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= 'return $entity;' . PHP_EOL;

		$method->setBody($body);

		$method = $class->addMethod('delete');
		$method->setReturnType('void');
		$method->addParameter('id')
			->setTypeHint('int');

		$body = '$entity = $this->' . $nameLower . Repository::NAME . '->getById($id);' . PHP_EOL;
		$body .= '$this->entityManager->remove($entity);' . PHP_EOL;
		$body .= '$this->entityManager->flush($entity);' . PHP_EOL;

		$method->setBody($body);

		$filePath = $this->autocrudService->getPath() . AutocrudService::ADMIN. '/' . $className . self::NAME . '.php';
		$this->autocrudService->createPhpFile($php, $filePath);
	}

}
