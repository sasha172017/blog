<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Services\FactoryLocales;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostFixtures extends Fixture implements OrderedFixtureInterface
{
	public const COUNT = 10;

	private $slugger;

	private $factory;

	private $postImagesDirectory;

	/**
	 * PostFixtures constructor.
	 *
	 * @param SluggerInterface $slugger
	 * @param FactoryLocales   $factoryLocales
	 * @param string           $postImagesDirectory
	 */
	public function __construct(SluggerInterface $slugger, FactoryLocales $factoryLocales, string $postImagesDirectory)
	{
		$this->slugger = $slugger;
		$this->factory = $factoryLocales->gatFactory();
		$this->postImagesDirectory = $postImagesDirectory;
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
				$user     = $this->getReference('user_' . random_int(0, UserFixtures::COUNT - 1) . '_' . $item['locale']);
				$category = $this->getReference('category_' . random_int(0, CategoryFixtures::COUNT - 1) . '_' . $item['locale']);

				$title = $item['faker']->realText(50);
				$time  = $item['faker']->unixTime;

				$ratingUp = random_int(0, 100);
				$ratingDown = random_int(0, 50);

				$total = $ratingUp + $ratingDown;
				$rating = $ratingUp - $ratingDown;

				$post = (new Post())
					->setTitle($title)
					->setSlug($this->slugger->slug($title))
					->setSummary($item['faker']->text)
					->setContent($item['faker']->paragraph(random_int(5, 50)))
					->setViews(random_int($total, $total + 100))
					->setAuthor($user)
					->addCategory($category)
					->setRatingUp($ratingUp)
					->setRatingDown($ratingDown)
					->setRating($rating)
					->setCreatedAt($time)
					->setUpdatedAt($time);


				$image = $item['faker']->image($this->postImagesDirectory, 750, 300, null, false);
				if ($image !== false)
				{
					$post->setImage($image);
				}

				$this->addReference('post_' . $i . '_' . $item['locale'], $post);

				$manager->persist($post);
			}

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
