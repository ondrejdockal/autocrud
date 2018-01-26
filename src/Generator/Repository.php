<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Nette\PhpGenerator\PhpNamespace;

class Repository extends BaseGenerator
{

	public const NAME = 'Repository';

	public function create(): void
	{
		$namespace = $this->autocrudService->getNamespace();
		$php = new PhpNamespace($namespace);

		$php->addUse('App\Doctrine\Repositories\BaseRepository');
		$php->addUse('App\Doctrine\Entities\NoEntityFoundException');
		$php->addUse('Kdyby\Doctrine\QueryBuilder');

		$className = $this->autocrudService->getClassName();
		$class = $php->addClass($className . self::NAME);
		$class->addExtend('App\Doctrine\Repositories\BaseRepository');

		$method = $class->addMethod('getDataSourceForDataGrid');
		$method->setReturnType('Kdyby\Doctrine\QueryBuilder');
		$nameLower = $this->autocrudService->getClassNameLower();
		$body = 'return $qb = $this->doctrineRepository->createQueryBuilder(\'' . $nameLower . '\');';
		$method->setBody($body);

		$method = $class->addMethod('getById');
		$method->addParameter('id')
			->setTypeHint('int');
		$method->setReturnType($namespace . '\\' . $className);
		$body = '$entity = $this->doctrineRepository->findOneBy([\'id\' => $id]);' . PHP_EOL;
		$body .= 'if ($entity === null) {' . PHP_EOL;
		$body .= '	throw new NoEntityFoundException();' . PHP_EOL;
		$body .= '}' . PHP_EOL;
		$body .= 'return $entity;';
		$method->setBody($body);

		$method = $class->addMethod('findAll');
		$method->setReturnType('array');
		$method->setComment('@return ' . $className . '[]');
		$body = 'return $this->doctrineRepository->findAll();';
		$method->setBody($body);

		$filePath = $this->autocrudService->getPath() . $className . self::NAME . '.php';
		$this->autocrudService->createPhpFile($php, $filePath);
	}

}
