<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Security\Voter\CommentVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
	/**
	 * @Route("/new/{id}", name="comment_new", methods={"GET","POST"})
	 * @IsGranted("ROLE_USER_CONFIRMED")
	 * @param Request $request
	 * @param Post    $post
	 *
	 * @return Response
	 */
	public function new(Request $request, Post $post): Response
	{
		$comment = new Comment();
		$comment
			->setAuthor($this->getUser())
			->setPost($post);

		$form = $this->createForm(CommentType::class, $comment);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($comment);
			$entityManager->flush();

			$this->addFlash('success', 'Comment added!');

			return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
		}

		return $this->render('comment/new.html.twig', [
			'comment' => $comment,
			'form'    => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
	 * @param Request $request
	 * @param Comment $comment
	 *
	 * @return Response
	 */
	public function edit(Request $request, Comment $comment): Response
	{
		$this->denyAccessUnlessGranted(CommentVoter::EDIT, $comment, 'Authors can only delete this comment!');

		$form = $this->createForm(CommentType::class, $comment);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$data = $form->getData();

			$this->getDoctrine()->getManager()->flush();

			$this->addFlash('success', 'Comment updated!');

			return $this->redirectToRoute('post_show', ['slug' => $data->getPost()->getSlug()]);
		}

		return $this->render('comment/edit.html.twig', [
			'comment' => $comment,
			'form'    => $form->createView(),
		]);
	}

	/**
	 * @Route("/delete/{id}/{post_id}", name="comment_delete", methods={"GET"})
	 * @Entity("post", expr="repository.find(post_id)")
	 * @param Request $request
	 * @param Comment $comment
	 *
	 * @param Post    $post
	 *
	 * @return Response
	 */
	public function delete(Request $request, Comment $comment, Post $post): Response
	{
		$this->denyAccessUnlessGranted(CommentVoter::DELETE, $comment, 'Authors can only delete this comment!');

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($comment);
		$entityManager->flush();

		$this->addFlash('success', 'Comment deleted!');

		return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
	}
}
