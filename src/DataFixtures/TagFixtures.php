<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Services\FactoryLocales;
use App\Twig\BootstrapColorExtension;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class TagFixtures extends Fixture implements OrderedFixtureInterface
{
	public const COUNT = 10;

	/**
	 * @var SluggerInterface
	 */
	private $slugger;

	private $factory;

	/**
	 * PostFixtures constructor.
	 *
	 * @param SluggerInterface $slugger
	 * @param FactoryLocales   $factoryLocales
	 */
	public function __construct(SluggerInterface $slugger, FactoryLocales $factoryLocales)
	{
		$this->slugger = $slugger;
		$this->factory = $factoryLocales->gatFactory();
	}

	/**
	 * @param ObjectManager $manager
	 *
	 * @throws \Exception
	 */
	public function load(ObjectManager $manager): void
	{
		foreach ($this->factory as $item)
		{
			for ($i = 0; $i < self::COUNT; $i++)
			{
				$title = $item['faker']->country;
				$time  = $item['faker']->unixTime;

				$tag = (new Tag())
					->setTitle($title)
					->setSlug($this->slugger->slug($title))
					->setColor(random_int(0, count(BootstrapColorExtension::COLORS_CLASS) - 1))
					->setCreatedAt($time)
					->setUpdatedAt($time);

				$this->addReference('tag_' . $i . '_' . $item['locale'], $tag);

				$manager->persist($tag);
			}

		}

		$manager->flush();

	}

	/**
	 * @inheritDoc
	 */
	public function getOrder()
	{
		return 1;
	}
}
