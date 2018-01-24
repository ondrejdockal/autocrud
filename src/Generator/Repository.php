<?php

declare(strict_types = 1);

namespace Docky\Autogen\Generator;

use Nette\PhpGenerator\PhpNamespace;

class Repository extends BaseGenerator
{

	public const NAME = 'Repository';

	/**
	 * @param string $filePath
	 * @param string $namespace
	 * @param string $className
	 * @param mixed[] $properties
	 */
	public function __construct(string $filePath, string $namespace, string $className, array $properties = [])
	{
		parent::__construct($filePath, $namespace, $className, $properties);
	}

	public function create(): void
	{
		$php = new PhpNamespace($this->namespace);

		$php->addUse('App\Doctrine\Repositories\BaseRepository');
		$php->addUse('App\Doctrine\Entities\NoEntityFoundException');
		$php->addUse('Kdyby\Doctrine\QueryBuilder');

		$class = $php->addClass($this->className . self::NAME);
		$class->addExtend('App\Doctrine\Repositories\BaseRepository');

		$method = $class->addMethod('getDataSourceForDataGrid');
		$method->setReturnType('Kdyby\Doctrine\QueryBuilder');
		$body = 'return $qb = $this->doctrineRepository->createQueryBuilder(\'' . $this->classNameLower . '\');';
		$method->setBody($body);

		$method = $class->addMethod('getById');
		$method->addParameter('id')
			->setTypeHint('int');
		$method->setReturnType($this->namespace . '\\' . $this->className);
		$body = '$entity = $this->doctrineRepository->findOneBy([\'id\' => $id]);' . PHP_EOL;
		$body .= 'if ($entity === null) {' . PHP_EOL;
		$body .= '	throw new NoEntityFoundException();' . PHP_EOL;
		$body .= '}' . PHP_EOL;
		$body .= 'return $entity;';
		$method->setBody($body);

		$method = $class->addMethod('findAll');
		$method->setReturnType('array');
		$method->setComment('@return ' . $this->className . '[]');
		$body = 'return $this->doctrineRepository->findAll();';
		$method->setBody($body);

		$this->createPhpFile($php, $this->filePath);
	}

}
