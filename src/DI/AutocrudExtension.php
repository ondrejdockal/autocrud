<?php

declare(strict_types = 1);

namespace Docky\Autocrud\DI;

use Kdyby\Console\DI\ConsoleExtension;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\Utils\AssertionException;

class AutocrudExtension extends CompilerExtension
{
	public function loadConfiguration()
	{
		if (!$this->compiler->getExtensions('Kdyby\Console\DI\ConsoleExtension')) {
			throw new AssertionException('You need to register \'Kdyby\Console\DI\ConsoleExtension\' before \'' . get_class($this) . '\'.');
		}

		$config = $this->getConfig();

		$builder = $this->getContainerBuilder();

		$builder->addDefinition(
			$this->prefix('command'),
			$this->getCommandServiceDefinition('Docky\Autocrud\Command\AutocrudCommand')
		);

		$builder->addDefinition($this->prefix('generator'))
			->setFactory('Docky\Autocrud\AutocrudGenerator');

		$builder->addDefinition($this->prefix('service'))
			->setFactory('Docky\Autocrud\AutocrudService', ['dir' => $config['dir']]);

		$builder->addDefinition($this->prefix('repository'))
			->setFactory('Docky\Autocrud\Generator\Repository');

		$builder->addDefinition($this->prefix('facade'))
			->setFactory('Docky\Autocrud\Generator\Facade');

		$builder->addDefinition($this->prefix('factory'))
			->setFactory('Docky\Autocrud\Generator\Factory');

		$builder->addDefinition($this->prefix('presenter'))
			->setFactory('Docky\Autocrud\Generator\Presenter');

		$builder->addDefinition($this->prefix('frontpresenter'))
			->setFactory('Docky\Autocrud\Generator\FrontPresenter');

		$builder->addDefinition($this->prefix('gridFactory'))
			->setFactory('Docky\Autocrud\Generator\GridFactory');

		$builder->addDefinition($this->prefix('formFactory'))
			->setFactory('Docky\Autocrud\Generator\FormFactory');

	}

	protected function getCommandServiceDefinition($commandClass)
	{
		$config = $this->getConfig();

		$command = new ServiceDefinition();
		$command->addTag(ConsoleExtension::TAG_COMMAND);
		$command->setClass($commandClass, ['entities' => $config['entities']]);
		$command->setInject(false);
		return $command;
	}
}