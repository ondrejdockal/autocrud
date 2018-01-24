# Autocrud

Automatically generate code from an entity for NETTE.

## Installation

```sh
$ composer require docky\autocrud
```

#### To config.neon

```yml
extensions:
	console: Kdyby\Console\DI\ConsoleExtension
	autocrud: Docky\Autocrud\DI\AutocrudExtension

autocrud:
	entities:
		- App\Test\Test

annotations:
	ignore:
		- Docky\Autocrud\Autocrud
		- Autocrud
```

#### To Entity 

```php
/**
 * @var string
 * @ORM\Column(type="string")
 * @Autocrud(type="string", inputType="text", inputLabel="Název", gridType="text")
 */
private $title;
```

#### RUN
```sh
$ bin/console autocrud
```
