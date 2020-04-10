<?php

namespace App\Form;

use App\Entity\Tag;
use App\Twig\BootstrapColorExtension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('title')
			->add('color', ChoiceType::class, [
				'expanded'     => true,
				'choices'      => array_flip(BootstrapColorExtension::COLORS_CLASS),
				'choice_label' => static function ($key, $label) {
					return $label;
				},
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Tag::class,
		]);
	}
}
