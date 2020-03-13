<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Services\FactoryLocales;
use App\Twig\BootstrapColorExtension;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture implements OrderedFixtureInterface
{
	public const COUNT = 5;

	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $passwordEncoder;

	/**
	 * @var array
	 */
	private $factory;

	/**
	 * @var string
	 */
	private $userAvatarsDirectory;

	/**
	 * UserFixtures constructor.
	 *
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @param FactoryLocales               $factoryLocales
	 * @param string                       $userAvatarsDirectory
	 */
	public function __construct(UserPasswordEncoderInterface $passwordEncoder, FactoryLocales $factoryLocales, string $userAvatarsDirectory)
	{
		$this->passwordEncoder      = $passwordEncoder;
		$this->factory              = $factoryLocales->gatFactory();
		$this->userAvatarsDirectory = $userAvatarsDirectory;
	}

	/**
	 * @param ObjectManager $manager
	 *
	 * @throws \Exception
	 */
	public function load(ObjectManager $manager): void
	{
		foreach ($this->factory as $k => $item)
		{
			for ($i = 0; $i < self::COUNT; $i++)
			{
				$time = $item['faker']->unixTime;

				$user = new User();

				if ($k === 0 && $i === 0)
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
						->setEmail($item['faker']->email)
						->setNickname($item['faker']->userName)
						->setPassword($this->passwordEncoder->encodePassword($user, 'blog'))
						->setRoles([User::ROLE_USER_CONFIRMED]);
				}

				$user
					->setColor(random_int(0, count(BootstrapColorExtension::COLORS_CLASS) - 1))
					->setActive(true)
					->setLocale(strstr($item['locale'], '_', true))
					->setCreatedAt($time)
					->setUpdatedAt($time);

//				$avatar = $item['faker']->image($this->userAvatarsDirectory, 150, 150, null, false);
//				if ($avatar !== false)
//				{
//					$user->setAvatar($avatar);
//				}

				$this->addReference('user_' . $i . '_' . $item['locale'], $user);

				$manager->persist($user);
			}
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
