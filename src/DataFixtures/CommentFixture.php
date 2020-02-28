<?php


namespace App\DataFixtures;

use App\Entity\Comment;
use App\Services\FactoryLocales;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixture extends Fixture implements OrderedFixtureInterface
{
	public const COUNT = 100;

	private $factory;

	private $fakerLocales;

	/**
	 * CommentFixture constructor.
	 *
	 * @param FactoryLocales $factoryLocales
	 * @param array          $fakerLocales
	 */
	public function __construct(FactoryLocales $factoryLocales, array $fakerLocales)
	{
		$this->factory = $factoryLocales->gatFactory();
		$this->fakerLocales = $fakerLocales;
	}

	/**
	 * @inheritDoc
	 * @throws \Exception
	 */
	public function load(ObjectManager $manager)
	{
		foreach ($this->factory as $item)
		{
			for ($i = 0; $i < self::COUNT; $i++)
			{
				$user = $this->getReference('user_' . random_int(0, UserFixtures::COUNT - 1) . '_' . $item['locale']);
				$post = $this->getReference('post_' . random_int(0, PostFixtures::COUNT - 1) . '_' . $this->fakerLocales[random_int(0, count($this->fakerLocales) - 1)]);

				$time = $item['faker']->unixTime;

				$comment = (new Comment())
					->setContent($item['faker']->realText())
					->setPost($post)
					->setAuthor($user)
					->setCreatedAt($time)
					->setUpdatedAt($time);

				$manager->persist($comment);
			}

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
