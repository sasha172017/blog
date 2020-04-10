<?php

namespace App\Twig;

use Symfony\Component\Intl\Locales;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LocalesExtension extends AbstractExtension
{
	private $localeCodes;
	private $locales;

	/**
	 * LocalesExtension constructor.
	 *
	 * @param string $locales
	 */
	public function __construct(string $locales)
	{
		$this->localeCodes = explode('|', $locales);
	}

	private function mb_ucfirst($str, $encoding = 'UTF-8')
	{
		$str = mb_ereg_replace('^[\ ]+', '', $str);
		$str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
			mb_substr($str, 1, mb_strlen($str), $encoding);

		return $str;
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('locales', [$this, 'getLocales']),
		];
	}


	public function getLocales(): array
	{
		if (null !== $this->locales)
		{
			return $this->locales;
		}

		$this->locales = [];
		foreach ($this->localeCodes as $localeCode)
		{
			$this->locales[$localeCode] = $this->mb_ucfirst(Locales::getName($localeCode, $localeCode));
		}

		return $this->locales;
	}
}
