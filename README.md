# Autogen

Automatically generate code from an entity for NETTE.

## Installation

```sh
$ composer require docky\autogen
```

#### To config.neon

```yml
class: Docky\Autogen\AutogenCommand
	arguments:
		-
			- App\Test\Test
	tags: [kdyby.console.command]
```

#### To Entity 

```php
/**
 * @var string
 * @ORM\Column(type="string")
 * @Autogen(type="string", inputType="text", inputLabel="Název", gridType="text")
 */
private $title;
```

#### RUN
```sh
$ bin/console autogen
```
