<?php

namespace App\Form;

use App\Entity\User;
use App\Twig\LocalesExtension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegistrationFormType extends AbstractType
{
	private $locales;

	/**
	 * RegistrationFormType constructor.
	 *
	 * @param LocalesExtension $localesExtension
	 */
	public function __construct(LocalesExtension $localesExtension)
	{
		$this->locales = $localesExtension->getLocales();
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$year = (int) (new \DateTime('now'))->format('Y');

		$builder
			->add('email', EmailType::class, [
				'label_format' => 'app.auth.email'
			])
			->add('nickname', TextType::class, [
				'label_format' => 'app.auth.nickname'
			])
			->add('locale', ChoiceType::class, [
				'label_format' => 'app.auth.locale',
				'choices' => array_flip($this->locales),
			])
			->add('dateOfBirth', DateType::class, [
				'label_format' => 'app.auth.date_of_birth',
				'years' => range($year - 100, $year - 6)
			])
			->add('plainPassword', RepeatedType::class, [
				'type'        => PasswordType::class,
				'first_options'  => ['label' => 'app.auth.password'],
				'second_options' => ['label' => 'app.auth.repeat_password'],
				'mapped'      => false,
				'constraints' => [

					new Length([
						'min'        => 6,
						'max'        => 4096,
					]),
				],
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}
}
