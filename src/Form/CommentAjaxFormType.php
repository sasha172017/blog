<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class CommentAjaxFormType
 * @package App\Form
 */
class CommentAjaxFormType extends AbstractType
{
	/**
	 * @var RouterInterface
	 */
	private $router;

	/**
	 * CommentAjaxFormType constructor.
	 *
	 * @param RouterInterface $router
	 */
	public function __construct(RouterInterface $router)
	{
		$this->router = $router;
	}

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$data = $builder->getData();

		$builder
			->setAction($this->router->generate('comment_edit_ajax', ['id' => $data->getId()]))
			->add('content', TextareaType::class, [
				'label' => false,
				'attr'  => [
					'rows' => 3
				]
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Comment::class,
		]);
	}
}
