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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
	/**
	 * @Route("/new/{id}", name="comment_new", methods={"GET","POST"})
	 * @IsGranted("IS_AUTHENTICATED_FULLY")
	 * @param Request $request
	 * @param Post    $post
	 *
	 * @return Response
	 */
	public function new(Request $request, Post $post, TranslatorInterface $translator): Response
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

			$this->addFlash('success', $translator->trans('comment.messages.success.added'));

			return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
		}

		return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
	}

	/**
	 * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
	 * @param Request $request
	 * @param Comment $comment
	 *
	 * @return Response
	 */
	public function edit(Request $request, Comment $comment, TranslatorInterface $translator): Response
	{
		$this->denyAccessUnlessGranted(CommentVoter::EDIT, $comment, $translator->trans('comment.messages.access.edit'));

		$form = $this->createForm(CommentType::class, $comment);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$data = $form->getData();

			$this->getDoctrine()->getManager()->flush();

			$this->addFlash('success',  $translator->trans('comment.messages.success.updated'));

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
	public function delete(Request $request, Comment $comment, Post $post, TranslatorInterface $translator): Response
	{
		$this->denyAccessUnlessGranted(CommentVoter::DELETE, $comment, $translator->trans('comment.messages.access.delete'));

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($comment);
		$entityManager->flush();

		$this->addFlash('success',  $translator->trans('comment.messages.success.deleted'));

		return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
	}
}
