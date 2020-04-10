<?php

namespace App\Form;

use App\Entity\User;
use App\Security\GitHubEntityUserProvider;
use App\Twig\LocalesExtension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class SocialRegistrationFormType extends AbstractType
{
	private $locales;

	/**
	 * @var SessionInterface
	 */
	private $session;

	/**
	 * @var Security
	 */
	private $security;

	/**
	 * RegistrationFormType constructor.
	 *
	 * @param LocalesExtension $localesExtension
	 * @param SessionInterface          $session
	 * @param Security         $security
	 */
	public function __construct(LocalesExtension $localesExtension, SessionInterface $session, Security $security)
	{
		$this->locales  = $localesExtension->getLocales();
		$this->session  = $session;
		$this->security = $security;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$socialData = $this->session->get(GitHubEntityUserProvider::SESSION_DATA_KEY);

		$year = (int) (new \DateTime('now'))->format('Y');

		$builder
			->add('email', EmailType::class, [
				'label_format' => 'app.auth.email',
				'data'         => $this->security->getUser()->getEmail(),
				'disabled'     => true
			])
			->add('nickname', TextType::class, [
				'label_format' => 'app.auth.nickname',
				'data' => $socialData['login']
			])
			->add('locale', ChoiceType::class, [
				'label_format' => 'app.auth.locale',
				'choices'      => array_flip($this->locales),
			])
			->add('avatar', FileType::class, [
				'label'       => 'app.auth.avatar',

				// unmapped means that this field is not associated to any entity property
				'mapped'      => false,

				// make it optional so you don't have to re-upload the PDF file
				// everytime you edit the Product details
				'required'    => false,

				// unmapped fields can't define their validation using annotations
				// in the associated entity, so you can use the PHP constraint classes
				'constraints' => [
					new File([
						'maxSize'   => '10m',
						'mimeTypes' => [
							'image/jpeg',
							'image/png',
							'image/gif'
						],
					])
				],
			])
			->add('dateOfBirth', DateType::class, [
				'label_format' => 'app.auth.date_of_birth',
				'years'        => range($year - 100, $year - 6)
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}
}
