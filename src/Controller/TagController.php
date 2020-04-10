<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Services\PostPagination;
use App\Services\PostPaginationSortQuery;
use App\Services\UrlRemember;
use App\Twig\BootstrapColorExtension;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TagController
 * @package App\Controller
 * @Route("/tag")
 *
 */
class TagController extends AbstractController
{
	/**
	 * @param TagRepository $tagRepository
	 *
	 * @return Response
	 */
	public function list(TagRepository $tagRepository): Response
	{
		return $this->render('default/tags-block.html.twig', [
			'tags' => $tagRepository->findBy([], ['createdAt' => 'DESC'])
		]);
	}

	/**
	 * @Route("/{slug}/posts", name="tag_posts", methods={"GET"})
	 * @param Tag                     $tag
	 *
	 * @param Request                 $request
	 * @param PostPagination          $pagination
	 * @param PostPaginationSortQuery $paginationSortQuery
	 * @param UrlRemember             $urlRemember
	 *
	 * @return Response
	 */
	public function posts(Tag $tag, Request $request, PostPagination $pagination, PostPaginationSortQuery $paginationSortQuery, UrlRemember $urlRemember): Response
	{
		$urlRemember->remember();

		$paginator = $pagination->pagination($paginationSortQuery->tag($tag->getId()));

		if ($request->isXmlHttpRequest())
		{
			return $this->render('post/_items.html.twig', ['pagination' => $paginator]);
		}

		return $this->render('tag/posts.html.twig', [
			'pagination' => $paginator,
			'tag'   => $tag
		]);
	}

	/**
	 * @Route("/{slug}/posts/search", name="tag_posts_search", methods={"GET"})
	 * @param Request                 $request
	 * @param Tag                     $tag
	 * @param UrlRemember             $urlRemember
	 * @param PostPagination          $pagination
	 * @param PostRepository          $postRepository
	 *
	 * @param PostPaginationSortQuery $paginationSortQuery
	 *
	 * @return Response
	 */
	public function search(Request $request, Tag $tag, UrlRemember $urlRemember, PostPagination $pagination, PostRepository $postRepository, PostPaginationSortQuery $paginationSortQuery): Response
	{
		$urlRemember->remember();

		if (!empty($request->get('q')))
		{
			$query = $postRepository->searchWithTag($request->get('q'), $tag->getId());
		}

		$paginator = $pagination->pagination($query ?? $paginationSortQuery->tag($tag->getId()));

		if ($request->isXmlHttpRequest())
		{
			return $this->render('post/_items.html.twig', ['pagination' => $paginator]);
		}

		return $this->render('tag/posts.html.twig', [
			'pagination' => $paginator,
			'tag'   => $tag
		]);
	}

	/**
	 * @Route("/new", name="tag_new", methods={"GET","POST"})
	 * @IsGranted("ROLE_ADMIN")
	 * @param Request             $request
	 * @param SluggerInterface    $slugger
	 *
	 * @param TranslatorInterface $translator
	 *
	 * @return Response
	 * @throws \Exception
	 */
	public function new(Request $request, SluggerInterface $slugger, TranslatorInterface $translator): Response
	{
		$tag = new Tag();
		$form = $this->createForm(TagType::class, $tag);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$tag->setSlug($slugger->slug($form->getData()->getTitle()))
				->setColor(random_int(0, count(BootstrapColorExtension::COLORS_CLASS) - 1));

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($tag);
			$entityManager->flush();

			$this->addFlash('success', $translator->trans('tag.messages.success.added', ['{name}' => $tag->getTitle()]));

			return $this->redirectToRoute('blog_index');
		}

		return $this->render('tag/new.html.twig', [
			'tag' => $tag,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{slug}/edit", name="tag_edit", methods={"GET","POST"})
	 * @IsGranted("ROLE_ADMIN")
	 * @param Request             $request
	 * @param Tag                 $tag
	 * @param SluggerInterface    $slugger
	 * @param TranslatorInterface $translator
	 *
	 * @return Response
	 * @throws \Exception
	 */
	public function edit(Request $request, Tag $tag, SluggerInterface $slugger, TranslatorInterface $translator): Response
	{
		$form = $this->createForm(TagType::class, $tag);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$tag->setSlug($slugger->slug($form->getData()->getTitle()))
				->setColor(random_int(0, count(BootstrapColorExtension::COLORS_CLASS) - 1));

			$this->getDoctrine()->getManager()->flush();

			$this->addFlash('success', $translator->trans('tag.messages.success.edit', ['{name}' => $tag->getTitle()]));

			return $this->redirectToRoute('blog_index');
		}

		return $this->render('tag/edit.html.twig', [
			'tag' => $tag,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}", name="tag_delete", methods={"GET"})
	 * @IsGranted("ROLE_ADMIN")
	 * @param Tag                 $tag
	 *
	 * @param TranslatorInterface $translator
	 *
	 * @return Response
	 */
	public function delete(Tag $tag, TranslatorInterface $translator): Response
	{
		if (!empty($tag->getPosts()))
		{
			$this->addFlash('danger', $translator->trans('tag.messages.danger.empty', ['{name}' => $tag->getTitle()]));
			return $this->redirectToRoute('tag_posts', ['slug' => $tag->getSlug()]);
		}

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($tag);
		$entityManager->flush();

		$this->addFlash('success', $translator->trans('tag.messages.success.deleted', ['{name}' => $tag->getTitle()]));

		return $this->redirectToRoute('blog_index');
	}

}
