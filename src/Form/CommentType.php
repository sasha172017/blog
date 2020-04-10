<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    $comment = $builder->getData();

	    if (!empty($comment))
	    {
		    $builder
			    ->add('post', EntityType::class, [
				    'class'        => Post::class,
				    'choice_label' => 'title',
				    'disabled'     => true,
				    'label_format' => 'comment.post'
			    ]);
	    }

	    $builder
		    ->add('content', TextareaType::class, [
			    'label_format' => 'post.form.content',
			    'attr' => [
				    'rows'  => 3
			    ]
		    ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
