<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Security\Voter\PostVoter;
use App\Services\FileUploader;
use App\Services\TopPosts;
use App\Services\UrlRemember;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/post")
 * Class PostController
 * @package App\Controller
 */
class PostController extends AbstractController
{
	/**
	 * @Route("/new", name="post_new", methods={"GET","POST"})
	 * @IsGranted("ROLE_USER_CONFIRMED")
	 * @param Request          $request
	 *
	 * @param SluggerInterface $slugger
	 *
	 * @param FileUploader     $fileUploader
	 *
	 * @return Response
	 */
	public function new(Request $request, SluggerInterface $slugger, FileUploader $fileUploader): Response
	{
		$post = new Post();
		$form = $this->createForm(PostType::class, $post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$data = $form->getData();

			$image = $form->get('image')->getData();
			if ($image)
			{
				$imageFileName = $fileUploader->upload($image, 'post_images_directory');
				$post->setImage($imageFileName);
			}

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
	 * @Route("/{slug}/show", name="post_show", methods={"GET"})
	 * @param Post        $post
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
	 * @param Request             $request
	 * @param Post                $post
	 * @param SluggerInterface    $slugger
	 * @param FileUploader        $fileUploader
	 * @param TranslatorInterface $translator
	 *
	 * @return Response
	 */
	public function edit(Request $request, Post $post, SluggerInterface $slugger, FileUploader $fileUploader, TranslatorInterface $translator): Response
	{
		$this->denyAccessUnlessGranted(PostVoter::EDIT, $post, $translator->trans('post.messages.access.edit'));

		$form = $this->createForm(PostType::class, $post);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$post
				->setSlug($slugger->slug($post->getTitle()));

			$image = $form->get('image')->getData();
			if ($image)
			{
				$imageFileName = $fileUploader->upload($image, 'post_images_directory');
				@unlink($this->getParameter('post_images_directory') . '/' . $post->getImage());
				$post->setImage($imageFileName);
			}

			$this->getDoctrine()->getManager()->flush();

			$this->addFlash('success', sprintf($translator->trans('post.messages.success.edit'), $post->getTitle()));

			return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
		}

		return $this->render('post/edit.html.twig', [
			'post' => $post,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}", name="post_delete", methods={"DELETE"})
	 * @IsGranted("ROLE_USER")
	 * @param Request             $request
	 * @param Post                $post
	 *
	 * @param TranslatorInterface $translator
	 *
	 * @return Response
	 */
	public function delete(Request $request, Post $post, TranslatorInterface $translator): Response
	{
		if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token')))
		{
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove($post);
			$entityManager->flush();

			@unlink($this->getParameter('post_images_directory') . '/' . $post->getImage());

			$this->addFlash('success', sprintf($translator->trans('post.messages.success.deleted'), $post->getTitle()));

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

	/**
	 * @Route("/{slug}/rating-up", name="post_rating_up", methods={"GET", "POST"})
	 * @IsGranted("ROLE_USER")
	 * @param Post                $post
	 *
	 * @param TranslatorInterface $translator
	 *
	 * @return RedirectResponse
	 */
	public function ratingUp(Post $post, TranslatorInterface $translator): RedirectResponse
	{
		$post->setRatingUp($post->getRatingUp() + 1);
		$this->getDoctrine()->getManager()->flush();

		$this->addFlash('success', $translator->trans('post.messages.rating'));

		return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
	}

	/**
	 * @Route("/{slug}/rating-down", name="post_rating_down", methods={"GET", "POST"})
	 * @IsGranted("ROLE_USER")
	 * @param Post                $post
	 *
	 * @param TranslatorInterface $translator
	 *
	 * @return RedirectResponse
	 */
	public function ratingDown(Post $post, TranslatorInterface $translator): RedirectResponse
	{
		$post->setRatingDown($post->getRatingDown() - 1);
		$this->getDoctrine()->getManager()->flush();

		$this->addFlash('success', $translator->trans('post.messages.rating'));

		return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
	}

	/**
	 * @Route("/top", name="post_top", methods={"GET"})
	 * @param Request            $request
	 * @param PostRepository     $postRepository
	 * @param PaginatorInterface $paginator
	 * @param int                $postLimitPerPage
	 * @return Response
	 */
	public function top(Request $request, PostRepository $postRepository, PaginatorInterface $paginator, int $postLimitPerPage): Response
	{
		$pagination = $paginator->paginate(
			$postRepository->top(),
			$request->query->getInt('page', 1),
			$postLimitPerPage
		);

		return $this->render($request->isXmlHttpRequest() ? 'post/_items.html.twig' : 'post/index.html.twig', ['pagination' => $pagination]);
	}

}
