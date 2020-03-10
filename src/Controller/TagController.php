<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Services\PostPagination;
use App\Services\PostPaginationSortQuery;
use App\Services\UrlRemember;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

}
