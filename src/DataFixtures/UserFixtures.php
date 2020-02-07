<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Twig\BootstrapColorExtension;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
	public const COUNT = 10;

	private $passwordEncoder;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->passwordEncoder = $passwordEncoder;
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
			$time = $faker->unixTime;

			$user = new User();

			if ($i === 0)
			{
				$user
					->setEmail('admin@blog.com')
					->setNickname('admin')
					->setPassword($this->passwordEncoder->encodePassword($user, 'admin'))
					->setRoles([User::ROLE_ADMIN, User::ROLE_USER_CONFIRMED]);
			}
			else
			{
				$user
					->setEmail($faker->email)
					->setNickname($faker->userName)
					->setPassword($this->passwordEncoder->encodePassword($user, 'blog'))
					->setRoles([User::ROLE_USER_CONFIRMED]);
			}

			$user
				->setColor(random_int(0, count(BootstrapColorExtension::COLORS_CLASS) - 1))
				->setActive(true)
				->setCreatedAt($time)
				->setUpdatedAt($time);

			$this->addReference('user_' . $i, $user);

			$manager->persist($user);
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
