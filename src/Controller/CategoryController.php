<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
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
	 * @param Category           $category
	 *
	 * @param Request            $request
	 * @param PostRepository     $postRepository
	 * @param PaginatorInterface $paginator
	 *
	 * @return Response
	 */
	public function posts(Category $category, Request $request, PostRepository $postRepository, PaginatorInterface $paginator): Response
	{
		$pagination = $paginator->paginate(
			$postRepository->paginationWithCategory($category->getId()),
			$request->query->getInt('page', 1),
			PostController::LIMIT_PER_PAGE
		);

		return $this->render('post/index.html.twig', [
			'pagination' => $pagination,
			'category'   => $category
		]);
	}
}
