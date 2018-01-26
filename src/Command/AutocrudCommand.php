<?php

declare(strict_types = 1);

namespace Docky\Autocrud\Command;

use Docky\Autocrud\AutocrudGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AutocrudCommand extends Command
{

	/** @var string[] */
	private $entities;

	/** @var AutocrudGenerator */
	private $autocrudGenerator;

	/**
	 * @param string[] $entities
	 * @param AutocrudGenerator $autocrudGenerator
	 */
	public function __construct(array $entities, AutocrudGenerator $autocrudGenerator)
	{
		parent::__construct();
		$this->entities = $entities;
		$this->autocrudGenerator = $autocrudGenerator;
	}

	protected function configure(): void
	{
		$this->setName('autocrud');
		$this->setDescription('Automatically generated administrative environment');
	}

	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		$output->writeln('<info>Starting...</info>');

		foreach ($this->entities as $entity) {
			$this->autocrudGenerator->generate($entity, $output);
		}

		$output->writeln('<info>Done</info>');
	}

}
