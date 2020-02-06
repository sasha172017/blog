<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
	 * @param Category $category
	 *
	 * @return Response
	 */
	public function posts(Category $category): Response
	{
		return $this->render('blog/index.html.twig', [
			'posts'    => $category->getPosts(),
			'category' => $category
		]);
	}
}
