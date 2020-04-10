<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class DateExtension
 * @package App\Twig
 */
class DateExtension extends AbstractExtension
{
	/**
	 * @var string
	 */
	private $locale;

	/**
	 * @var string
	 */
	private $dateTimeFormat;

	/**
	 * DateExtension constructor.
	 *
	 * @param RequestStack $requestStack
	 * @param string       $defaultLocale
	 * @param string       $dateTimeFormat
	 */
	public function __construct(RequestStack $requestStack, string $defaultLocale, string $dateTimeFormat)
	{
		if (($request = $requestStack->getCurrentRequest()) !== null)
		{
			$locale = $request->getLocale();
		}

		$this->locale = $locale ?? $defaultLocale;

		$this->dateTimeFormat = $dateTimeFormat;
	}

	/**
	 * @return array|TwigFilter[]
	 */
	public function getFilters(): array
	{
		return [
			new TwigFilter('localizedTimestamp', [$this, 'localizedTimestamp']),
		];
	}

	/**
	 * @param int $timestamp
	 *
	 * @return bool|false|string
	 */
	public function localizedTimestamp(int $timestamp)
	{
		$formatter = new \IntlDateFormatter($this->locale, \IntlDateFormatter::NONE, \IntlDateFormatter::NONE);
		$formatter->setPattern($this->dateTimeFormat);

		return $formatter->format($timestamp);
	}
}
