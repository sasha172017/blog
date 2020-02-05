<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Twig\BootstrapColorExtension;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture implements OrderedFixtureInterface
{
	public const COUNT = 10;

	/**
	 * @var SluggerInterface
	 */
	private $slugger;

	/**
	 * PostFixtures constructor.
	 *
	 * @param SluggerInterface $slugger
	 */
	public function __construct(SluggerInterface $slugger)
	{
		$this->slugger = $slugger;
	}

	/**
	 * @param ObjectManager $manager
	 *
	 * @throws \Exception
	 */
	public function load(ObjectManager $manager): void
	{
		$faker = Factory::create();

		for ($i = 0; $i < self::COUNT; $i++)
		{
			$title = $faker->country;
			$time  = $faker->unixTime;

			$category = (new Category())
				->setTitle($title)
				->setSlug($this->slugger->slug($title))
				->setColor(random_int(0, count(BootstrapColorExtension::COLORS_CLASS) - 1))
				->setCreatedAt($time)
				->setUpdatedAt($time);

			$this->addReference('category_' . $i, $category);

			$manager->persist($category);
		}

		$manager->flush();
	}

	/**
	 * @inheritDoc
	 */
	public function getOrder()
	{
		return 2;
	}
}
