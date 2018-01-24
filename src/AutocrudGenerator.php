<?php

declare(strict_types = 1);

namespace Docky\Autocrud;

use Docky\Autocrud\Generator\BaseGenerator;
use Docky\Autocrud\Generator\Facade;
use Docky\Autocrud\Generator\Factory;
use Docky\Autocrud\Generator\FormFactory;
use Docky\Autocrud\Generator\FrontPresenter;
use Docky\Autocrud\Generator\GridFactory;
use Docky\Autocrud\Generator\Presenter;
use Docky\Autocrud\Generator\Repository;
use Nette\Reflection\ClassType;

class AutocrudGenerator
{

	/** @var AutocrudService */
	private $autocrudService;

	/** @var Repository */
	private $repository;

	/** @var Factory */
	private $factory;

	/** @var Facade */
	private $facade;

	/** @var GridFactory */
	private $gridFactory;

	/** @var FormFactory */
	private $formFactory;

	/** @var Presenter */
	private $presenter;

	/** @var FrontPresenter */
	private $frontPresenter;

	public function __construct(
		AutocrudService $autocrudService,
		Repository $repository,
		Factory $factory,
		Facade $facade,
		GridFactory $gridFactory,
		FormFactory $formFactory,
		Presenter $presenter,
		FrontPresenter $frontPresenter
	)
	{
		$this->autocrudService = $autocrudService;
		$this->repository = $repository;
		$this->factory = $factory;
		$this->facade = $facade;
		$this->gridFactory = $gridFactory;
		$this->formFactory = $formFactory;
		$this->presenter = $presenter;
		$this->frontPresenter = $frontPresenter;
	}

	public function generate(string $entity): void
	{
		$reflection = new ClassType($entity);

		$namespace = $reflection->getNamespaceName();
		$className = $reflection->getShortName();

		$classProperties = $reflection->getProperties();

		$properties = [];

		foreach ($classProperties as $property) {
			if ($reflection->getProperty($property->name)->hasAnnotation('Autocrud')) {
				$autocrud = $property->getAnnotation('Autocrud');
				$properties[] = [
					'name' => $property->name,
					'settings' => $autocrud,
				];
			}
		}

		$this->autocrudService->setNamespace($namespace);
		$this->autocrudService->setClassName($className);
		$this->autocrudService->setProperties($properties);

		$this->autocrudService->generateFolders();

		$this->repository->create();
		$this->factory->create();
		$this->facade->create();
		$this->gridFactory->create();
		$this->formFactory->create();
		$this->presenter->create();
		$this->frontPresenter->create();

		$this->autocrudService->generateTemplates();
		$this->autocrudService->appendToConfig();
		$this->autocrudService->appendToMenu();

	}

}
