<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Security\Voter\PostVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{

	/**
	 * @Route("/new", name="post_new", methods={"GET","POST"})
	 * @IsGranted("ROLE_USER")
	 * @param Request          $request
	 *
	 * @param SluggerInterface $slugger
	 *
	 * @return Response
	 */
	public function new(Request $request, SluggerInterface $slugger): Response
	{
		$post = new Post();
		$form = $this->createForm(PostType::class, $post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$data = $form->getData();

			$post
				->setAuthor($this->getUser())
				->setSlug($slugger->slug($data->getTitle()))
				->setViews(0);

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($post);
			$entityManager->flush();

			$this->addFlash('success', sprintf('Post <b>%s</b> added!', $data->getTitle()));

			return $this->redirectToRoute('blog_index');
		}

		return $this->render('post/new.html.twig', [
			'post' => $post,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{slug}", name="post_show", methods={"GET"})
	 * @param Post $post
	 *
	 * @return Response
	 */
	public function show(Post $post): Response
	{
		$post->setViews($post->getViews() + 1);
		$this->getDoctrine()->getManager()->flush();

		return $this->render('post/show.html.twig', [
			'post' => $post,
		]);
	}

	/**
	 * @Route("/{slug}/edit", name="post_edit", methods={"GET","POST"})
	 * @IsGranted("ROLE_USER")
	 * @param Request          $request
	 * @param Post             $post
	 * @param SluggerInterface $slugger
	 *
	 * @return Response
	 */
	public function edit(Request $request, Post $post, SluggerInterface $slugger): Response
	{
		$this->denyAccessUnlessGranted(PostVoter::EDIT, $post, 'Authors can only edit this post!');

		$form = $this->createForm(PostType::class, $post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$post
				->setSlug($slugger->slug($post->getTitle()));

			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute('blog_index');
		}

		return $this->render('post/edit.html.twig', [
			'post' => $post,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}", name="post_delete", methods={"DELETE"})
	 * @IsGranted("ROLE_USER")
	 * @param Request $request
	 * @param Post    $post
	 *
	 * @return Response
	 */
	public function delete(Request $request, Post $post): Response
	{
		if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token')))
		{
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove($post);
			$entityManager->flush();

			$this->addFlash('success', sprintf('Post <b>%s</b> deleted!', $post->getTitle()));

		}

		return $this->redirectToRoute('blog_index');
	}

	/**
	 * @param Post $post
	 *
	 * @return Response
	 */
	public function commentForm(Post $post): Response
	{
		$form = $this->createForm(CommentType::class);

		return $this->render('post/_comment-form.html.twig', [
			'post' => $post,
			'form' => $form->createView(),
		]);
	}

}
