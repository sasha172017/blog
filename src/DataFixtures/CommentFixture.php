<?php


namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixture extends Fixture implements OrderedFixtureInterface
{
	public const COUNT = 100;

	/**
	 * @inheritDoc
	 * @throws \Exception
	 */
	public function load(ObjectManager $manager)
	{
		$faker = Factory::create();

		for ($i = 0; $i < self::COUNT; $i++)
		{
			$user    = $this->getReference('user_' . random_int(0, UserFixtures::COUNT - 1));
			$post = $this->getReference('post_' . random_int(0, PostFixtures::COUNT - 1));

			$time = $faker->unixTime;

			$comment = (new Comment())
				->setContent($faker->realText())
				->setPost($post)
				->setAuthor($user)
				->setCreatedAt($time)
				->setUpdatedAt($time);

			$manager->persist($comment);
		}

		$manager->flush();
	}

	/**
	 * @inheritDoc
	 */
	public function getOrder()
	{
		return 4;
	}
}
