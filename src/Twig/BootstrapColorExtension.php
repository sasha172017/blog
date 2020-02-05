<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BootstrapColorExtension extends AbstractExtension
{
    public const COLORS_CLASS = [
        'primary',
	    'secondary',
	    'success',
	    'danger',
	    'warning',
	    'info',
	    'dark'
    ];

    public function getFunctions(): array
    {
        return [
	        new TwigFunction('random_color', [$this, 'randomColor']),
	        new TwigFunction('color_by_key', [$this, 'colorByKey']),
        ];
    }

	/**
	 * @param int $key
	 *
	 * @return string
	 */
    public function colorByKey(int $key): string
    {
	    return self::COLORS_CLASS[$key];
    }

	/**
	 * @param string $color
	 *
	 * @return false|int
	 */
    public function keyByColor(string $color)
    {
    	return array_search($color, self::COLORS_CLASS, true);
    }

	/**
	 * @return string
	 * @throws \Exception
	 */
    public function randomColor(): string
    {
        return self::COLORS_CLASS[random_int(0, count(self::COLORS_CLASS) - 1)];
    }
}
