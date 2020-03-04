<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Services\PostPagination;
use App\Services\PostPaginationSortQuery;
use App\Services\UrlRemember;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller
 * @Route("/category")
 *
 */
class CategoryController extends AbstractController
{
	/**
	 * @param CategoryRepository $categoryRepository
	 *
	 * @return Response
	 */
	public function list(CategoryRepository $categoryRepository): Response
	{
		return $this->render('default/categories-block.html.twig', [
			'categories' => $categoryRepository->findBy([], ['createdAt' => 'DESC'])
		]);
	}

	/**
	 * @Route("/{slug}/posts", name="category_posts", methods={"GET"})
	 * @param Category                $category
	 *
	 * @param Request                 $request
	 * @param PostPagination          $pagination
	 * @param PostPaginationSortQuery $paginationSortQuery
	 * @param UrlRemember             $urlRemember
	 *
	 * @return Response
	 */
	public function posts(Category $category, Request $request, PostPagination $pagination, PostPaginationSortQuery $paginationSortQuery, UrlRemember $urlRemember): Response
	{
		$urlRemember->remember();

		$paginator = $pagination->pagination($paginationSortQuery->category($category->getId()));

		if ($request->isXmlHttpRequest())
		{
			return $this->render('post/_items.html.twig', ['pagination' => $paginator]);
		}

		return $this->render('category/posts.html.twig', [
			'pagination' => $paginator,
			'category'   => $category
		]);
	}
}
