<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

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

			->add('image', FileType::class, [
				'label' => 'Image',

				// unmapped means that this field is not associated to any entity property
				'mapped' => false,

				// make it optional so you don't have to re-upload the PDF file
				// everytime you edit the Product details
				'required' => false,

				// unmapped fields can't define their validation using annotations
				// in the associated entity, so you can use the PHP constraint classes
				'constraints' => [
					new File([
						'maxSize' => '10m',
						'mimeTypes' => [
							'image/jpeg',
							'image/png',
						],
						'mimeTypesMessage' => 'Please upload a valid image file. Max size 10mb, ext jpg, png',
					])
				],
			])

			->add('content', CKEditorType::class, [
				'required' => true
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Post::class,
		]);
	}
}
