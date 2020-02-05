<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$year = (int) (new \DateTime('now'))->format('Y');

		$builder
			->add('email')
			->add('nickname')
			->add('dateOfBirth', DateType::class, [
				'years' => range($year - 100, $year - 6)
			])
			->add('plainPassword', RepeatedType::class, [
				'type'        => PasswordType::class,
				'mapped'      => false,
				'constraints' => [
					new NotBlank([
						'message' => 'Please enter a password',
					]),
					new Length([
						'min'        => 6,
						'minMessage' => 'Your password should be at least {{ limit }} characters',
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
