<?php

declare(strict_types = 1);

namespace Docky\Autogen\Generator;

use Nette\PhpGenerator\PhpNamespace;

class Facade extends BaseGenerator
{

	public const NAME = 'Facade';

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
		$php->addUse('Doctrine\ORM\EntityManager');

		$php->addUse('App\\' . $this->className . '\\' . $this->className . Factory::NAME);
		$php->addUse('App\\' . $this->className . '\\' . $this->className . Repository::NAME);
		$php->addUse('App\\' . $this->className . '\\' . $this->className);

		$class = $php->addClass($this->getFileName());

		$class->addProperty('entityManager')
			->setVisibility('private')
			->addComment('@var EntityManager');

		$class->addProperty($this->classNameLower . Factory::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->className . Factory::NAME);

		$class->addProperty($this->classNameLower . Repository::NAME)
			->setVisibility('private')
			->addComment('@var ' . $this->className . Repository::NAME);

		$method = $class->addMethod('__construct');
		$method->addParameter('entityManager')
			->setTypeHint('Doctrine\ORM\EntityManager');
		$method->addParameter($this->classNameLower . Factory::NAME)
			->setTypeHint($this->namespace . '\\' . $this->className . Factory::NAME);
		$method->addParameter($this->classNameLower . Repository::NAME)
			->setTypeHint($this->namespace . '\\' . $this->className . Repository::NAME);

		$body = '$this->entityManager = $entityManager;' . PHP_EOL;
		$body .= '$this->' . $this->classNameLower . Factory::NAME . ' = $' . $this->classNameLower . Factory::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= '$this->' . $this->classNameLower . Repository::NAME . ' = $' . $this->classNameLower . Repository::NAME.';' . PHP_EOL; // @codingStandardsIgnoreLine

		$method->setBody($body);

		$method = $class->addMethod('create');
		$method->setReturnType($this->namespace . '\\' . $this->className);

		foreach ($this->properties as $property) {
			$method->addParameter($property['name'])
				->setTypeHint($property['settings']['type']);
		}

		$body = '$entity = $this->' . $this->classNameLower . Factory::NAME . '->create(' . $this->toParameters() . ');' . PHP_EOL; // @codingStandardsIgnoreLine
		$body .= PHP_EOL;
		$body .= '$this->entityManager->persist($entity);' . PHP_EOL;
		$body .= '$this->entityManager->flush($entity);' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= 'return $entity;';

		$method->setBody($body);

		$method = $class->addMethod('update');
		$method->setReturnType($this->namespace . '\\' . $this->className);
		$method->addParameter('id')
			->setTypeHint('int');

		foreach ($this->properties as $property) {
			$method->addParameter($property['name'])
				->setTypeHint($property['settings']['type']);
		}

		$body = '$entity = $this->' . $this->classNameLower . Repository::NAME . '->getById($id);' . PHP_EOL;
		$body .= PHP_EOL;

		foreach ($this->properties as $property) {
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

		$body = '$entity = $this->' . $this->classNameLower . Repository::NAME . '->getById($id);' . PHP_EOL;
		$body .= '$this->entityManager->remove($entity);' . PHP_EOL;
		$body .= '$this->entityManager->flush($entity);' . PHP_EOL;

		$method->setBody($body);

		$this->createPhpFile($php, $this->filePath);
	}

}
