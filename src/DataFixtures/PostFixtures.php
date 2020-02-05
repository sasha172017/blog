<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostFixtures extends Fixture implements OrderedFixtureInterface
{
	public const COUNT = 50;

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
			$user     = $this->getReference('user_' . random_int(0, UserFixtures::COUNT - 1));
			$category = $this->getReference('category_' . random_int(0, CategoryFixtures::COUNT - 1));

			$title = $faker->realText(50);
			$time  = $faker->unixTime;

			$post = (new Post())
				->setTitle($title)
				->setSlug($this->slugger->slug($title))
				->setSummary($faker->text)
				->setContent($faker->paragraph(random_int(5, 50)))
				->setViews(random_int(0, 50))
				->setAuthor($user)
				->addCategory($category)
				->setCreatedAt($time)
				->setUpdatedAt($time);

			$this->addReference('post_' . $i, $post);

			$manager->persist($post);
		}

		$manager->flush();
	}

	/**
	 * @inheritDoc
	 */
	public function getOrder()
	{
		return 3;
	}
}
