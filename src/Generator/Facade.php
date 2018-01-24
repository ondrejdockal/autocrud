<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Nette\PhpGenerator\PhpNamespace;

class Facade extends BaseGenerator
{

	public const NAME = 'Facade';

	private function getFileName(): string
	{
		return $this->getClassName() . self::NAME;
	}

	public function create(): void
	{
		$php = new PhpNamespace($this->getNamespace() . '\Admin');
		$php->addUse('Doctrine\ORM\EntityManager');

		$php->addUse('App\\' . $this->getClassName() . '\\' . $this->getClassName() . Factory::NAME);
		$php->addUse('App\\' . $this->getClassName() . '\\' . $this->getClassName() . Repository::NAME);
		$php->addUse('App\\' . $this->getClassName() . '\\' . $this->getClassName());

		$class = $php->addClass($this->getFileName());

		$class->addProperty('entityManager')
			->setVisibility('private')
			->addComment('@var EntityManager');

		$class->addProperty($this->getClassNameLower() . Factory::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->getClassName() . Factory::NAME);

		$class->addProperty($this->getClassNameLower() . Repository::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->getClassName() . Repository::NAME);

		$method = $class->addMethod('__construct');
		$method->addParameter('entityManager')
			->setTypeHint('Doctrine\ORM\EntityManager');
		$method->addParameter($this->getClassNameLower() . Factory::NAME)
			->setTypeHint($this->getNamespace() . '\\' . $this->getClassName() . Factory::NAME);
		$method->addParameter($this->getClassNameLower() . Repository::NAME)
			->setTypeHint($this->getNamespace() . '\\' . $this->getClassName() . Repository::NAME);

		$body = '$this->entityManager = $entityManager;' . PHP_EOL;
		$body .= '$this->' . $this->getClassNameLower() . Factory::NAME . ' = $' . $this->getClassNameLower() . Factory::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '$this->' . $this->getClassNameLower() . Repository::NAME . ' = $' . $this->getClassNameLower() . Repository::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine

		$method->setBody($body);

		$method = $class->addMethod('create');
		$method->setReturnType($this->getNamespace() . '\\' . $this->getClassName());

		foreach ($this->getProperties() as $property) {
			$method->addParameter($property['name'])
				->setTypeHint($property['settings']['type']);
		}

		$body = '$entity = $this->' . $this->getClassNameLower() . Factory::NAME . '->create(' . $this->toParameters() . ');' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= PHP_EOL;
		$body .= '$this->entityManager->persist($entity);' . PHP_EOL;
		$body .= '$this->entityManager->flush($entity);' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= 'return $entity;';

		$method->setBody($body);

		$method = $class->addMethod('update');
		$method->setReturnType($this->getNamespace() . '\\' . $this->getClassName());
		$method->addParameter('id')
			->setTypeHint('int');

		foreach ($this->getProperties() as $property) {
			$method->addParameter($property['name'])
				->setTypeHint($property['settings']['type']);
		}

		$body = '$entity = $this->' . $this->getClassNameLower() . Repository::NAME . '->getById($id);' . PHP_EOL;
		$body .= PHP_EOL;

		foreach ($this->getProperties() as $property) {
			$body .= $this->toSetter($property['name']) . PHP_EOL;
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

		$body = '$entity = $this->' . $this->getClassNameLower() . Repository::NAME . '->getById($id);' . PHP_EOL;
		$body .= '$this->entityManager->remove($entity);' . PHP_EOL;
		$body .= '$this->entityManager->flush($entity);' . PHP_EOL;

		$method->setBody($body);

		$filePath = $this->getPath() . 'Admin/' . $this->getClassName() . self::NAME . '.php';
		$this->createPhpFile($php, $filePath);
	}

}
