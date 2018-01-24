<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Generator;

use Nette\PhpGenerator\PhpNamespace;

class Repository extends BaseGenerator
{

	public const NAME = 'Repository';

	public function create(): void
	{
		$php = new PhpNamespace($this->getNamespace());

		$php->addUse('App\Doctrine\Repositories\BaseRepository');
		$php->addUse('App\Doctrine\Entities\NoEntityFoundException');
		$php->addUse('Kdyby\Doctrine\QueryBuilder');

		$class = $php->addClass($this->getClassName() . self::NAME);
		$class->addExtend('App\Doctrine\Repositories\BaseRepository');

		$method = $class->addMethod('getDataSourceForDataGrid');
		$method->setReturnType('Kdyby\Doctrine\QueryBuilder');
		$body = 'return $qb = $this->doctrineRepository->createQueryBuilder(\'' . $this->getClassNameLower() . '\');';
		$method->setBody($body);

		$method = $class->addMethod('getById');
		$method->addParameter('id')
			->setTypeHint('int');
		$method->setReturnType($this->getNamespace() . '\\' . $this->getClassName());
		$body = '$entity = $this->doctrineRepository->findOneBy([\'id\' => $id]);' . PHP_EOL;
		$body .= 'if ($entity === null) {' . PHP_EOL;
		$body .= '	throw new NoEntityFoundException();' . PHP_EOL;
		$body .= '}' . PHP_EOL;
		$body .= 'return $entity;';
		$method->setBody($body);

		$method = $class->addMethod('findAll');
		$method->setReturnType('array');
		$method->setComment('@return ' . $this->getClassName() . '[]');
		$body = 'return $this->doctrineRepository->findAll();';
		$method->setBody($body);

		$filePath = $this->getPath() . $this->getClassName() . self::NAME . '.php';
		$this->createPhpFile($php, $filePath);
	}

}
