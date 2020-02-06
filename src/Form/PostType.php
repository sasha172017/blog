<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('title')
			->add('categories', EntityType::class, [
				'attr'         => ['size' => 7],
				'class'        => Category::class,
				'required'     => true,
				'choice_label' => static function ($category) {
					return $category->getTitle();
				},
				'choice_attr'  => ['size' => '10'],
				'multiple'     => true
			])
			->add('summary')
			->add('content', CKEditorType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Post::class,
		]);
	}
}
