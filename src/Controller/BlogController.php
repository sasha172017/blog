<?php


namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
	/**
	 * @Route("/", name="blog_index")
	 * @param PostRepository $postRepository
	 *
	 * @return Response
	 */
	public function index(PostRepository $postRepository): Response
	{
		return $this->render('blog/index.html.twig', [
			'posts'      => $postRepository->findBy([], ['updatedAt' => 'DESC']),
		]);
	}

}
