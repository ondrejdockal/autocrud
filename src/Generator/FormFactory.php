<?php

declare(strict_types = 1);

namespace Docky\Autogen\Generator;

use Nette\PhpGenerator\PhpNamespace;

class FormFactory extends BaseGenerator
{

	public const NAME = 'FormFactory';

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

		$php->addUse('Nette\Application\UI\Form');
		$php->addUse('Nette\Utils\ArrayHash');
		$php->addUse($this->namespace . '\\' . $this->className);

		$class = $php->addClass($this->getFileName());

		$method = $class->addMethod('create');
		$method->setReturnType('Nette\Application\UI\Form');

		$method->addParameter('entity')
			->setTypeHint($this->namespace . '\\' . $this->className)
			->setDefaultValue(null)
			->setNullable();

		$method->addParameter('onSuccess')
			->setTypeHint('callable');

		$body = '$form = new Form;' . PHP_EOL;
		$body .= PHP_EOL;

		$prop = [];
		foreach ($this->properties as $property) {
			$prop[] = '$values->' . $property['name'];

			$body .= '$form->add' . ucfirst($property['settings']['inputType']) . '(\'' . $property['name'] . '\', \'' . $property['settings']['inputLabel'] . '\')' . PHP_EOL; // @codingStandardsIgnoreLine

			if ($property['settings']['inputType'] == 'upload') {
				$body .= '	->addRule(Form::MAX_FILE_SIZE, \'Maximální velikost je 5 MB.\', 5 * 1024 * 1024)' . PHP_EOL; // @codingStandardsIgnoreLine

			} else {
				if ($property['name'] == 'email') {
					$body .= '	->addRule(Form::EMAIL, \'Email musí mít platný formát\')' . PHP_EOL;
				}
				$body .= '	->addRule(Form::MAX_LENGTH, \'Maximální délka pole je %d znaků\', 255)' . PHP_EOL;
			}

			$body .= '	->setRequired();' . PHP_EOL;
			$body .= PHP_EOL;
		}

		$body .= '$form->addSubmit(\'send\', \'Uložit\');' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= 'if ($entity !== null) {' . PHP_EOL;
		$body .= '	$form->setDefaults($this->getDefaults($entity));' . PHP_EOL;
		$body .= '}' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= '$form->onSuccess[] = function (Form $form, ArrayHash $values) use ($onSuccess): void {' . PHP_EOL;
		$body .= '	$this->processForm($values, $onSuccess);' . PHP_EOL;
		$body .= '};' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= 'return $form;' . PHP_EOL;

		$method->setBody($body);

		$method = $class->addMethod('processForm');
		$method->setReturnType('void');

		$method->addParameter('values')
			->setTypeHint('Nette\Utils\ArrayHash');

		$method->addParameter('onSuccess')
			->setTypeHint('callable');

		$body = 'call_user_func_array($onSuccess, [' . implode(', ', $prop) . ']);' . PHP_EOL;

		$method->setBody($body);

		$method = $class->addMethod('getDefaults');
		$method->setReturnType('array');

		$method->addParameter('entity')
			->setTypeHint($this->namespace . '\\' . $this->className);

		$method->addComment(' @param ' . $this->className . ' $entity');
		$method->addComment(' @return mixed[]');

		$body = 'return [' . PHP_EOL;

		foreach ($this->properties as $property) {
			$body .= '	\'' . $property['name'] . '\' => $entity->get' . ucfirst($property['name']) . '(),' . PHP_EOL;
		}
		$body .= '];' . PHP_EOL;

		$method->setBody($body);

		$this->createPhpFile($php, $this->filePath);
	}

}