<?php

declare(strict_types = 1);

namespace Docky\Autogen;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AutogenCommand extends Command
{

	/** @var string[] */
	private $entities;

	/**
	 * @param string[] $entities
	 */
	public function __construct(array $entities)
	{
		parent::__construct();
		$this->entities = $entities;
	}

	protected function configure(): void
	{
		$this->setName('autogen');
		$this->setDescription('Automatically generated administrative environment');
	}

	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		$output->writeln('<info>Starting...</info>');

		$generator = new AutogenGenerator();

		foreach ($this->entities as $entity) {
			$generator->generate($entity, $output);
		}

		exec('php src/console.php orm:schema-tool:update --force');

		$output->writeln('<info>Done</info>');
	}

}
