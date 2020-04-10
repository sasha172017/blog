<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Post;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PostType extends AbstractType
{
	private $locale;

	public function __construct(SessionInterface $session, string $defaultLocale)
	{
		$this->locale = $session->get('_locale', $defaultLocale);
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('title', TextType::class, [
				'label_format' => 'post.form.title'
			])
			->add('tags', EntityType::class, [
				'attr'         => ['size' => 7],
				'class'        => Tag::class,
				'required'     => true,
				'choice_label' => static function ($tag) {
					return $tag->getTitle();
				},
				'choice_attr'  => ['size' => '10'],
				'multiple'     => true,
				'label_format' => 'post.form.tags'
			])
			->add('summary', TextType::class, [
				'label_format' => 'post.form.summary'
			])
			->add('image', FileType::class, [
				'label'       => 'post.form.image',

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
			->add('content', CKEditorType::class, [
				'config' => [
					'language'     => $this->locale,
				],
				'label_format' => 'post.form.content',
				'required'     => true
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Post::class,
		]);
	}
}
