<?php


namespace App\Services;

use Faker\Factory;
use Faker\Generator;

class FactoryLocales
{
	/**
	 * @var array $locales
	 */
	private $locales;

	/**
	 * FactoryLocales constructor.
	 *
	 * @param array $fakerLocales
	 */
	public function __construct(array $fakerLocales)
	{
		$this->locales = $fakerLocales;
	}

	/**
	 * @param string $locale
	 *
	 * @return Generator
	 */
	private function generator(string $locale): Generator
	{
		return Factory::create($locale);
	}

	/**
	 * @return array
	 */
	public function gatFactory(): array
	{
		$result = [];

		foreach ($this->locales as $locale)
		{
			$result [] = [
				'locale' => $locale,
				'faker'  => $this->generator($locale)
			];
		}

		return $result;
	}
}
