<?php

declare(strict_types = 1);

namespace Docky\Autogen;

use Docky\Autogen\Generator\Facade;
use Docky\Autogen\Generator\Factory;
use Docky\Autogen\Generator\FormFactory;
use Docky\Autogen\Generator\FrontPresenter;
use Docky\Autogen\Generator\GridFactory;
use Docky\Autogen\Generator\Presenter;
use Docky\Autogen\Generator\Repository;
use Nette\Reflection\ClassType;
use Symfony\Component\Console\Output\OutputInterface;

class AutogenGenerator
{

	/** @var string */
	private $namespace;

	/** @var string */
	private $className;

	/** @var mixed[] */
	private $properties;

	public function setNamespace(string $namespace): void
	{
		$this->namespace = $namespace;
	}

	public function setClassName(string $className): void
	{
		$this->className = $className;
	}

	/**
	 * @param mixed[] $properties
	 */
	public function setProperties(array $properties): void
	{
		$this->properties = $properties;
	}

	private function getPath(): string
	{
		$namespace = str_replace('App', '', $this->namespace);
		$namespace = str_replace('\\', '', $namespace);

		$filePath = 'src/' . $namespace . '/';
		return $filePath;
	}

	public function generate(string $entity, OutputInterface $output): void
	{
		$reflection = new ClassType($entity);

		$namespace = $reflection->getNamespaceName();
		$className = $reflection->getShortName();

		$classProperties = $reflection->getProperties();

		$properties = [];

		foreach ($classProperties as $property) {
			if ($reflection->getProperty($property->name)->hasAnnotation('Autogen')) {
				$test = $property->getAnnotation('Autogen');
				$properties[] = [
					'name' => $property->name,
					'settings' => $test,
				];
			}
		}

		$this->setNamespace($namespace);
		$this->setClassName($className);
		$this->setProperties($properties);

		if (!file_exists($this->getPath() . 'UI')) {
			mkdir($this->getPath() . 'UI', 0777, true);
		}

		if (!file_exists($this->getPath() . 'Admin')) {
			mkdir($this->getPath() . 'Admin', 0777, true);
		}

		$this->generateRepository();
		$output->writeln('<comment>' . $className . '</comment> Repository was successfully generated.');

		$this->generateFactory();
		$output->writeln('<comment>' . $className . '</comment> Factory was successfully generated.');

		$this->generateFacade();
		$output->writeln('<comment>' . $className . '</comment> Facade was successfully generated.');

		$this->generateGrid();
		$output->writeln('<comment>' . $className . '</comment> GridFactory was successfully generated.');

		$this->generateForm();
		$output->writeln('<comment>' . $className . '</comment> FormFactory was successfully generated.');

		$this->generatePresenter();
		$output->writeln('<comment>' . $className . '</comment> Admin Presenter was successfully generated.');

		$this->generateFrontPresenter();
		$output->writeln('<comment>' . $className . '</comment> Front Presenter was successfully generated.');

		$autogenService = new AutogenService();

		$autogenService->setNamespace($this->namespace);
		$autogenService->setClassName($this->className);

		$autogenService->generateTemplates();
		$output->writeln('<comment>Templates</comment> was successfully generated.');

		$autogenService->appendToConfig();
		$autogenService->appendToMenu();
	}

	private function generateRepository(): void
	{
		$filePath = $this->getPath() . $this->className . Repository::NAME . '.php';
		$repository = new Repository($filePath, $this->namespace, $this->className);
		$repository->create();
	}

	private function generateFactory(): void
	{
		$filePath = $this->getPath() . $this->className . Factory::NAME . '.php';
		$factory = new Factory($filePath, $this->namespace, $this->className, $this->properties);
		$factory->create();
	}

	private function generateFacade(): void
	{
		$filePath = $this->getPath() . 'Admin/' . $this->className . Facade::NAME . '.php';
		$facade = new Facade($filePath, $this->namespace, $this->className, $this->properties);
		$facade->create();
	}

	private function generatePresenter(): void
	{
		$filePath = $this->getPath() . 'Admin/' . $this->className . Presenter::NAME . '.php';
		$presenter = new Presenter($filePath, $this->namespace, $this->className, $this->properties);
		$presenter->create();
	}

	private function generateFrontPresenter(): void
	{
		$filePath = $this->getPath() . 'UI/' . $this->className . Presenter::NAME . '.php';
		$presenter = new FrontPresenter($filePath, $this->namespace, $this->className, $this->properties);
		$presenter->create();
	}

	private function generateForm(): void
	{
		$filePath = $this->getPath() . 'Admin/' . $this->className . FormFactory::NAME . '.php';
		$formFactory = new FormFactory($filePath, $this->namespace, $this->className, $this->properties);
		$formFactory->create();
	}

	private function generateGrid(): void
	{
		$filePath = $this->getPath() . 'Admin/' . $this->className . GridFactory::NAME . '.php';
		$gridFactory = new GridFactory($filePath, $this->namespace, $this->className, $this->properties);
		$gridFactory->create();
	}

}
